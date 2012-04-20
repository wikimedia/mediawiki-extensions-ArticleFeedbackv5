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
		$filters   = array();
		$update    = array();
		$results   = array();
		$log = array();
		$relevance_score = array();

		// start
		$where = array( 'af_id' => $this->feedbackId );

		// we use ONE db connection that talks to master
		$dbw     = wfGetDB( DB_MASTER );
		$dbw->begin();
		$timestamp = $dbw->timestamp();

		// load feedback record, bail if we don't have one
		$record = $this->fetchRecord( $dbw, $this->feedbackId );

		if ( $record === false || !$record->af_id ) {
			// no-op, because this is already broken
			$error = 'articlefeedbackv5-invalid-feedback-id';
		// deleting means to "mark as oversighted" and "delete" it
		} elseif ( 'delete' == $flag && $this->isAllowed( 'aftv5-delete-feedback' ) ) {
			// oversight and optional autohide (if not hidden)
			if ( $direction == 'increase' && !$record->af_is_deleted ) {
				$update['af_is_deleted'] = true;
				$update['af_is_undeleted'] = false;

				$log[] = array('oversight', $notes, $this->isSystemCall());

				// always increment oversighted count and decrement notdeleted
				$filters = array('all-oversighted' => 1,
						 'notdeleted' => -1);

				// if this was previously visible, adjust counts
				$filters = $this->visibleCounts( $record, $filters, 'invisible' );

				// adjust notdeleted counts
				if( $record->af_is_hidden ) {
					$filters['notdeleted-hidden'] = -1;
				}
				if( $record->af_is_unhidden ) {
					$filters['notdeleted-unhidden'] = -1;
				}
				if( $record->af_oversight_count > 0 ) {
					$filters['notdeleted-requested'] = -1;
				}
				if( $record->af_is_unrequested ) {
					$filters['notdeleted-unrequested'] = -1;
				}
				if( $record->af_is_declined ) {
					$filters['notdeleted-declined'] = -1;
				}

				// adjust undeleted count if necessary
				if( $record->af_is_undeleted ) {
					$filters['all-unoversighted'] = -1;
				}

				// autohide if not hidden
				if ( false == $record->af_is_hidden ) {
					// we must tell the helper method this record is deleted for filter purposes
					$record->af_is_deleted = true;

					$update['af_is_hidden'] = true;
					$update['af_is_unhidden'] = false;
					$update['af_is_autohide'] = true;

					$update['af_last_status'] = 'autohide';
					$update['af_last_status_user_id'] = $this->getUserId();
					$update['af_last_status_timestamp'] = $timestamp;

					$log[] = array( 'autohide', '', $this->getUserId );

					$results['autohidden'] = 1;
					$results['status-line'] = $this->createStatusLine( 'autohide', $this->getUserId(), $timestamp );

					// NOTE: we've already adjusted all visiblity counts above so we only do hide specific ones
					$filters = $this->hideCounts( $record, $filters, 'hide' );

				} else {

					$update['af_last_status'] = 'deleted';
					$update['af_last_status_user_id'] = $this->getUserId();
					$update['af_last_status_timestamp'] = $timestamp;

					$results['status-line'] = $this->createStatusLine( 'deleted', $this->getUserId(), $timestamp );
				}

			// unoversight (no autohide)
			} elseif( $direction == 'decrease' && $record->af_is_deleted ) {

				$update['af_is_deleted'] = false;
				$update['af_is_undeleted'] = true;

				$update['af_last_status'] = 'undeleted';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;

				$log[] = array('unoversight', $notes, $this->isSystemCall());
				$results['status-line'] = $this->createStatusLine( 'undeleted', $this->getUserId(), $timestamp );

				$filters = array('all-unoversighted' => 1,
						 'all-oversighted' => -1);

				$filters = $this->visibleCounts( $record, $filters, 'visible' );

				// adjust notdeleted counts
				if( $record->af_is_hidden ) {
					$filters['notdeleted-hidden'] = 1;
				}
				if( $record->af_is_unhidden ) {
					$filters['notdeleted-unhidden'] = 1;
				}
				if( $record->af_oversight_count > 0 ) {
					$filters['notdeleted-requested'] = 1;
				}
				if( $record->af_is_unrequested ) {
					$filters['notdeleted-unrequested'] = 1;
				}
				if( $record->af_is_declined ) {
					$filters['notdeleted-declined'] = 1;
				}

			} else {
				$error = 'articlefeedbackv5-invalid-feedback-state';
			}
		// hidden is only visible to monitors or oversighters
		} elseif ( 'hide' == $flag && $this->isAllowed( 'aftv5-hide-feedback' ) ) {
			if ( $direction == 'increase' && !$record->af_is_hidden ) {
				$update['af_is_hidden'] = true;
				$update['af_is_unhidden'] = false;

				$update['af_last_status'] = 'hidden';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;

				$filters = array();

				$filters = $this->hideCounts( $record, $filters, 'hide' );
				$filters = $this->visibleCounts( $record, $filters, 'invisible' );

				$log[] = array('hidden', $notes, $this->isSystemCall());
				$results['status-line'] = $this->createStatusLine( 'hidden', $this->getUserId(), $timestamp );

			} elseif( $direction == 'decrease' && $record->af_is_hidden ) {

				$update['af_is_hidden'] = false;
				$update['af_is_unhidden'] = true;

				$update['af_last_status'] = 'unhidden';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;

				$filters = array();

				$filters = $this->hideCounts( $record, $filters, 'show' );
				$filters = $this->visibleCounts( $record, $filters, 'visible' );

				$log[] = array('unhidden', $notes, $this->isSystemCall());
				$results['status-line'] = $this->createStatusLine( 'unhidden', $this->getUserId(), $timestamp );

			} else {
				$error = 'articlefeedbackv5-invalid-feedback-state';
			}
		// request/unrequest oversight
		} elseif ( 'oversight' === $flag && $this->isAllowed( 'aftv5-hide-feedback' ) ) {
			if ( $direction == 'increase' ) {

				$log[] = array('request', $notes, $this->isSystemCall());

				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_oversight_count = af_oversight_count + 1";

				// we do not increment anything by default
				$filters = array();

				// if this is a NEW request, increment our counters
				if ( $record->af_oversight_count < 1 ) {
					$filters['all-requested'] = 1;
					if ( $record->af_is_deleted == false ) {
						$filters['notdeleted-requested'] = 1;
					}
				}

				// turn off unrequested if necessary
				if( $record->af_is_unrequested ) {
					$filters['all-unrequested'] = -1;
					$update['af_is_unrequested'] = false;
					if ( $record->af_is_deleted == false ) {
						$filters['notdeleted-unrequested'] = -1;
					}
				}

				if ( false == $record->af_is_hidden ) {

					$update['af_is_hidden'] = true;
					$update['af_is_unhidden'] = false;
					$update['af_is_autohide'] = true;

					$update['af_last_status'] = 'autohide';
					$update['af_last_status_user_id'] = $this->getUserId();
					$update['af_last_status_timestamp'] = $timestamp;

					$log[] = array( 'autohide', '', $this->getUserId );

					$results['autohidden'] = 1;
					$results['status-line'] = $this->createStatusLine( 'autohide', $this->getUserId(), $timestamp );

					$filters = $this->hideCounts( $record, $filters, 'hide' );
					// NOTE: unlike autohide after oversight, we must do the visiblity filter
					$filters = $this->visibleCounts( $record, $filters, 'invisible' );

				} else {
					$update['af_last_status'] = 'request';
					$update['af_last_status_user_id'] = $this->getUserId();
					$update['af_last_status_timestamp'] = $timestamp;
					$results['status-line'] = $this->createStatusLine( 'request', $this->getUserId(), $timestamp );
				}

				// IF the previous setting was 0, send an email
				if ( $record->af_oversight_count < 1 ) {
					 $this->sendOversightEmail();
				}

			} elseif ( $direction == 'decrease' ) {

				$log[] = array('unrequest', $notes, $this->isSystemCall());
				$filters = array();

				if( ( $record->af_oversight_count - 1 ) < 1) {
					$update['af_is_unrequested'] = true;
					$filters['all-unrequested'] = 1;
					$filters['all-requested'] = -1;
					if ( $record->af_is_deleted == false ) {
						$filters['notdeleted-unrequested'] = 1;
						$filters['notdeleted-requested'] = -1;
					}
				}

				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_oversight_count = GREATEST(CONVERT(af_oversight_count, SIGNED) - 1, 0)";
				$update['af_last_status'] = 'unrequest';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;
				$results['status-line'] = $this->createStatusLine( 'unrequest', $this->getUserId(), $timestamp );

			} else {
				$error = 'articlefeedbackv5-invalid-feedback-state';
			}
		// this is "decline oversight" which unsets all request/unrequest on a piece of feedback
		} elseif ( 'feature' === $flag && $this->isAllowed( 'aftv5-feature-feedback' ) ) {

			// increase means "feature this"
			if ( $direction == 'increase' && !$record->af_is_featured ) {
				$log[] = array('feature', $notes, $this->isSystemCall());

				$update['af_is_featured'] = true;
				$update['af_is_unfeatured'] = false;
				$update['af_last_status'] = 'featured';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;

				// filter adjustments
				$filters['visible-featured'] = 1;
				if ( true == $record->af_is_unfeatured) {
					$filters['visible-unfeatured'] = -1;
				}
				$relevance_score[] = 'featured';

				$results['status-line'] = $this->createStatusLine( 'featured', $this->getUserId(), $timestamp );

			} elseif ( $direction == 'decrease' && $record->af_is_featured ) {
				// decrease means "unfeature" this
				$log[] = array('unfeature', $notes, $this->isSystemCall());

				$update['af_is_featured'] = false;
				$update['af_is_unfeatured'] = true;
				$update['af_last_status'] = 'unfeatured';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;
				// filter adjustments
				$filters['visible-featured'] = -1;
				$filters['visible-unfeatured'] = 1;
				$relevance_score[] = 'unfeatured';

				$results['status-line'] = $this->createStatusLine( 'unfeatured', $this->getUserId(), $timestamp );
			} else {
				$error = 'articlefeedbackv5-invalid-feedback-state';
			}

		} elseif ( 'resolve' === $flag && $this->isAllowed( 'aftv5-feature-feedback' ) ) {

			// increase means "resolve this"
			if ( $direction == 'increase' && !$record->af_is_resolved) {
				$log[] = array('resolve', $notes, $this->isSystemCall());

				$update['af_is_resolved'] = true;
				$update['af_is_unresolved'] = false;
				$update['af_last_status'] = 'resolved';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;

				// filter adjustments
				$filters['visible-resolved'] = 1;
				if ( true == $record->af_is_unresolved) {
					$filters['visible-unresolved'] = -1;
				}
				$relevance_score[] = 'resolved';

				$results['status-line'] = $this->createStatusLine( 'resolved', $this->getUserId(), $timestamp );

			} elseif ( $direction == 'decrease' && $record->af_is_resolved ) {
				// decrease means "unresolve" this
				$log[] = array('unresolve', $notes, $this->isSystemCall());

				$update['af_is_resolved'] = false;
				$update['af_is_unresolved'] = true;
				$update['af_last_status'] = 'unresolved';
				$update['af_last_status_user_id'] = $this->getUserId();
				$update['af_last_status_timestamp'] = $timestamp;
				// filter adjustments
				$filters['visible-resolved'] = -1;
				$filters['visible-unresolved'] = 1;
				$relevance_score[] = 'unresolved';

				$results['status-line'] = $this->createStatusLine( 'unresolved', $this->getUserId(), $timestamp );
			} else {
				$error = 'articlefeedbackv5-invalid-feedback-state';
			}

		} elseif ( 'resetoversight' === $flag && $this->isAllowed( 'aftv5-delete-feedback' ) ) {

			$log[] = array('decline', $notes, $this->isSystemCall());

			// oversight request count becomes 0
			$update['af_oversight_count'] = 0;
			$update['af_is_declined'] = true;
			$update['af_last_status'] = 'declined';
			$update['af_last_status_user_id'] = $this->getUserId();
			$update['af_last_status_timestamp'] = $timestamp;

			// always increment our all declined
			$filters = array('all-declined' => 1);
			if ( $record->af_oversight_count > 0 ) {
				$filters['all-requested'] = -1;
			}

			// if this is NOT deleted, change our notdeleted request items
			if( $record->af_is_deleted == false) {
				$filters['notdeleted-declined'] = 1;
				if ( $record->af_oversight_count > 0 ) {
					$filters['notdeleted-requested'] = -1;
				}
			}

			$results['status-line'] = $this->createStatusLine( 'declined', $this->getUserId(), $timestamp );

		// this is flag/unflag for abuse
		} elseif ( 'abuse' === $flag ) {

			// Conditional formatting for abuse flag
			global $wgArticleFeedbackv5AbusiveThreshold,
				$wgArticleFeedbackv5HideAbuseThreshold;

			$results['abuse_count'] = $record->af_abuse_count;
			$filters = array();

			// Make the abuse count in the result reflect this vote.
			if ( $direction == 'increase' ) {
				$results['abuse_count']++;
				$relevance_score[] = 'flagged';
			} else {
				$results['abuse_count']--;
				$relevance_score[] = 'unflagged';
			}
			// no negative numbers
			$results['abuse_count'] = max( 0, $results['abuse_count'] );

			// Return a flag in the JSON, that turns the link red.
			if ( $results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
				$results['abusive'] = 1;
			}

			if ( $this->isSystemCall() ) {
				$log[] = array('autoflag', $notes, $this->isSystemCall());
			} else {
				$log[] = array('flag', $notes, $this->isSystemCall());
			}

			if ( $direction == 'increase' ) {

				$update[] = "af_abuse_count = af_abuse_count + 1";

				if ( $results['abuse_count'] > 0
				    && $record->af_is_hidden == false && $record->af_is_deleted == false ) {
					$filters['visible-abusive'] = 1;
				}

				// Auto-hide after threshold flags
				if ( $record->af_abuse_count > $wgArticleFeedbackv5HideAbuseThreshold
				   && false == $record->af_is_hidden ) {

					$update['af_is_hidden'] = true;
					$update['af_is_unhidden'] = false;
					$update['af_is_autohide'] = true;

					$update['af_last_status'] = 'autohide';
					$update['af_last_status_user_id'] = $this->getUserId();
					$update['af_last_status_timestamp'] = $timestamp;

					$log[] = array( 'autohide', '', $this->getUserId );

					$results['autohidden'] = 1;
					$results['status-line'] = $this->createStatusLine( 'autohide', $this->getUserId(), $timestamp );

					$filters = $this->hideCounts( $record, $filters, 'hide' );
					// NOTE: unlike autohide after oversight, we must do the visiblity filter
					$filters = $this->visibleCounts( $record, $filters, 'invisible' );

				} elseif ( $this->getUserId() < 1 ) {
					$update['af_last_status'] = 'autoflag';
					$update['af_last_status_user_id'] = 0;
					$update['af_last_status_timestamp'] = $timestamp;
					$results['status-line'] = $this->createStatusLine( 'autoflag', 0, $timestamp );
				}

			} elseif ( $direction == 'decrease' ) {

				$log[] = array('unflag', $notes, $this->isSystemCall());

				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_abuse_count = GREATEST(CONVERT(af_abuse_count, SIGNED) -1, 0)";

				if ( $results['abuse_count'] < 1
				    && $record->af_is_hidden == false && $record->af_is_deleted == false ) {
					$filters['visible-abusive'] = -1;
				}

				// Un-hide if we don't have threshold flags anymore
				if ( $record->af_abuse_count < $wgArticleFeedbackv5AbusiveThreshold && true == $record->af_is_autohide ) {
					$update['af_is_hidden'] = false;
					$update['af_is_unhidden'] = true;

					$filters = $this->hideCounts( $record, $filters, 'show' );
					$filters = $this->visibleCounts( $record, $filters, 'visible' );

					$log[] = array( 'unhidden', 'Automatic un-hide', 0 );
				}

			} else {
				$error = 'articlefeedbackv5-invalid-feedback-state';
			}

		// helpful and unhelpful flagging
		} elseif ( 'unhelpful' === $flag || 'helpful' === $flag ) {

			$results['toggle'] = $toggle;
			$helpful = $record->af_helpful_count;
			$unhelpful = $record->af_unhelpful_count;
			$filters = array();

			// if toggle is on, we are decreasing one and increasing the other atomically
			// means one less http request and the counts don't mess up
			if ( true == $toggle ) {

				if ( ( ( $flag == 'helpful' && $direction == 'increase' )
				 || ( $flag == 'unhelpful' && $direction == 'decrease' ) )
				) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_helpful_count = af_helpful_count + 1";
					$update[] = "af_unhelpful_count = GREATEST(0, CONVERT(af_unhelpful_count, SIGNED) - 1)";

					$helpful++;
					$unhelpful = max(0, --$unhelpful);

				} elseif ( ( ( $flag == 'unhelpful' && $direction == 'increase' )
				 || ( $flag == 'helpful' && $direction == 'decrease' ) )
				) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = af_unhelpful_count + 1";
					$update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
					$helpful = max(0, --$helpful);
					$unhelpful++;
				}
				$relevance_score[] = 'helpful';
				$relevance_score[] = 'unhelpful';

			} else {

				if ( 'unhelpful' === $flag && $direction == 'increase' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = af_unhelpful_count + 1";
					$unhelpful++;
					$relevance_score[] = 'unhelpful';
				} elseif ( 'unhelpful' === $flag && $direction == 'decrease' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = GREATEST(0, CONVERT(af_unhelpful_count, SIGNED) - 1)";
					$unhelpful = max(0, --$unhelpful);
					$relevance_score[] = 'unhelpful';
				} elseif ( $flag == 'helpful' && $direction == 'increase' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_helpful_count = af_helpful_count + 1";
					$helpful++;
					$relevance_score[] = 'helpful';
				} elseif ( $flag == 'helpful' && $direction == 'decrease' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
					$helpful = max(0, --$helpful);
					$relevance_score[] = 'helpful';
				}

			}

			// note this is signed - no max/min needed here
			$netHelpfulness = $helpful - $unhelpful;

			// If net == 0 neither helpful nor unhelpful
			if ( $netHelpfulness == 0 ) {
				// if it WAS unhelpful or helpful undo
				if ( $record->af_net_helpfulness < 0 ) {
					$filters['visible-unhelpful'] = -1;
				} elseif ( $record->af_net_helpfulness > 0 ) {
					$filters['visible-helpful'] = -1;
				}
			// if net > 0 we are now helpful
			} elseif ( $netHelpfulness > 0 ) {
				// if it WAS unhelpful undo
				if ( $record->af_net_helpfulness < 0 ) {
					$filters['visible-unhelpful'] = -1;
				}
				// if it didn't use to be helpful but now is, bump it
				if ( $record->af_net_helpfulness < 1 ) {
					$filters['visible-helpful'] = 1;
				}
			// if net < 0 we are now unhelpful
			} elseif ( $netHelpfulness < 0 ) {
				// if it WAS helpful undo
				if ( $record->af_net_helpfulness > 0 ) {
					$filters['visible-helpful'] = -1;
				}
				// if it didn't use to be unhelpful but now is, bump it
				if ( $record->af_net_helpfulness > -1 ) {
					$filters['visible-unhelpful'] = 1;
				}
			}

		} else {
			$error = 'articlefeedbackv5-invalid-feedback-flag';
		}

		// figure out if we have relevance_scores to adjust
		if ( count($relevance_score) > 0 ) {
			global $wgArticleFeedbackv5RelevanceScoring;
			$math = array();

			foreach( $relevance_score as $item ) {
				if ( array_key_exists( $item, $wgArticleFeedbackv5RelevanceScoring ) ) {
					$math[] = $wgArticleFeedbackv5RelevanceScoring[$item];
				}
			}

			$update[] = 'af_relevance_score = (' . implode (' + ', $math) . ')';
			$update[] = 'af_relevance_sort = - af_relevance_score';
		}

		// we were valid
		if ( !isset( $error ) ) {

			$success = $dbw->update(
				'aft_article_feedback',
				$update,
				$where,
				__METHOD__
			);

			// Update the filter count rollups.
			ApiArticleFeedbackv5Utils::updateFilterCounts( $dbw, $this->pageId, $filters );

			// Update helpful/unhelpful display count after submission but BEFORE db commit to stay in the transaction
			if ( $flag == 'helpful' || $flag == 'unhelpful' ) {

				// no negative numbers please
				$helpful = max( 0, $helpful );
				$unhelpful = max( 0, $unhelpful );

				$results['helpful'] = wfMessage(
					'articlefeedbackv5-form-helpful-votes',
					$helpful, $unhelpful
				)->escaped();

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
			foreach( $log as $entry ) {
				ApiArticleFeedbackv5Utils::logActivity( $entry[0], $this->pageId, $this->feedbackId, $entry[1], $entry[2] );
			}

		}

		if ( isset( $error ) ) {
			$results['result'] = 'Error';
			$results['reason'] = $error;
		} else {
			$results['result'] = 'Success';
			$results['reason'] = null;
		}

		return $results;
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
				'af_is_unresolved'),
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
	 * @param  object $record  existing feedback database record
	 * @param  array  $filters existing filters
	 * @param  string $action visible or invisible
	 * @return array  the filter array with new filter choices added
	 */
	protected function visibleCounts( $record, $filters, $action = 'invisible' ) {

		if( $action === 'visible' ) {
			if ( $record->af_is_hidden == false && $record->af_is_deleted == false) {
				return $filters;
			}
			$int = 1;
		} else {
			if ( $record->af_is_hidden == true || $record->af_is_deleted == true) {
				return $filters;
			}
			$int = -1;
		}

		// visible is only decremented for hide or delete
		$filters['visible'] = $int;

		// all can see
		if( $record->af_has_comment || ( $record->af_net_helpfulness > 0 ) || $record->af_is_featured ) {
			$filters['visible-relevant'] = $int;
		}
		if( $record->af_is_featured ) {
			$filters['visible-featured'] = $int;
		}
		if( $record->af_has_comment ) {
			$filters['visible-comment'] = $int;
		}
		if( $record->af_net_helpfulness > 0 ) {
			$filters['visible-helpful'] = $int;
		}

		// monitors can see
		if( $record->af_net_helpfulness < 0 ) {
			$filters['visible-unhelpful'] = $int;
		}
		if( $record->af_abuse_count > 0 ) {
			$filters['visible-abusive'] = $int;
		}
		if( $record->af_is_unfeatured ) {
			$filters['visible-unfeatured'] = $int;
		}
		if( $record->af_is_resolved ) {
			$filters['visible-resolved'] = $int;
		}
		if( $record->af_is_unresolved ) {
			$filters['visible-unresolved'] = $int;
		}

		return $filters;
	}

	/**
	 * Helper function - all filter changes for hiding a row because autohide
	 * will use this
	 *
	 * @param  object $record  existing feedback database record
	 * @param  array  $filters existing filters
	 * @return array  the filter array with new filter choices added
	 */
	protected function hideCounts( $record, $filters, $action = 'hide' ) {

		if( $action === 'show' ) {
			// we always increment total unhidden count, and if it's not deleted increment notdeleted count
			if ( !$record->af_is_deleted ) {
				$filters['notdeleted-unhidden'] = 1;
				$filters['notdeleted-hidden'] = -1;
			}

			$filters['all-unhidden'] = 1;
			$filters['all-hidden'] = -1;

		} else {
			// we always increment total hidden count, and if it's not deleted increment notdeleted count
			if ( !$record->af_is_deleted ) {
				$filters['notdeleted-hidden'] = 1;
				if( $record->af_is_unhidden ) {
					$filters['notdeleted-unhidden'] = -1;
				}
			}
			$filters['all-hidden'] = 1;

			if( $record->af_is_unhidden ) {
				$filters['all-unhidden'] = -1;
			}
		}

		return $filters;
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
		$title_object = Title::newFromID( $this->pageId );

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
		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$page_name/" . $this->feedbackId );

		if ( !$permalink ) {
			return; // no proper permalink? no mail
		}

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
	 * Helper function to create a new red status line based on the last status line created
	 * action performed
	 */
	protected function createStatusLine( $last_status, $last_status_user_id, $last_status_timestamp ) {
		global $wgLang;

		return Html::rawElement( 'span', array(
			'class' => 'articleFeedbackv5-feedback-status-marker'
			),
			wfMessage( 'articlefeedbackv5-status-' . $last_status )
				->rawParams( ApiArticleFeedbackv5Utils::getUserLink( $last_status_user_id ) )
				->params( $wgLang->date( $last_status_timestamp ),
					$wgLang->time( $last_status_timestamp ) )
				->escaped()
			);
	}
}
