<?php
/**
 * ArticleFeedbackv5Flagging class
 *
 * @package    ArticleFeedback
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

/**
 * Handles flagging of feedback
 *
 * Some weird stuff goed on here with flags. E.g.
 * * request feedback: request 0 -> 1; decline 0
 * * request declined: request 1; decline 0 -> 1
 * * requested again: request 1; decline 1 -> 0
 * * unrequested: request 1 -> 0; decline 0
 *
 * If you're expecting the data in the database to contain totals
 * for all actions executed, you're out of luck; their nothing
 * more than vague indicators that you can extract the current state
 * from, no flagging history of flagging totals. That information
 * should be sought in logging table (but there currently is no
 * functionality that requires this)
 *
 * @package    ArticleFeedback
 */
class ArticleFeedbackv5Flagging {

	/**
	 * The user performing the action
	 *
	 * Either zero for a system call, or $wgUser for a user-directed one
	 *
	 * @var mixed
	 */
	private $user;

	/**
	 * The feedback object
	 *
	 * @var ArticleFeedbackv5Model
	 */
	private $feedback;

	/**
	 * The origin of the flag
	 *
	 * @var string
	 */
	private $source = 'unknown';

	/**
	 * The results to return
	 *
	 * @var array
	 */
	private $results;

	/**
	 * Constructor
	 *
	 * @param mixed $user       the user performing the action ($wgUser), or
	 *                          zero if it's a system call
	 * @param int   $feedbackId the feedback ID
	 * @param int   $pageId     the page ID
	 */
	public function __construct( $user, $feedbackId, $pageId ) {
		$this->user = $user;
		$this->feedback = ArticleFeedbackv5Model::get( $feedbackId, $pageId );
	}

	/**
	 * Run a flagging action
	 *
	 * @param  $flag      string the flag
	 * @param  $notes     string [optional] any notes to send to the activity log
	 * @param  $toggle    bool   [optional] whether to toggle the flag
	 * @param  $source    string [optional] the origin of the flag (article, central, watchlist, permalink)
	 * @return array      information about the run, containing at least the
	 *                    keys 'result' ('Error' / 'Success') and 'reason' (a
	 *                    message key)
	 */
	public function run( $flag, $notes = '', $toggle = false, $source = 'unknown' ) {
		wfProfileIn( __METHOD__ . "-{$flag}" );

		// check if feedback record exists
		if ( !$this->feedback ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-id' );
		}

		// check permissions
		if (
			// system calls (e.g. autoabuse by AbuseFilter) are always permitted
			!$this->isSystemCall() &&
			// check permissions map
			!ArticleFeedbackv5Activity::canPerformAction( $flag, $this->user ) &&
			// users are always allowed to flag their own feedback
			!( $this->user->getId() && $this->user->getId() == intval( $this->feedback->aft_user ) )
		) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-flag' );
		}

		// determine the appropriate method for this action
		$method = str_replace( '-', '_', $flag );
		if ( !method_exists( $this, $method ) ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-flag' );
		}

		// save origin
		$this->source = $source;

		// run action-specific code
		$result = $this->{$method}( $notes, $toggle ? true : false );
		if ( $result !== true ) {
			return $result;
		}

		// update feedback entry
		$this->feedback->update();

		$this->results['result'] = 'Success';
		$this->results['reason'] = null;

		// update helpful/unhelpful display count after submission
		if ( in_array( $flag, array( 'helpful', 'undo-helpful', 'unhelpful', 'undo-unhelpful' ) ) ) {
			$percentHelpful = ArticleFeedbackv5Utils::percentHelpful(
				$this->feedback->aft_helpful,
				$this->feedback->aft_unhelpful
			);

			$this->results['helpful'] = wfMessage( 'articlefeedbackv5-form-helpful-votes-percent' )
				->params( $percentHelpful )
				->escaped();
			$this->results['helpful_counts'] = wfMessage( 'articlefeedbackv5-form-helpful-votes-count' )
				->params( $this->feedback->aft_helpful, $this->feedback->aft_unhelpful )
				->escaped();
		}

		wfProfileOut( __METHOD__ . "-{$flag}" );

		return $this->results;
	}

	/**
	 * Flag: delete
	 *
	 * Deleting means to mark as "oversighted" so it doesn't show up in most
	 * filters (i.e., any filter not prefixed with "notdeleted-").  No data is
	 * removed from the database.  Deleting a post also hides it.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function oversight( $notes, $toggle ) {
		// already oversighted?
		if ( $this->feedback->isOversighted() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_oversight = 1;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		$this->results['mask-line'] = ArticleFeedbackv5Utils::renderMaskLine(
			__FUNCTION__,
			$this->feedback->aft_id,
			$this->getUserId()
		);

		// autohide if not yet hidden
		if ( !$this->feedback->isHidden() ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->aft_hide = 1;
			$this->feedback->aft_autohide = 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic hide', $this->user, array( 'source' => $this->source ) );

			$this->results['autohide'] = 1;
		}

		return true;
	}

	/**
	 * Flag: un-delete
	 *
	 * Un-deleting means to remove the "oversighted" mark from a post.  It
	 * leaves the feedback hidden, even if it was auto-hidden when deleted.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unoversight( $notes, $toggle ) {
		// not yet oversighted?
		if ( !$this->feedback->isOversighted() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_oversight = 0;
		$this->feedback->aft_request = 0;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// un-hide if autohidden
		if ( $this->feedback->aft_hide && $this->feedback->aft_autohide ) {
			$this->feedback->aft_autohide = 0;
			$this->feedback->aft_hide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic un-hide', $this->user, array( 'source' => $this->source ) );
		}

		return true;
	}

	/**
	 * Flag: hide
	 *
	 * Hiding means to mark as "hidden" so it doesn't show up in the visible
	 * filters (i.e., any filter prefixed with "visible-").  Hidden feedback is
	 * only visible to monitors or oversighters.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function hide( $notes, $toggle ) {
		// already hidden?
		if ( $this->feedback->isHidden() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_hide = 1;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		$this->results['mask-line'] = ArticleFeedbackv5Utils::renderMaskLine(
			__FUNCTION__,
			$this->feedback->aft_id,
			$this->getUserId()
		);

		return true;
	}

	/**
	 * Flag: un-hide
	 *
	 * Un-hiding means to remove the "hidden" mark from a post so it shows up
	 * in the visible filters (i.e., any filter prefixed with "visible-")
	 * again.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unhide( $notes, $toggle ) {
		// not yet hidden?
		if ( !$this->feedback->isHidden() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_hide = 0;
		$this->feedback->aft_autohide = 0;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// clear all abuse flags
		if ( $this->feedback->aft_flag && $this->feedback->aft_autoflag ) {
			$this->clear_flags( $notes, $toggle );
		}

		return true;
	}

	/**
	 * Flag: request oversight
	 *
	 * This flag allows monitors (who can hide feedback but not delete it) to
	 * submit a post for deletion.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function request( $notes, $toggle ) {
		// already requested?
		if ( $this->feedback->isRequested() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_request = 1;
		$this->feedback->aft_decline = 0;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// autohide if not yet hidden
		if ( !$this->feedback->isHidden() ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->aft_hide = 1;
			$this->feedback->aft_autohide = 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic hide', $this->user, array( 'source' => $this->source ) );

			$this->results['autohide'] = 1;
		}

		// send an email to oversighter(s)
		$this->sendOversightEmail();

		return true;
	}

	/**
	 * Flag: un-request oversight
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unrequest( $notes, $toggle ) {
		// not yet requested?
		if ( !$this->feedback->isRequested() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_request = 0;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// un-hide if autohidden
		if ( $this->feedback->aft_hide && $this->feedback->aft_autohide ) {
			$this->feedback->aft_hide = 0;
			$this->feedback->aft_autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic un-hide', $this->user, array( 'source' => $this->source ) );
		}

		return true;
	}

	/**
	 * Flag: feature feedback
	 *
	 * This flag allows monitors to highlight a particularly useful or
	 * interesting post.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function feature( $notes, $toggle ) {
		// already featured?
		if ( $this->feedback->isFeatured() ||
			$this->feedback->isHidden() ||
			$this->feedback->isOversighted() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_feature = 1;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// clear all abuse flags
		if ( $this->feedback->aft_flag && $this->feedback->aft_autoflag ) {
			$this->clear_flags( $notes, $toggle );
		}

		return true;
	}

	/**
	 * Flag: un-feature
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unfeature( $notes, $toggle ) {
		// not yet featured?
		if ( !$this->feedback->isFeatured() ||
			$this->feedback->isHidden() ||
			$this->feedback->isOversighted() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_feature = 0;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: mark feedback resolved
	 *
	 * This flag allows monitors to mark a featured post as resolved, when the
	 * suggestion has been implemented.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function resolve( $notes, $toggle ) {
		// already resolved?
		if ( $this->feedback->isResolved() ||
			$this->feedback->isHidden() ||
			$this->feedback->isOversighted() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_resolve = 1;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: un-mark a post as resolved
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unresolve( $notes, $toggle ) {
		// not yet resolved?
		if ( !$this->feedback->isResolved() ||
			$this->feedback->isHidden() ||
			$this->feedback->isOversighted() ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_resolve = 0;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: decline oversight
	 *
	 * This flag allows oversighters to decline a request for oversight.  It
	 * unsets all request/unrequest on a piece of feedback.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function decline( $notes, $toggle ) {
		// not requested?
		if ( $this->feedback->aft_request <= 0 ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->aft_decline = 1;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// un-hide if autohidden
		if ( $this->feedback->aft_hide && $this->feedback->aft_autohide ) {
			$this->feedback->aft_hide = 0;
			$this->feedback->aft_autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic un-hide', $this->user, array( 'source' => $this->source ) );
		}

		return true;
	}

	/**
	 * Flag: flag as abuse
	 *
	 * This flag allows readers to flag a piece of feedback as abusive.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function flag( $notes, $toggle ) {
		$flag = $this->isSystemCall() ? 'autoflag' : 'flag';
		$this->feedback->{"aft_$flag"}++;
		ArticleFeedbackv5Log::logActivity( $flag, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->isSystemCall() ? null : $this->user, array( 'source' => $this->source ) );

		global $wgArticleFeedbackv5AbusiveThreshold,
			$wgArticleFeedbackv5HideAbuseThreshold;

		// return a flag in the JSON, that turns the link red.
		$this->results['abuse_count'] = $this->feedback->aft_flag + $this->feedback->aft_autoflag;
		$this->results['abuse_count'] = max( 0, $this->results['abuse_count'] );
		if ( $this->results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
			$this->results['abusive'] = 1;
		}
		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-count' )
			->params( $this->results['abuse_count'] )
			->escaped();

		// auto-hide after [threshold] flags
		if ( $this->results['abuse_count'] > $wgArticleFeedbackv5HideAbuseThreshold &&
			!$this->feedback->isHidden() ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->aft_hide = 1;
			$this->feedback->aft_autohide = 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic hide', $this->user, array( 'source' => $this->source ) );

			$this->results['autohide'] = 1;
			$this->results['mask-line'] = ArticleFeedbackv5Utils::renderMaskLine(
				'hide',
				$this->feedback->aft_id,
				$this->getUserId()
			);
		}

		return true;
	}

	/**
	 * Flag: flag as abuse
	 *
	 * This flag allows readers to remove an abuse flag on a piece of feedback.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unflag( $notes, $toggle ) {
		if ( $this->feedback->aft_flag <= 0 ) {
			$this->feedback->aft_autoflag = 0;
		} else {
			$this->feedback->aft_flag--;
		}
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		global $wgArticleFeedbackv5AbusiveThreshold,
			   $wgArticleFeedbackv5HideAbuseThreshold;

		// return a flag in the JSON, that turns the link red.
		$this->results['abuse_count'] = $this->feedback->aft_flag + $this->feedback->aft_autoflag;
		$this->results['abuse_count'] = max( 0, $this->results['abuse_count'] );
		if ( $this->results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
			$this->results['abusive'] = 1;
		}
		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-count' )
			->params( $this->results['abuse_count'] )
			->escaped();

		// un-hide if autohidden & we don't have [threshold] flags anymore
		if ( $this->results['abuse_count'] < $wgArticleFeedbackv5HideAbuseThreshold &&
			$this->feedback->aft_autohide ) {
			$this->feedback->aft_autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic un-hide', $this->user, array( 'source' => $this->source ) );
		}

		return true;
	}

	/**
	 * Flag: clear all abuse flags
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function clear_flags( $notes, $toggle ) {
		$this->feedback->aft_autoflag = 0;
		$this->feedback->aft_flag = 0;
		ArticleFeedbackv5Log::logActivity( 'clear-flags', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatically clearing all flags', $this->user, array( 'source' => $this->source ) );

		$this->results['abuse_count'] = 0;
		$this->results['abusive'] = 0;

		$this->results['abuse_cleared'] = true;
		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-cleared' )->escaped();

		return true;
	}

	/**
	 * Flag: mark as helpful
	 *
	 * This flag allows readers to vote a piece of feedback up.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function helpful( $notes, $toggle ) {
		$this->feedback->aft_helpful++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// was voted unhelpful already, now voting helpful should also remove unhelpful vote
		if ( $toggle ) {
			$this->feedback->aft_unhelpful--;
		}
		$this->results['toggle'] = $toggle;

		return true;
	}

	/**
	 * Flag: un-mark as helpful
	 *
	 * This flag allows readers to un-vote a piece of feedback up.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function undo_helpful( $notes, $toggle ) {
		$this->feedback->aft_helpful--;
		ArticleFeedbackv5Log::logActivity( 'undo-helpful', $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: mark as unhelpful
	 *
	 * This flag allows readers to vote a piece of feedback down.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unhelpful( $notes, $toggle ) {
		$this->feedback->aft_unhelpful++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// was voted helpful already, now voting unhelpful should also remove helpful vote
		if ( $toggle ) {
			$this->feedback->aft_helpful--;
		}
		$this->results['toggle'] = $toggle;

		return true;
	}

	/**
	 * Flag: un-mark as unhelpful
	 *
	 * This flag allows readers to un-vote a piece of feedback down.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function undo_unhelpful( $notes, $toggle ) {
		$this->feedback->aft_unhelpful--;
		ArticleFeedbackv5Log::logActivity( 'undo-unhelpful', $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Returns whether this is a system call rather than a user-directed one
	 *
	 * @return bool
	 */
	public function isSystemCall() {
		return $this->user === 0;
	}

	/**
	 * Gets the user id
	 *
	 * @return mixed the user's ID, or zero if it's a system call
	 */
	public function getUserId() {
		return $this->isSystemCall() ? null : $this->user->getId();
	}

	/**
	 * Gets the user link, for use in displays
	 *
	 * @return string the link
	 */
	public function getUserLink() {
		return ArticleFeedbackv5Utils::getUserLink( $this->getUserId() );
	}

	/**
	 * Helper function to dig out page url and title, feedback permalink, and
	 * requestor page url and name - if all this data can be retrieved properly
	 * it shoves an email job into the queue for sending to the oversighters'
	 * mailing list - only called for NEW oversight requests
	 *
	 * @return ArticleFeedbackv5Flagging
	 */
	protected function sendOversightEmail() {
		global $wgUser;

		// jobs need a title object
		$page = Title::newFromID( $this->feedback->aft_page );
		if ( !$page ) {
			return;
		}

		// make a title out of our user (sigh)
		$userPage = $wgUser->getUserPage();
		if ( !$userPage ) {
			return; // no user title object, no mail
		}

		// to build our permalink, use the feedback entry key + the page name (isn't page name a title? but title is an object? confusing)
//		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getDBKey() . '/' . $this->feedback->aft_id );

		// @todo: these 2 lines will spoof a new url which will lead to the central feedback page with the
		// selected post on top; this is due to a couple of oversighters reporting issues with the permalink page.
		// once these issues have been solved, these lines should be removed & above line uncommented
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5' );
		$permalink = Title::makeTitle( NS_SPECIAL, $centralPageName, $this->feedback->aft_id );

		// build our params
		$params = array(
			'user_name' => $wgUser->getName(),
			'user_url' => $userPage->getCanonicalUrl(),
			'page_name' => $page->getPrefixedText(),
			'page_url' => $page->getCanonicalUrl(),
			'permalink' => $permalink->getCanonicalUrl()
		);

		$job = new ArticleFeedbackv5MailerJob( $page, $params );
		$job->insert();

		return $this;
	}

	/**
	 * Builds an error result
	 *
	 * Retains anything added to the result before the error.
	 *
	 * @param  $message string the error message key
	 * @return array    the result
	 */
	public function errorResult( $message ) {
		$this->results['result'] = 'Error';
		$this->results['reason'] = $message;
		return $this->results;
	}
}
