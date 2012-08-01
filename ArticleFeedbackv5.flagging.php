<?php
/**
 * ArticleFeedbackv5Flagging class
 *
 * @package    ArticleFeedback
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * Handles flagging of feedback
 *
 * Known flags are: 'delete', 'hide', 'resetoversight', 'abuse', 'oversight',
 * 'unhelpful', and 'helpful'
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
	 * The page ID
	 *
	 * @var int
	 */
	private $pageId;

	/**
	 * The feedback ID
	 *
	 * @var int
	 */
	private $feedbackId;

	/**
	 * The filters to be changed
	 *
	 * @var array
	 */
	private $filters;

	/**
	 * The updates to be run
	 *
	 * @var array
	 */
	private $updates;

	/**
	 * The results to return
	 *
	 * @var array
	 */
	private $results;

	/**
	 * The log lines to add
	 *
	 * @var array
	 */
	private $log;

	/**
	 * The adjustments to be made to the relevance score
	 *
	 * @var array
	 */
	private $relevance;

	/**
	 * The helpful count
	 *
	 * @var int
	 */
	private $helpfulCount;

	/**
	 * The unhelpful count
	 *
	 * @var int
	 */
	private $unhelpfulCount;

	/**
	 * The helpful count minus the unhelpful count
	 *
	 * @var int
	 */
	private $netHelpfulness;

	/**
	 * The map of flags to permissions.
	 * If an action is not mentionned here, it is not tied to specific permissions
	 * and everyone is able to perform the action.
	 *
	 * @var array
	 */
	private $flagPermissionMap = array(
		'delete'         => array( 'aftv5-delete-feedback' ),
		'hide'           => array( 'aftv5-hide-feedback' ),
		'oversight'      => array( 'aftv5-hide-feedback' ),
		'feature'        => array( 'aftv5-feature-feedback' ),
		'resolve'        => array( 'aftv5-feature-feedback' ),
		'resetoversight' => array( 'aftv5-delete-feedback' ),
	);

	/**
	 * Constructor
	 *
	 * @param mixed $user       the user performing the action ($wgUser), or
	 *                          zero if it's a system call
	 * @param int   $pageId     the page ID
	 * @param int   $feedbackId the feedback ID
	 */
	public function __construct( $user, $pageId, $feedbackId ) {
		$this->user       = $user;
		$this->pageId     = $pageId;
		$this->feedbackId = $feedbackId;
	}

	/**
	 * Run a flagging action
	 *
	 * @param  $flag      string the flag
	 * @param  $notes     string [optional] any notes to send to the activity log
	 * @param  $direction string [optional] the direction of the request ('increase' / 'decrease')
	 * @param  $toggle    bool   [optional] whether to toggle the flag
	 * @return array      information about the run, containing at least the
	 *                    keys 'result' ('Error' / 'Success') and 'reason' (a
	 *                    message key)
	 */
	public function run( $flag, $notes = '', $direction = 'increase', $toggle = false ) {
		$flag       = $flag;
		$notes      = $notes;
		$direction  = $direction == 'increase' ? 'increase' : 'decrease';
		$toggle     = $toggle ? true : false;

		// default values for information to be filled in
		$this->filters = array();
		$this->update  = array();
		$this->results = array();
		$this->log     = array();
		$this->relevance = array();

		// start
		$where = array( 'af_id' => $this->feedbackId );

		// we use ONE db connection that talks to master
		$dbw     = wfGetDB( DB_MASTER );
		$dbw->begin();
		$timestamp = $dbw->timestamp();

		// load feedback record, bail if we don't have one
		$record = $this->fetchRecord( $dbw, $this->feedbackId );

		// if there's no record, this is already broken
		if ( $record === false || !$record->af_id ) {
			return $this->errorResult( 'articlefeedbackv5-invalid-feedback-id' );
		}

		// check permissions
		if ( isset( $this->flagPermissionMap[$flag] ) ) {
			foreach ( $this->flagPermissionMap[$flag] as $permission ) {
				if ( !$this->isAllowed( $permission ) ) {
					return $this->errorResult( 'articlefeedbackv5-invalid-feedback-flag' );
				}
			}
		}

		// run the appropriate method
		if ( method_exists( $this, 'flag_' . $flag . '_' . $direction ) ) {
			$res = call_user_func_array(
				array( $this, 'flag_' . $flag . '_' . $direction ),
				array( $record, $notes, $timestamp, $toggle )
			);
		} elseif ( method_exists( $this, 'flag_' . $flag ) ) {
			$res = call_user_func_array(
				array( $this, 'flag_' . $flag ),
				array( $record, $notes, $timestamp, $toggle, $direction )
			);
		} else {
			$res = 'articlefeedbackv5-invalid-feedback-flag';
		}
		if ( $res !== true ) {
			return $this->errorResult( $res );
		}

		wfProfileIn( __METHOD__ . "-flag_{$flag}_$direction" );

		// figure out if we have relevance_scores to adjust
		if ( count($this->relevance) > 0 ) {
			global $wgArticleFeedbackv5RelevanceScoring;
			$math = array();

			foreach( $this->relevance as $item ) {
				if ( array_key_exists( $item, $wgArticleFeedbackv5RelevanceScoring ) ) {
					$math[] = $wgArticleFeedbackv5RelevanceScoring[$item];
				}
			}

			$this->update[] = 'af_relevance_score = af_relevance_score + (' . implode (' + ', $math) . ')';
			$this->update[] = 'af_relevance_sort = - af_relevance_score';
		}

		// note: af_activity_count & af_suppress_count need to be updated as well
		// ApiArticleFeedbackv5Utils::logActivity takes care of that

		// we were valid
		$success = $dbw->update(
			'aft_article_feedback',
			$this->update,
			$where,
			__METHOD__
		);

		// if we've changed helpfulness, featured, or relevance_score we adjust the relevance filter
		if ( count($this->relevance) > 0 || $flag == 'helpful' || $flag == 'unhelpful' || $flag == 'feature' ) {
			global $wgArticleFeedbackv5Cutoff;

			// we might have changed the featured status or helpfulness score here
			if ( !isset( $this->netHelpfulness ) ) {
				$this->netHelpfulness = $record->af_net_helpfulness;
			}
			if ( !isset( $is_featured ) ) {
				$is_featured = $record->af_is_featured;
			}
			// grab our shiny new relevance score
			$new_score = $dbw->selectField( 'aft_article_feedback', 'af_relevance_score', $where, __METHOD__ );

			// we add 1 if

			// 1. we're newly featured, don't have a comment, and nethelpfulness is not at threshold and greater then cutoff
			if ( !$record->af_has_comment && $this->netHelpfulness <= 0
				&& !$record->af_is_featured && $is_featured
				&& $new_score > $wgArticleFeedbackv5Cutoff ) {
				$this->filters['visible-relevant'] = 1;
			// 2. we're newly net_helpful, don't have a comment, and aren't featured and greater then cutoff
			} elseif ( !$record->af_has_comment && !$is_featured
				  && $record->af_net_helpfulness <= 0 && $this->netHelpfulness > 0
				  && $new_score > $wgArticleFeedbackv5Cutoff ) {
				$this->filters['visible-relevant'] = 1;
			// 3. we're newly above the cutoff and qualify as relevant in any way
			} elseif ( ( $record->af_has_comment || $this->netHelpfulness > 0 || $is_featured )
				&& $new_score > $wgArticleFeedbackv5Cutoff
				&& $record->af_relevance_score <= $wgArticleFeedbackv5Cutoff ) {
				$this->filters['visible-relevant'] = 1;

			// we subtract 1 if

			// we used to be in the relevant filter but are now below the cutoff
			} elseif ( ( $record->af_has_comment || $record->af_new_helpfulness > 0 || $record->af_is_featured )
				  && $new_score <= $wgArticleFeedbackv5Cutoff
				  && $record->af_relevance_score > $wgArticleFeedbackv5Cutoff ) {
				$this->filters['visible-relevant'] = -1;
			// we used to be in the relevant filter via featured or helpfulness and are now not
			} elseif ( !$record->af_has_comment
				  && ( ( $record->af_new_helpfulness > 0 && $this->netHelpfulness <= 0 )
					|| ( $record->af_is_featured && !$is_featured ) ) ) {
					$this->filters['visible-relevant'] = -1;
			}
		}

		// Update the filter count rollups.
		ApiArticleFeedbackv5Utils::updateFilterCounts( $dbw, $record->af_page_id, $this->filters );

		// Update helpful/unhelpful display count after submission but BEFORE db commit to stay in the transaction
		if ( $flag == 'helpful' || $flag == 'unhelpful' ) {

			// no negative numbers please
			$helpful = max( 0, $this->helpfulCount );
			$unhelpful = max( 0, $this->unhelpfulCount );

			$this->results['helpful'] = wfMessage( 'articlefeedbackv5-form-helpful-votes' )
				->rawParams( wfMessage( 'percent',
						ApiArticleFeedbackv5Utils::percentHelpful( $this->helpfulCount, $this->unhelpfulCount )
					)->text() )
				->escaped();
			$this->results['helpful_counts'] = wfMessage( 'articlefeedbackv5-form-helpful-votes-count' )
				->params( $this->helpfulCount, $this->unhelpfulCount )
				->escaped();

			// Update net_helpfulness after flagging as helpful/unhelpful.
			$dbw->update(
				'aft_article_feedback',
				array( 'af_net_helpfulness = CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED)' ),
				array(
					'af_id' => $this->feedbackId,
				),
				__METHOD__
			);
		}

		$dbw->commit(); // everything went well, so we commit our db changes

		// activity logging
		foreach( $this->log as $entry ) {
			ApiArticleFeedbackv5Utils::logActivity( $entry[0], $record->af_page_id, $this->feedbackId, $entry[1], $entry[2], array( $this->feedbackId ) );
		}

		$this->results['result'] = 'Success';
		$this->results['reason'] = null;

		wfProfileOut( __METHOD__ . "-flag_{$flag}_$direction" );

		return $this->results;
	}

	/**
	 * Flag: delete
	 *
	 * Deleting means to mark as "oversighted" so it doesn't show up in most
	 * filters (i.e., any filter not prefixed with "notdeleted-").  No data is
	 * removed from the database.  Deleting a post also hides it.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_delete_increase( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( $record->af_is_deleted ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->update['af_is_deleted'] = true;
		$this->update['af_is_undeleted'] = false;

		$this->log[] = array('oversight', $notes, $this->isSystemCall());

		// always increment oversighted count and decrement notdeleted
		$this->filters = array('all-oversighted' => 1, 'notdeleted' => -1);

		// if this was previously visible, adjust counts
		$this->visibleCounts( $record, 'invisible' );

		// adjust notdeleted counts
		if ( $record->af_is_hidden ) {
			$this->filters['notdeleted-hidden'] = -1;
		}
		if ( $record->af_is_unhidden ) {
			$this->filters['notdeleted-unhidden'] = -1;
		}
		if ( $record->af_oversight_count > 0 ) {
			$this->filters['notdeleted-requested'] = -1;
		}
		if ( $record->af_is_unrequested ) {
			$this->filters['notdeleted-unrequested'] = -1;
		}
		if ( $record->af_is_declined ) {
			$this->filters['notdeleted-declined'] = -1;
		}

		// adjust undeleted count if necessary
		if ( $record->af_is_undeleted ) {
			$this->filters['all-unoversighted'] = -1;
		}

		// autohide if not hidden
		if ( false == $record->af_is_hidden ) {
			// we must tell the helper method this record is deleted for filter purposes
			$record->af_is_deleted = true;

			$this->update['af_is_hidden'] = true;
			$this->update['af_is_unhidden'] = false;
			$this->update['af_is_autohide'] = true;

			$this->update['af_last_status'] = 'autohide';
			$this->update['af_last_status_user_id'] = $this->getUserId();
			$this->update['af_last_status_timestamp'] = $timestamp;
			if ( $notes ) {
				$this->update['af_last_status_notes'] = $notes;
			}

			$this->log[] = array( 'autohide', '', $this->getUserId() );

			$this->results['autohidden'] = 1;
			$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
				'autohide', $this->getUserId(), $timestamp );
			$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
				'hidden', $record->af_id, $this->getUserId(), $timestamp );

			// NOTE: we've already adjusted all visiblity counts above so we only do hide specific ones
			$this->hideCounts( $record, 'hide' );

		} else {

			$this->update['af_last_status'] = 'deleted';
			$this->update['af_last_status_user_id'] = $this->getUserId();
			$this->update['af_last_status_timestamp'] = $timestamp;
			if ( $notes ) {
				$this->update['af_last_status_notes'] = $notes;
			}

			$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
				'deleted', $this->getUserId(), $timestamp );
			$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
				'oversight', $record->af_id, $this->getUserId(), $timestamp );
		}
		return true;
	}

	/**
	 * Flag: un-delete
	 *
	 * Un-deleting means to remove the "oversighted" mark from a post.  It
	 * leaves the feedback hidden, even if it was auto-hidden when deleted.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_delete_decrease( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( !$record->af_is_deleted ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}

		$this->update['af_is_deleted'] = false;
		$this->update['af_is_undeleted'] = true;

		$this->update['af_last_status'] = 'undeleted';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		$this->log[] = array('unoversight', $notes, $this->isSystemCall());
		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'undeleted', $this->getUserId(), $timestamp );

		$this->filters = array('all-unoversighted' => 1,
				 'all-oversighted' => -1);

		$this->visibleCounts( $record, 'visible' );

		// adjust notdeleted counts
		if ( $record->af_is_hidden ) {
			$this->filters['notdeleted-hidden'] = 1;
		}
		if ( $record->af_is_unhidden ) {
			$this->filters['notdeleted-unhidden'] = 1;
		}
		if ( $record->af_oversight_count > 0 ) {
			$this->filters['notdeleted-requested'] = 1;
		}
		if ( $record->af_is_unrequested ) {
			$this->filters['notdeleted-unrequested'] = 1;
		}
		if ( $record->af_is_declined ) {
			$this->filters['notdeleted-declined'] = 1;
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
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_hide_increase( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( $record->af_is_hidden ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}
		$this->update['af_is_hidden'] = true;
		$this->update['af_is_unhidden'] = false;

		$this->update['af_last_status'] = 'hidden';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		$this->filters = array();

		$this->hideCounts( $record, 'hide' );
		$this->visibleCounts( $record, 'invisible' );

		$this->log[] = array('hidden', $notes, $this->isSystemCall());
		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'hidden', $this->getUserId(), $timestamp );
		$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
			'hidden', $record->af_id, $this->getUserId(), $timestamp );
		return true;
	}

	/**
	 * Flag: un-hide
	 *
	 * Un-hiding means to remove the "hidden" mark from a post so it shows up
	 * in the visible filters (i.e., any filter prefixed with "visible-")
	 * again.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_hide_decrease( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( !$record->af_is_hidden ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}
		$this->update['af_is_hidden'] = false;
		$this->update['af_is_unhidden'] = true;

		$this->update['af_last_status'] = 'unhidden';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		$this->filters = array();

		$this->hideCounts( $record, 'show' );
		$this->visibleCounts( $record, 'visible' );

		$this->log[] = array('unhidden', $notes, $this->isSystemCall());
		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'unhidden', $this->getUserId(), $timestamp );
		return true;
	}

	/**
	 * Flag: request oversight
	 *
	 * This flag allows monitors (who can hide feedback but not delete it) to
	 * submit a post for deletion.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_oversight_increase( stdClass $record, $notes, $timestamp, $toggle ) {
		$this->log[] = array('request', $notes, $this->isSystemCall());

		// NOTE: we are bypassing traditional sql escaping here
		$this->update[] = "af_oversight_count = af_oversight_count + 1";

		// we do not increment anything by default
		$this->filters = array();

		// if this is a NEW request, increment our counters
		if ( $record->af_oversight_count < 1 ) {
			$this->filters['all-requested'] = 1;
			if ( $record->af_is_deleted == false ) {
				$this->filters['notdeleted-requested'] = 1;
			}
		}

		// turn off unrequested if necessary
		if ( $record->af_is_unrequested ) {
			$this->filters['all-unrequested'] = -1;
			$this->update['af_is_unrequested'] = false;
			if ( $record->af_is_deleted == false ) {
				$this->filters['notdeleted-unrequested'] = -1;
			}
		}

		if ( false == $record->af_is_hidden ) {

			$this->update['af_is_hidden'] = true;
			$this->update['af_is_unhidden'] = false;
			$this->update['af_is_autohide'] = true;

			$this->update['af_last_status'] = 'autohide';
			$this->update['af_last_status_user_id'] = $this->getUserId();
			$this->update['af_last_status_timestamp'] = $timestamp;
			if ( $notes ) {
				$this->update['af_last_status_notes'] = $notes;
			}

			$this->log[] = array( 'autohide', '', $this->getUserId() );

			$this->results['autohidden'] = 1;
			$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
				'hidden', $this->getUserId(), $timestamp );
			$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
				'hidden', $record->af_id, $this->getUserId(), $timestamp );

			$this->hideCounts( $record, 'hide' );
			// NOTE: unlike autohide after oversight, we must do the visiblity filter
			$this->visibleCounts( $record, 'invisible' );

		} else {
			$this->update['af_last_status'] = 'request';
			$this->update['af_last_status_user_id'] = $this->getUserId();
			$this->update['af_last_status_timestamp'] = $timestamp;
			if ( $notes ) {
				$this->update['af_last_status_notes'] = $notes;
			}
			$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
				'request', $this->getUserId(), $timestamp );
		}

		// IF the previous setting was 0, send an email
		if ( $record->af_oversight_count < 1 ) {
			$this->sendOversightEmail( $record );
		}
		return true;
	}

	/**
	 * Flag: un-request oversight
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_oversight_decrease( stdClass $record, $notes, $timestamp, $toggle ) {
		$this->log[] = array('unrequest', $notes, $this->isSystemCall());
		$this->filters = array();

		if( ( $record->af_oversight_count - 1 ) < 1) {
			$this->update['af_is_unrequested'] = true;
			$this->filters['all-unrequested'] = 1;
			$this->filters['all-requested'] = -1;
			if ( $record->af_is_deleted == false ) {
				$this->filters['notdeleted-unrequested'] = 1;
				$this->filters['notdeleted-requested'] = -1;
			}
		}

		// NOTE: we are bypassing traditional sql escaping here
		$this->update[] = "af_oversight_count = GREATEST(CONVERT(af_oversight_count, SIGNED) - 1, 0)";
		$this->update['af_last_status'] = 'unrequest';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}
		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'unrequest', $this->getUserId(), $timestamp );
		return true;
	}

	/**
	 * Flag: feature feedback
	 *
	 * This flag allows monitors to highlight a particularly useful or
	 * interesting post.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_feature_increase( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( $record->af_is_featured ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}
		$this->log[] = array('feature', $notes, $this->isSystemCall());

		$this->update['af_is_featured'] = true;
		$this->update['af_is_unfeatured'] = false;
		$this->update['af_last_status'] = 'featured';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		// filter adjustments
		$this->filters['visible-featured'] = 1;
		if ( true == $record->af_is_unfeatured) {
			$this->filters['visible-unfeatured'] = -1;
		}
		$this->relevance[] = 'featured';

		$is_featured = true;

		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'featured', $this->getUserId(), $timestamp );

		return true;
	}

	/**
	 * Flag: un-feature
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_feature_decrease( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( !$record->af_is_featured ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}
		$this->log[] = array('unfeature', $notes, $this->isSystemCall());

		$this->update['af_is_featured'] = false;
		$this->update['af_is_unfeatured'] = true;
		$this->update['af_last_status'] = 'unfeatured';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}
		// filter adjustments
		$this->filters['visible-featured'] = -1;
		$this->filters['visible-unfeatured'] = 1;
		$this->relevance[] = 'unfeatured';

		$is_featured = false;

		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'unfeatured', $this->getUserId(), $timestamp );

		return true;
	}

	/**
	 * Flag: mark feedback resolved
	 *
	 * This flag allows monitors to mark a featured post as resolved, when the
	 * suggestion has been implemented.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_resolve_increase( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( $record->af_is_resolved ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}
		$this->log[] = array('resolve', $notes, $this->isSystemCall());

		$this->update['af_is_resolved'] = true;
		$this->update['af_is_unresolved'] = false;
		$this->update['af_last_status'] = 'resolved';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		// filter adjustments
		$this->filters['visible-resolved'] = 1;
		if ( true == $record->af_is_unresolved) {
			$this->filters['visible-unresolved'] = -1;
		}
		$this->relevance[] = 'resolved';

		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'resolved', $this->getUserId(), $timestamp );

		return true;
	}

	/**
	 * Flag: un-mark a post as resolved
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_resolve_decrease( stdClass $record, $notes, $timestamp, $toggle ) {
		if ( !$record->af_is_resolved ) {
			return 'articlefeedbackv5-invalid-feedback-state';
		}
		// decrease means "unresolve" this
		$this->log[] = array('unresolve', $notes, $this->isSystemCall());

		$this->update['af_is_resolved'] = false;
		$this->update['af_is_unresolved'] = true;
		$this->update['af_last_status'] = 'unresolved';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}
		// filter adjustments
		$this->filters['visible-resolved'] = -1;
		$this->filters['visible-unresolved'] = 1;
		$this->relevance[] = 'unresolved';

		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'unresolved', $this->getUserId(), $timestamp );

		return true;
	}

	/**
	 * Flag: decline oversight
	 *
	 * This flag allows oversighters to decline a request for oversight.  It
	 * unsets all request/unrequest on a piece of feedback.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @param  $direction string   increase/decrease
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_resetoversight( stdClass $record, $notes, $timestamp, $toggle, $direction ) {
		$this->log[] = array('decline', $notes, $this->isSystemCall());

		// oversight request count becomes 0
		$this->update['af_oversight_count'] = 0;
		$this->update['af_is_declined'] = true;
		$this->update['af_last_status'] = 'declined';
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		// always increment our all declined
		$this->filters = array('all-declined' => 1);
		if ( $record->af_oversight_count > 0 ) {
			$this->filters['all-requested'] = -1;
		}

		// if this is NOT deleted, change our notdeleted request items
		if( $record->af_is_deleted == false) {
			$this->filters['notdeleted-declined'] = 1;
			if ( $record->af_oversight_count > 0 ) {
				$this->filters['notdeleted-requested'] = -1;
			}
		}

		$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
			'declined', $this->getUserId(), $timestamp );
		return true;
	}

	/**
	 * Flag: flag as abuse
	 *
	 * This flag allows readers to flag a piece of feedback as abusive.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_abuse_increase( stdClass $record, $notes, $timestamp, $toggle ) {
		global $wgArticleFeedbackv5AbusiveThreshold,
			$wgArticleFeedbackv5HideAbuseThreshold;

		$this->results['abuse_count'] = $record->af_abuse_count;
		$this->filters = array();

		$this->results['abuse_count']++;
		$this->relevance[] = 'flagged';

		// Don't allow negative numbers
		$this->results['abuse_count'] = max( 0, $this->results['abuse_count'] );

		// Return a flag in the JSON, that turns the link red.
		if ( $this->results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
			$this->results['abusive'] = 1;
		}

		if ( $this->isSystemCall() ) {
			$this->log[] = array('autoflag', $notes, $this->isSystemCall());
		} else {
			$this->log[] = array('flag', $notes, $this->isSystemCall());
		}

		$this->update[] = "af_abuse_count = af_abuse_count + 1";

		if ( $this->results['abuse_count'] > 0
			&& $record->af_is_hidden == false && $record->af_is_deleted == false ) {
			$this->filters['visible-abusive'] = 1;
		}

		// Auto-hide after threshold flags
		if ( $record->af_abuse_count > $wgArticleFeedbackv5HideAbuseThreshold
		   && false == $record->af_is_hidden ) {

			$this->update['af_is_hidden'] = true;
			$this->update['af_is_unhidden'] = false;
			$this->update['af_is_autohide'] = true;

			$this->update['af_last_status'] = 'autohide';
			$this->update['af_last_status_user_id'] = $this->getUserId();
			$this->update['af_last_status_timestamp'] = $timestamp;
			if ( $notes ) {
				$this->update['af_last_status_notes'] = $notes;
			}

			$this->log[] = array( 'autohide', '', $this->getUserId );

			$this->results['autohidden'] = 1;
			$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
				'autohide', $this->getUserId(), $timestamp );
			$this->results['mask-line'] = ApiArticleFeedbackv5Utils::renderMaskLine(
				'hidden', $record->af_id, $this->getUserId(), $timestamp );

			$this->hideCounts( $record, 'hide' );
			// NOTE: unlike autohide after oversight, we must do the visiblity filter
			$this->visibleCounts( $record, 'invisible' );

		} elseif ( $this->getUserId() < 1 ) {
			$this->update['af_last_status'] = 'autoflag';
			$this->update['af_last_status_user_id'] = 0;
			$this->update['af_last_status_timestamp'] = $timestamp;
			if ( $notes ) {
				$this->update['af_last_status_notes'] = $notes;
			}
			$this->results['status-line'] = ApiArticleFeedbackv5Utils::renderStatusLine(
				'autoflag', 0, $timestamp );
		}

		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-count' )
			->params( $this->results['abuse_count'] )
			->escaped();

		return true;
	}

	/**
	 * Flag: flag as abuse
	 *
	 * This flag allows readers to remove an abuse flag on a piece of feedback.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_abuse_decrease( stdClass $record, $notes, $timestamp, $toggle ) {
		global $wgArticleFeedbackv5AbusiveThreshold,
			$wgArticleFeedbackv5HideAbuseThreshold;

		$this->results['abuse_count'] = $record->af_abuse_count;
		$this->filters = array();

		$this->results['abuse_count']--;
		$this->relevance[] = 'unflagged';

		// Don't allow negative numbers
		$this->results['abuse_count'] = max( 0, $this->results['abuse_count'] );

		// Return a flag in the JSON, that turns the link red.
		if ( $this->results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
			$this->results['abusive'] = 1;
		}

		$this->log[] = array('unflag', $notes, $this->isSystemCall());

		// NOTE: we are bypassing traditional sql escaping here
		$this->update[] = "af_abuse_count = GREATEST(CONVERT(af_abuse_count, SIGNED) -1, 0)";

		if ( $this->results['abuse_count'] < 1
			&& $record->af_is_hidden == false && $record->af_is_deleted == false ) {
			$this->filters['visible-abusive'] = -1;
		}

		// Un-hide if we don't have threshold flags anymore
		if ( $record->af_abuse_count < $wgArticleFeedbackv5AbusiveThreshold && true == $record->af_is_autohide ) {
			$this->update['af_is_hidden'] = false;
			$this->update['af_is_unhidden'] = true;

			$this->hideCounts( $record, 'show' );
			$this->visibleCounts( $record, 'visible' );

			$this->log[] = array( 'unhidden', 'Automatic un-hide', 0 );
		}

		$this->results['abuse_report'] = wfMessage( 'articlefeedbackv5-form-abuse-count' )
			->params( $this->results['abuse_count'] )
			->escaped();

		return true;
	}

	/**
	 * Flag: mark as helpful
	 *
	 * This flag allows readers to vote a piece of feedback up.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @param  $direction string   increase/decrease
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_helpful( stdClass $record, $notes, $timestamp, $toggle, $direction ) {
		$vote = $this->vote( $record, 'helpful', $notes, $timestamp, $toggle, $direction );

		if ( $record->af_helpful_count < $this->helpfulCount ) {
			$type = 'helpful';
		} else {
			$type = 'undo-helpful';
		}

		$this->log[] = array( $type, $notes, $this->isSystemCall() );

		$this->update['af_last_status'] = $type;
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		return $vote;
	}

	/**
	 * Flag: mark as unhelpful
	 *
	 * This flag allows readers to vote a piece of feedback down.
	 *
	 * @param  $record    stdClass the record
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @param  $direction string   increase/decrease
	 * @return mixed      true if success, message key (string) if not
	 */
	private function flag_unhelpful( stdClass $record, $notes, $timestamp, $toggle, $direction ) {
		$vote = $this->vote( $record, 'unhelpful', $notes, $timestamp, $toggle, $direction );

		if ( $record->af_unhelpful_count < $this->unhelpfulCount ) {
			$type = 'unhelpful';
		} else {
			$type = 'undo-unhelpful';
		}

		$this->log[] = array( $type, $notes, $this->isSystemCall() );

		$this->update['af_last_status'] = $type;
		$this->update['af_last_status_user_id'] = $this->getUserId();
		$this->update['af_last_status_timestamp'] = $timestamp;
		if ( $notes ) {
			$this->update['af_last_status_notes'] = $notes;
		}

		return $vote;
	}

	/**
	 * Flag: mark as helpful/unhelpful
	 *
	 * This flag allows readers to vote a piece of feedback up.
	 *
	 * @param  $record    stdClass the record
	 * @param  $flag      string   the flag (helpful/unhelpful)
	 * @param  $notes     string   any notes passed in
	 * @param  $timestamp string   the timestamp
	 * @param  $toggle    bool     whether to toggle the flag
	 * @param  $direction string   increase/decrease
	 * @return mixed      true if success, message key (string) if not
	 */
	private function vote( stdClass $record, $flag, $notes, $timestamp, $toggle, $direction ) {
		$this->results['toggle'] = $toggle;
		$this->helpfulCount = $record->af_helpful_count;
		$this->unhelpfulCount = $record->af_unhelpful_count;
		$this->filters = array();

		// if toggle is on, we are decreasing one and increasing the other atomically
		// means one less http request and the counts don't mess up
		if ( true == $toggle ) {

			if ( ( ( $flag == 'helpful' && $direction == 'increase' )
			 || ( $flag == 'unhelpful' && $direction == 'decrease' ) )
			) {
				// NOTE: we are bypassing traditional sql escaping here
				$this->update[] = "af_helpful_count = af_helpful_count + 1";
				$this->update[] = "af_unhelpful_count = GREATEST(0, CONVERT(af_unhelpful_count, SIGNED) - 1)";

				$this->helpfulCount++;
				$this->unhelpfulCount = max(0, --$this->unhelpfulCount);

			} elseif ( ( ( $flag == 'unhelpful' && $direction == 'increase' )
			 || ( $flag == 'helpful' && $direction == 'decrease' ) )
			) {
				// NOTE: we are bypassing traditional sql escaping here
				$this->update[] = "af_unhelpful_count = af_unhelpful_count + 1";
				$this->update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
				$this->helpfulCount = max(0, --$this->helpfulCount);
				$this->unhelpfulCount++;
			}
			$this->relevance[] = 'helpful';
			$this->relevance[] = 'unhelpful';

		} else {

			if ( 'unhelpful' === $flag && $direction == 'increase' ) {
				// NOTE: we are bypassing traditional sql escaping here
				$this->update[] = "af_unhelpful_count = af_unhelpful_count + 1";
				$this->unhelpfulCount++;
				$this->relevance[] = 'unhelpful';
			} elseif ( 'unhelpful' === $flag && $direction == 'decrease' ) {
				// NOTE: we are bypassing traditional sql escaping here
				$this->update[] = "af_unhelpful_count = GREATEST(0, CONVERT(af_unhelpful_count, SIGNED) - 1)";
				$this->unhelpfulCount = max(0, --$this->unhelpfulCount);
				$this->relevance[] = 'unhelpful';
			} elseif ( $flag == 'helpful' && $direction == 'increase' ) {
				// NOTE: we are bypassing traditional sql escaping here
				$this->update[] = "af_helpful_count = af_helpful_count + 1";
				$this->helpfulCount++;
				$this->relevance[] = 'helpful';
			} elseif ( $flag == 'helpful' && $direction == 'decrease' ) {
				// NOTE: we are bypassing traditional sql escaping here
				$this->update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
				$this->helpfulCount = max(0, --$this->helpfulCount);
				$this->relevance[] = 'helpful';
			}

		}

		// note this is signed - no max/min needed here
		$this->netHelpfulness = $this->helpfulCount - $this->unhelpfulCount;

		// If net == 0 neither helpful nor unhelpful
		if ( $this->netHelpfulness == 0 ) {
			// if it WAS unhelpful or helpful undo
			if ( $record->af_net_helpfulness < 0 ) {
				$this->filters['visible-unhelpful'] = -1;
			} elseif ( $record->af_net_helpfulness > 0 ) {
				$this->filters['visible-helpful'] = -1;
			}
		// if net > 0 we are now helpful
		} elseif ( $this->netHelpfulness > 0 ) {
			// if it WAS unhelpful undo
			if ( $record->af_net_helpfulness < 0 ) {
				$this->filters['visible-unhelpful'] = -1;
			}
			// if it didn't use to be helpful but now is, bump it
			if ( $record->af_net_helpfulness < 1 ) {
				$this->filters['visible-helpful'] = 1;
			}
		// if net < 0 we are now unhelpful
		} elseif ( $this->netHelpfulness < 0 ) {
			// if it WAS helpful undo
			if ( $record->af_net_helpfulness > 0 ) {
				$this->filters['visible-helpful'] = -1;
			}
			// if it didn't use to be unhelpful but now is, bump it
			if ( $record->af_net_helpfulness > -1 ) {
				$this->filters['visible-unhelpful'] = 1;
			}
		}

		$this->results['vote_count'] = $this->helpfulCount +
		$this->unhelpfulCount;
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
	 * Returns whether an action is allowed
	 *
	 * @param  $action string the name of the action
	 * @return bool whether it's allowed
	 */
	public function isAllowed( $action ) {
		if ( $this->isSystemCall() ) {
			return true;
		}
		return $this->user->isAllowed( $action );
	}

	/**
	 * Gets the user id
	 *
	 * @return mixed the user's ID, or zero if it's a system call
	 */
	public function getUserId() {
		if ( $this->isSystemCall() ) {
			return 0;
		}
		return $this->user->getId();
	}

	/**
	 * Gets the user link, for use in displays
	 *
	 * @return string the link
	 */
	public function getUserLink() {
		if ( $this->isSystemCall() ) {
			return ApiArticleFeedbackv5Utils::getUserLink( 0, null );
		}
		return ApiArticleFeedbackv5Utils::getUserLink( $this->user );
	}

	/**
	 * Helper function to grab a record from the database with information
	 * about the current feedback row
	 *
	 * @param  object $dbw     connection to database
	 * @param  int    $id      id of the feedback to fetch
	 * @return object database record
	 */
	private function fetchRecord( $dbw, $id ) {
		$record = $dbw->selectRow(
			'aft_article_feedback',
			array(
				'af_id',
				'af_page_id',
				'af_abuse_count',
				'af_is_hidden',
				'af_helpful_count',
				'af_unhelpful_count',
				'af_is_deleted',
				'af_net_helpfulness',
				'af_is_unhidden',
				'af_is_undeleted',
				'af_is_declined',
				'af_has_comment',
				'af_oversight_count',
				'af_is_unrequested',
				'af_is_autohide',
				'af_is_featured',
				'af_is_unfeatured',
				'af_is_resolved',
				'af_is_unresolved',
				'af_relevance_score'),
			array( 'af_id' => $id )
		);
		return $record;
	}

	/**
	 * Helper function - if an item is deleted OR it is hidden, then
	 * all visible items need to be adjusted
	 *
	 * Just pass in a record, if it is already hidden or deleted no changes are made
	 * to filters, otherwise all visible items are changed
	 *
	 * @param object $record existing feedback database record
	 * @param string $action visible or invisible
	 */
	protected function visibleCounts( $record, $action = 'invisible' ) {
		global $wgArticleFeedbackv5Cutoff;

		if ( $action === 'visible' ) {
			if ( $record->af_is_hidden == false && $record->af_is_deleted == false) {
				return;
			}
			$int = 1;

		} else {
			if ( $record->af_is_hidden == true || $record->af_is_deleted == true) {
				return;
			}
			$int = -1;
		}

		// if this was/is in the relevant filter count it needs to be incremented/decremented
		if ( ( $record->af_has_comment || ( $record->af_net_helpfulness > 0 ) || $record->af_is_featured )
		   && $record->af_relevance_score > $wgArticleFeedbackv5Cutoff )  {
			$this->filters['visible-relevant'] = $int;
		}

		// visible is only decremented for hide or delete
		$this->filters['visible'] = $int;

		if ( $record->af_is_featured ) {
			$this->filters['visible-featured'] = $int;
		}
		if ( $record->af_has_comment ) {
			$this->filters['visible-comment'] = $int;
		}
		if ( $record->af_net_helpfulness > 0 ) {
			$this->filters['visible-helpful'] = $int;
		}

		// monitors can see
		if ( $record->af_net_helpfulness < 0 ) {
			$this->filters['visible-unhelpful'] = $int;
		}
		if ( $record->af_abuse_count > 0 ) {
			$this->filters['visible-abusive'] = $int;
		}
		if ( $record->af_is_unfeatured ) {
			$this->filters['visible-unfeatured'] = $int;
		}
		if ( $record->af_is_resolved ) {
			$this->filters['visible-resolved'] = $int;
		}
		if ( $record->af_is_unresolved ) {
			$this->filters['visible-unresolved'] = $int;
		}
	}

	/**
	 * Helper function - all filter changes for hiding a row because autohide
	 * will use this
	 *
	 * @param object $record existing feedback database record
	 * @param string $action hide or show
	 */
	protected function hideCounts( $record, $action = 'hide' ) {
		if ( $action === 'show' ) {
			// we always increment total unhidden count, and if it's not deleted increment notdeleted count
			if ( !$record->af_is_deleted ) {
				$this->filters['notdeleted-unhidden'] = 1;
				$this->filters['notdeleted-hidden'] = -1;
			}
			$this->filters['all-unhidden'] = 1;
			$this->filters['all-hidden'] = -1;
		} else {
			// we always increment total hidden count, and if it's not deleted increment notdeleted count
			if ( !$record->af_is_deleted ) {
				$this->filters['notdeleted-hidden'] = 1;
				if ( $record->af_is_unhidden ) {
					$this->filters['notdeleted-unhidden'] = -1;
				}
			}
			$this->filters['all-hidden'] = 1;
			if ( $record->af_is_unhidden ) {
				$this->filters['all-unhidden'] = -1;
			}
		}
	}

	/**
	 * Helper function to dig out page url and title, feedback permalink, and
	 * requestor page url and name - if all this data can be retrieved properly
	 * it shoves an email job into the queue for sending to the oversighters'
	 * mailing list - only called for NEW oversight requests
	 *
	 * @param  $record    stdClass the record
	 */
	protected function sendOversightEmail( stdClass $record ) {
		global $wgUser;

		// jobs need a title object
		$title_object = Title::newFromID( $record->af_page_id );

		if ( !$title_object ) {
			return; // no title object, no mail
		}

		// get the string name of the page
		$page_name = $title_object->getDBKey();

		// make a title out of our user (sigh)
		$user_page = $wgUser->getUserPage();

		if ( !$user_page ) {
			return; // no user title object, no mail
		}

		// to build our permalink, use the feedback entry key + the page name (isn't page name a title? but title is an object? confusing)
//		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$page_name/" . $this->feedbackId );

		// @todo: these 2 lines will spoof a new url which will lead to the central feedback page with the
		// selected post on top; this is due to a couple of oversighters reporting issues with the permalink page.
		// once these issues have been solved, these lines should be removed & above line uncommented
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5' );
		$permalink = Title::makeTitle( NS_SPECIAL, $centralPageName, "$this->feedbackId" );

		// build our params
		$params = array( 'user_name' => $wgUser->getName(),
				'user_url' => $user_page->getCanonicalUrl(),
				'page_name' => $title_object->getPrefixedText(),
				'page_url' => $title_object->getCanonicalUrl(),
				'permalink' => $permalink->getCanonicalUrl() );

		$job = new ArticleFeedbackv5MailerJob( $title_object, $params );
		$job->insert();
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
