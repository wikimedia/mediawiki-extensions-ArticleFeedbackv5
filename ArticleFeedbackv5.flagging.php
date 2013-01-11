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
	 * Last error message, after error has occurred
	 *
	 * @var string
	 */
	private $error = '';

	/**
	 * The id of the inserted log entry
	 *
	 * @var int
	 */
	private $logId = null;

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
	 * @return bool       true upon successful flagging, false on failure. In the event of a failure,
	 *                    the error can be fetched through ->getError())
	 */
	public function run( $flag, $notes = '', $toggle = false, $source = 'unknown' ) {
		wfProfileIn( __METHOD__ . "-{$flag}" );

		// check if feedback record exists
		if ( !$this->feedback ) {
			$this->error = 'articlefeedbackv5-invalid-feedback-id';
			return false;
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
			$this->error = 'articlefeedbackv5-invalid-feedback-flag';
			return false;
		}

		// determine the appropriate method for this action
		$method = str_replace( '-', '_', $flag );
		if ( !method_exists( $this, $method ) ) {
			$this->error = 'articlefeedbackv5-invalid-feedback-flag';
			return false;
		}

		// save origin
		$this->source = $source;

		/*
		 * The method corresponding to the requested "flag" will be called;
		 * these methods then each will perform the particular changes that
		 * an individual flag entails.
		 * The method is - at least - supposed to make the required adjustments
		 * to $this->feedback and add the id of the log entry for the requested
		 * flag to $this->logId.
		 * Some additional stuff may be done as well (e.g. certain flags result
		 * in follow-up automated flags), but these should be done "under the
		 * radar"; no logId is required for these automated actions.
		 */
		$result = $this->{$method}( $notes, $toggle ? true : false );
		if ( !$result ) {
			return false;
		}

		if ( !is_int( $this->logId ) ) {
			$this->error = 'articlefeedbackv5-invalid-log-id';
			return false;
		}

		// update feedback entry for real
		$this->feedback->update();

		wfProfileOut( __METHOD__ . "-{$flag}" );

		return true;
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
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_oversight = 1;
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// autohide if not yet hidden
		if ( !$this->feedback->isHidden() ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->aft_hide = 1;
			$this->feedback->aft_autohide = 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic hide', $this->user, array( 'source' => $this->source ) );
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
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_oversight = 0;
		$this->feedback->aft_request = 0;
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// un-hide if autohidden
		if ( $this->feedback->aft_hide && $this->feedback->aft_autohide ) {
			$this->feedback->aft_autohide = 0;
			$this->feedback->aft_hide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic un-hide', $this->user, array( 'source' => $this->source ) );
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
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_request = 1;
		$this->feedback->aft_decline = 0;
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// autohide if not yet hidden
		if ( !$this->feedback->isHidden() ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->aft_hide = 1;
			$this->feedback->aft_autohide = 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic hide', $this->user, array( 'source' => $this->source ) );
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
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_request = 0;
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// un-hide if autohidden
		if ( $this->feedback->aft_hide && $this->feedback->aft_autohide ) {
			$this->feedback->aft_hide = 0;
			$this->feedback->aft_autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic un-hide', $this->user, array( 'source' => $this->source ) );
		}

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
		if ( !$this->feedback->isRequested() ) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_decline = 1;
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

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
		if ( $this->feedback->isFeatured() ||
			$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_feature = 1;
//		$this->feedback->aft_resolve = 0; // don't touch resolved flag
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

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
		if ( !$this->feedback->isFeatured() ||
			$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_feature = 0;
//		$this->feedback->aft_resolve = 0; // don't touch resolved flag
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: mark feedback resolved
	 *
	 * This flag allows monitors to mark a post as resolved, when the
	 * suggestion has been implemented.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function resolve( $notes, $toggle ) {
		if ( $this->feedback->isResolved() ||
			$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

//		$this->feedback->aft_feature = 0; // don't touch featured flag
		$this->feedback->aft_resolve = 1;
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

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
		if ( !$this->feedback->isResolved() ||
			$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

//		$this->feedback->aft_feature = 0; // don't touch featured flag
		$this->feedback->aft_resolve = 0;
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: mark feedback as not actionable
	 *
	 * This flag allows monitors to mark a post as not actionable.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function noaction( $notes, $toggle ) {
		if ( $this->feedback->isFeatured() ||
			$this->feedback->isResolved() ||
			$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_feature = 0;
		$this->feedback->aft_resolve = 0;
		$this->feedback->aft_noaction = 1;
		$this->feedback->aft_hide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: un-mark a post as not actionable
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unnoaction( $notes, $toggle ) {
		if ( $this->feedback->isFeatured() ||
			$this->feedback->isResolved() ||
			!$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_feature = 0;
		$this->feedback->aft_resolve = 0;
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: hide
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function hide( $notes, $toggle ) {
		if ( $this->feedback->isFeatured() ||
			$this->feedback->isResolved() ||
			$this->feedback->isNonActionable() ||
			$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_feature = 0;
		$this->feedback->aft_resolve = 0;
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 1;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		return true;
	}

	/**
	 * Flag: un-hide
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return array|bool
	 */
	private function unhide( $notes, $toggle ) {
		if ( $this->feedback->isFeatured() ||
			$this->feedback->isResolved() ||
			$this->feedback->isNonActionable() ||
			!$this->feedback->isHidden()
		) {
			$this->error = 'articlefeedbackv5-invalid-feedback-state';
			return false;
		}

		$this->feedback->aft_feature = 0;
		$this->feedback->aft_resolve = 0;
		$this->feedback->aft_noaction = 0;
		$this->feedback->aft_hide = 0;

		$this->feedback->aft_autohide = 0;

		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// clear all abuse flags
		if ( $this->feedback->aft_flag && $this->feedback->aft_autoflag ) {
			$this->clear_flags( $notes, $toggle );
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
		$this->logId = ArticleFeedbackv5Log::logActivity( $flag, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->isSystemCall() ? null : $this->user, array( 'source' => $this->source ) );

		global $wgArticleFeedbackv5HideAbuseThreshold;

		// auto-hide after [threshold] flags
		if ( $this->feedback->aft_flag + $this->feedback->aft_autoflag > $wgArticleFeedbackv5HideAbuseThreshold &&
			!$this->feedback->isHidden() ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->aft_hide = 1;
			$this->feedback->aft_autohide = 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatic hide', $this->user, array( 'source' => $this->source ) );
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
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		global $wgArticleFeedbackv5HideAbuseThreshold;

		// un-hide if autohidden & we don't have [threshold] flags anymore
		if ( $this->feedback->aft_flag + $this->feedback->aft_autoflag < $wgArticleFeedbackv5HideAbuseThreshold &&
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

		/*
		 * Note: this one does not save logId because (currently) it will never
		 * be called directly, but only as an automated result after certain flags.
		 */
		ArticleFeedbackv5Log::logActivity( 'clear-flags', $this->feedback->aft_page, $this->feedback->aft_id, 'Automatically clearing all flags', $this->user, array( 'source' => $this->source ) );

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
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// was voted unhelpful already, now voting helpful should also remove unhelpful vote
		if ( $toggle ) {
			$this->feedback->aft_unhelpful--;
		}

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
		$this->logId = ArticleFeedbackv5Log::logActivity( 'undo-helpful', $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

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
		$this->logId = ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

		// was voted helpful already, now voting unhelpful should also remove helpful vote
		if ( $toggle ) {
			$this->feedback->aft_helpful--;
		}

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
		$this->logId = ArticleFeedbackv5Log::logActivity( 'undo-unhelpful', $this->feedback->aft_page, $this->feedback->aft_id, $notes, $this->user, array( 'source' => $this->source ) );

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
	 * Helper function to dig out page url and title, feedback permalink, and
	 * requestor page url and name - if all this data can be retrieved properly
	 * it shoves an email job into the queue for sending to the oversighters'
	 * mailing list - only called for NEW oversight requests
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
		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getDBKey() . '/' . $this->feedback->aft_id );

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
	}

	/**
	 * Return the error message (if any)
	 *
	 * @return string the message
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Return the id of the requested flag's log entry
	 *
	 * @return int the id
	 */
	public function getLogId() {
		return $this->logId;
	}
}
