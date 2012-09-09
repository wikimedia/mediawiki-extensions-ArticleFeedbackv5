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
	 * The results to return
	 *
	 * @var array
	 */
	private $results;

	/**
	 * The map of flags to permissions.
	 * If an action is not mentioned here, it is not tied to specific permissions
	 * and everyone is able to perform the action.
	 *
	 * @var array
	 */
	private $flagPermissionMap = array(
		'oversight' => 'aft-oversighter',
		'unoversight' => 'aft-oversighter',
		'decline' => 'aft-oversighter',
		'request' => 'aft-monitor',
		'unrequest' => 'aft_monitor',
		'hide' => 'aft-monitor',
		'unhide' => 'aft-monitor',
		'flag' => 'aft-reader',
		'unflag' => 'aft-reader',
		'clear-flags' => 'aft-monitor',
		'feature' => 'aft-editor',
		'unfeature' => 'aft-editor',
		'resolve' => 'aft-editor',
		'unresolve' => 'aft-editor',
		'helpful' => 'aft-reader',
		'undo-helpful' => 'aft-reader',
		'unhelpful' => 'aft-reader',
		'undo-unhelpful' => 'aft-reader',
	);

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

		$this->feedback = ArticleFeedbackv5Model::loadFromId( $feedbackId, $pageId );
		if ( !$this->feedback ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-id' );
		}
	}

	/**
	 * Run a flagging action
	 *
	 * @param  $flag      string the flag
	 * @param  $notes     string [optional] any notes to send to the activity log
	 * @param  $toggle    bool   [optional] whether to toggle the flag
	 * @return array      information about the run, containing at least the
	 *                    keys 'result' ('Error' / 'Success') and 'reason' (a
	 *                    message key)
	 */
	public function run( $flag, $notes = '', $toggle = false ) {
		wfProfileIn( __METHOD__ . "-{$flag}" );

		// check if a user is operating on his/her own feedback
		$ownFeedback = $this->user->getId() && $this->user->getId() == intval( $this->feedback->user );

		// check permissions
		if ( isset( $this->flagPermissionMap[$flag] ) ) {
			// regardless of permissions, users are always allowed to flag their own feedback
			if ( !$this->isAllowed( $this->flagPermissionMap[$flag] ) && !$ownFeedback ) {
				return $this->errorResult( 'articlefeedbackv5-invalid-feedback-flag' );
			}
		}

		// run the appropriate method
		$method = str_replace( '-', '_', $flag );
		if ( method_exists( $this, $method ) ) {
			$this->{$method}( $notes, $toggle ? true : false );
		}

		// update feedback entry
		$this->feedback->save();

		// update helpful/unhelpful display count after submission
		if ( in_array( $flag, array( 'helpful', 'undo-helpful', 'unhelpful', 'undo-unhelpful' ) ) ) {
			$percentHelpful = ApiArticleFeedbackv5Utils::percentHelpful(
				$this->feedback->helpful,
				$this->feedback->unhelpful
			);

			$this->results['helpful'] = wfMessage( 'articlefeedbackv5-form-helpful-votes-percent' )
				->params( $percentHelpful )
				->escaped();
			$this->results['helpful_counts'] = wfMessage( 'articlefeedbackv5-form-helpful-votes-count' )
				->params( $this->feedback->helpful, $this->feedback->unhelpful )
				->escaped();
		}

		$this->results['result'] = 'Success';
		$this->results['reason'] = null;

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
	 * @return ArticleFeedbackv5Flagging
	 */
	private function oversight( $notes, $toggle ) {
		// already oversighted?
		if ( $this->feedback->oversight > $this->feedback->unoversight ) {
			$this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
			__FUNCTION__,
			$this->feedback->id,
			$this->getUserId(),
			$this->getTimestamp()
		);

		// autohide if not yet hidden
		if ( $this->feedback->hide + $this->feedback->autohide <= $this->feedback->unhide ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->autohide = $this->feedback->unhide - $this->feedback->hide + 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->page, $this->feedback->id, $notes, null );

			$this->results['autohide'] = 1;
		}

		return $this;
	}

	/**
	 * Flag: un-delete
	 *
	 * Un-deleting means to remove the "oversighted" mark from a post.  It
	 * leaves the feedback hidden, even if it was auto-hidden when deleted.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unoversight( $notes, $toggle ) {
		// not yet oversighted?
		if ( $this->feedback->oversight <= $this->feedback->unoversight ) {
			$this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// un-hide if autohidden
		if ( $this->feedback->autohide ) {
			$this->feedback->autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->page, $this->feedback->id, $notes, null );
		}

		return $this;
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
	 * @return ArticleFeedbackv5Flagging
	 */
	private function hide( $notes, $toggle ) {
		// already hidden?
		if ( $this->feedback->hide + $this->feedback->autohide > $this->feedback->unhide ) {
			$this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
			__FUNCTION__,
			$this->feedback->id,
			$this->getUserId(),
			$this->getTimestamp()
		);

		return $this;
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
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unhide( $notes, $toggle ) {
		// not yet hidden?
		if ( $this->feedback->hide + $this->feedback->autohide <= $this->feedback->unhide ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// clear all abuse flags
		if ( $this->feedback->flag <= $this->feedback->unflag ) {
			$this->clear_flags( $notes, $toggle );
		}

		return $this;
	}

	/**
	 * Flag: request oversight
	 *
	 * This flag allows monitors (who can hide feedback but not delete it) to
	 * submit a post for deletion.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function request( $notes, $toggle ) {
		// already requested?
		if ( $this->feedback->request > $this->feedback->unrequest ) {
			$this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// autohide if not yet hidden
		if ( $this->feedback->hide + $this->feedback->autohide <= $this->feedback->unhide ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->autohide = $this->feedback->unhide - $this->feedback->hide + 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->page, $this->feedback->id, $notes, null );

			$this->results['autohide'] = 1;
		}

		// send an email if this is the first (new) request
		if ( $this->feedback->request > $this->feedback->request &&
			$this->feedback->request - 1 <= $this->feedback->request ) {
			$this->sendOversightEmail();
		}

		return $this;
	}

	/**
	 * Flag: un-request oversight
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unrequest( $notes, $toggle ) {
		// not yet requested?
		if ( $this->feedback->request <= $this->feedback->unrequest ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// un-hide if autohidden
		if ( $this->feedback->autohide ) {
			$this->feedback->autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->page, $this->feedback->id, $notes, null );
		}

		return $this;
	}

	/**
	 * Flag: feature feedback
	 *
	 * This flag allows monitors to highlight a particularly useful or
	 * interesting post.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function feature( $notes, $toggle ) {
		// already featured?
		if ( $this->feedback->feature > $this->feedback->unfeature ) {
			$this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// clear all abuse flags
		if ( $this->feedback->flag <= $this->feedback->unflag ) {
			$this->clear_flags( $notes, $toggle );
		}

		return $this;
	}

	/**
	 * Flag: un-feature
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unfeature( $notes, $toggle ) {
		// not yet featured?
		if ( $this->feedback->feature <= $this->feedback->unfeature ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		return $this;
	}

	/**
	 * Flag: mark feedback resolved
	 *
	 * This flag allows monitors to mark a featured post as resolved, when the
	 * suggestion has been implemented.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function resolve( $notes, $toggle ) {
		// already resolved?
		if ( $this->feedback->resolve > $this->feedback->unresolve ) {
			$this->errorResult( 'articlefeedbackv5-invalid-feedback-state' );
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		return $this;
	}

	/**
	 * Flag: un-mark a post as resolved
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unresolve( $notes, $toggle ) {
		// not yet resolved?
		if ( $this->feedback->resolve <= $this->feedback->unresolve ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		return $this;
	}

	/**
	 * Flag: decline oversight
	 *
	 * This flag allows oversighters to decline a request for oversight.  It
	 * unsets all request/unrequest on a piece of feedback.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function decline( $notes, $toggle ) {
		// not requested?
		if ( $this->feedback->request <= 0 ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// un-hide if autohidden
		if ( $this->feedback->autohide ) {
			$this->feedback->autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->page, $this->feedback->id, $notes, null );
		}

		return $this;
	}

	/**
	 * Flag: flag as abuse
	 *
	 * This flag allows readers to flag a piece of feedback as abusive.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function flag( $notes, $toggle ) {
		$this->feedback->{__FUNCTION__}++;

		$flag = $this->isSystemCall() ? 'autoflag' : __FUNCTION__;
		ArticleFeedbackv5Log::logActivity( $flag, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		global $wgArticleFeedbackv5AbusiveThreshold,
			$wgArticleFeedbackv5HideAbuseThreshold;

		// return a flag in the JSON, that turns the link red.
		$this->results['abuse_count'] = $this->feedback->flag - $this->feedback->unflag;
		$this->results['abuse_count'] = max( 0, $this->results['abuse_count'] );
		if ( $this->results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
			$this->results['abusive'] = 1;
		}
		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-count' )
			->params( $this->results['abuse_count'] )
			->escaped();

		// auto-hide after [threshold] flags
		if ( $this->results['abuse_count'] > $wgArticleFeedbackv5HideAbuseThreshold &&
			$this->feedback->hide + $this->feedback->autohide <= $this->feedback->unhide ) {
			/*
			 * We want to keep track of hides/unhides, but also autohides.
			 * Feedback will be hidden when hide + autohide > unhide
			 */
			$this->feedback->autohide = $this->feedback->unhide - $this->feedback->hide + 1;
			ArticleFeedbackv5Log::logActivity( 'autohide', $this->feedback->page, $this->feedback->id, $notes, null );

			$this->results['autohide'] = 1;
			$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
				'hide',
				$this->feedback->id,
				$this->getUserId(),
				$this->getTimestamp()
			);
		}

		return $this;
	}

	/**
	 * Flag: flag as abuse
	 *
	 * This flag allows readers to remove an abuse flag on a piece of feedback.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unflag( $notes, $toggle ) {
		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		global $wgArticleFeedbackv5AbusiveThreshold,
			   $wgArticleFeedbackv5HideAbuseThreshold;

		// return a flag in the JSON, that turns the link red.
		$this->results['abuse_count'] = $this->feedback->flag - $this->feedback->unflag;
		$this->results['abuse_count'] = max( 0, $this->results['abuse_count'] );
		if ( $this->results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
			$this->results['abusive'] = 1;
		}
		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-count' )
			->params( $this->results['abuse_count'] )
			->escaped();

		// un-hide if autohidden & we don't have [threshold] flags anymore
		if ( $this->results['abuse_count'] < $wgArticleFeedbackv5HideAbuseThreshold &&
			$this->feedback->autohide ) {
			$this->feedback->autohide = 0;
			ArticleFeedbackv5Log::logActivity( 'unhide', $this->feedback->page, $this->feedback->id, $notes, null );
		}

		return $this;
	}

	/**
	 * Flag: clear all abuse flags
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function clear_flags( $notes, $toggle ) {
		$this->feedback->unflag = $this->feedback->flag;
		ArticleFeedbackv5Log::logActivity( 'clear-flags', $this->feedback->page, $this->feedback->id, $notes, null );

		$this->results['abuse_count'] = 0;
		$this->results['abusive'] = 0;
		$this->results['abuse_cleared'] = true;
		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-cleared' )->escaped();

		return $this;
	}

	/**
	 * Flag: mark as helpful
	 *
	 * This flag allows readers to vote a piece of feedback up.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function helpful( $notes, $toggle ) {
		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// was voted unhelpful already, now voting helpful should also remove unhelpful vote
		if ( $toggle ) {
			$this->feedback->unhelpful--;
		}
		$this->results['toggle'] = $toggle;

		return $this;
	}

	/**
	 * Flag: un-mark as helpful
	 *
	 * This flag allows readers to un-vote a piece of feedback up.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function undo_helpful( $notes, $toggle ) {
		$this->feedback->helpful--;
		ArticleFeedbackv5Log::logActivity( 'undo-helpful', $this->feedback->page, $this->feedback->id, $notes, $this->user );

		return $this;
	}

	/**
	 * Flag: mark as unhelpful
	 *
	 * This flag allows readers to vote a piece of feedback down.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function unhelpful( $notes, $toggle ) {
		$this->feedback->{__FUNCTION__}++;
		ArticleFeedbackv5Log::logActivity( __FUNCTION__, $this->feedback->page, $this->feedback->id, $notes, $this->user );

		// was voted helpful already, now voting unhelpful should also remove helpful vote
		if ( $toggle ) {
			$this->feedback->helpful--;
		}
		$this->results['toggle'] = $toggle;

		return $this;
	}

	/**
	 * Flag: un-mark as unhelpful
	 *
	 * This flag allows readers to un-vote a piece of feedback down.
	 *
	 * @param  $notes     string   any notes passed in
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return ArticleFeedbackv5Flagging
	 */
	private function undo_unhelpful( $notes, $toggle ) {
		$this->feedback->unhelpful--;
		ArticleFeedbackv5Log::logActivity( 'undo-unhelpful', $this->feedback->page, $this->feedback->id, $notes, $this->user );

		return $this;
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
	 * Returns whether an action is allowed
	 *
	 * @param  $action string the name of the action
	 * @return bool whether it's allowed
	 */
	public function isAllowed( $permission ) {
		if ( $this->isSystemCall() ) {
			return true;
		}

		return $this->user->isAllowed( $permission ) && !$this->user->isBlocked();
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
		return ApiArticleFeedbackv5Utils::getUserLink( $this->getUserId() );
	}

	/**
	 * Get current timestamp
	 *
	 * @return string
	 */
	public function getTimestamp() {
		$dbr = wfGetDB( DB_SLAVE );
		return $dbr->timestamp();
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
		$page = Title::newFromID( $this->feedback->page );
		if ( !$page ) {
			return;
		}

		// make a title out of our user (sigh)
		$userPage = $wgUser->getUserPage();
		if ( !$userPage ) {
			return; // no user title object, no mail
		}

		// to build our permalink, use the feedback entry key + the page name (isn't page name a title? but title is an object? confusing)
//		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getDBKey() . '/' . $this->feedback->id );

		// @todo: these 2 lines will spoof a new url which will lead to the central feedback page with the
		// selected post on top; this is due to a couple of oversighters reporting issues with the permalink page.
		// once these issues have been solved, these lines should be removed & above line uncommented
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5' );
		$permalink = Title::makeTitle( NS_SPECIAL, $centralPageName, $this->feedback->id );

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
