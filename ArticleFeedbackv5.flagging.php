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

		// start
		$where = array( 'af_id' => $this->feedbackId );

		// we may not actually use this, but don't want to repeat this a million times
		$default_user = wfMessage( 'articlefeedbackv5-default-user' )->text();

		// we use ONE db connection that talks to master
		$dbw     = wfGetDB( DB_MASTER );
		$dbw->begin();
		$timestamp = $dbw->timestamp();

		// load feedback record, bail if we don't have one
		$record = $this->fetchRecord( $dbw, $this->feedbackId );

		if ( $record === false || !$record->af_id ) {
			// no-op, because this is already broken
			$error = 'articlefeedbackv5-invalid-feedback-id';

		} elseif ( 'delete' == $flag && $this->isAllowed( 'aftv5-delete-feedback' ) ) {

			// deleting means to "mark as oversighted" and "delete" it
			// oversighting also auto-hides the item

			// increase means "oversight this"
			if ( $direction == 'increase' ) {
				$activity = 'oversight';

				// delete
				$update['af_is_deleted'] = true;
				$update['af_is_undeleted'] = false;
				// only store the oversighter on delete/oversight
				$update['af_oversight_user_id'] = $this->getUserId();
				$update['af_oversight_timestamp'] = $timestamp;
				// delete specific filters
				$filters['deleted'] = 1;
				$filters['notdeleted'] = -1;
				if ( true == $record->af_is_undeleted ) {
					$filters['undeleted'] = -1;
				}

				// This is data for the "hidden by, oversighted by" red line
				$results['oversight-user'] = $this->getUserLink();
				$results['oversight-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );

				// autohide if not hidden
				if ( false == $record->af_is_hidden ) {
					$update['af_is_hidden'] = true;
					$update['af_is_unhidden'] = false;
					$filters = $this->changeFilterCounts( $record, $filters, 'hide' );
					// 0 is used for "autohidden" purposes, we'll explicitly set it to overwrite last hider
					$update['af_hide_user_id'] = 0;
					$update['af_hide_timestamp'] = $timestamp;
					$implicit_hide = true; // for logging
					// tell front-end autohiding was done
					$results['autohidden'] = 1;
					// This is data for the "hidden by, oversighted by" red line
					$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink( null, $default_user );
					$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );
				}

			} else {
			// decrease means "unoversight this" but does NOT auto-unhide
				$activity = 'unoversight';
				$update['af_is_deleted'] = false;
				$update['af_is_undeleted'] = true;
				// increment "undeleted", decrement "deleted"
				// NOTE: we do not touch visible, since hidden controls visiblity
				$filters['deleted'] = -1;
				$filters['undeleted'] = 1;
				// increment "notdeleted" for count of everything but oversighted
				$filters['notdeleted'] = 1;
			}

		} elseif ( 'hide' == $flag && $this->isAllowed( 'aftv5-hide-feedback' ) ) {

			// increase means "hide this"
			if ( $direction == 'increase' ) {
				$activity = 'hidden';

				// hide
				$update['af_is_hidden'] = true;
				$update['af_is_unhidden'] = false;
				// only store the hider on hide not show
				$update['af_hide_user_id'] = $this->getUserId();
				$update['af_hide_timestamp'] = $timestamp;
				$filters = $this->changeFilterCounts( $record, $filters, 'hide' );

				// This is data for the "hidden by, oversighted by" red line
				$results['hide-user'] = $this->getUserLink();
				$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );

			} else {
			// decrease means "unhide this"
				$activity = 'unhidden';

				$update['af_is_hidden'] = false;
				$update['af_is_unhidden'] = true;

				$filters = $this->changeFilterCounts( $record, $filters, 'show' );
			}

		} elseif ( 'resetoversight' === $flag && $this->isAllowed( 'aftv5-delete-feedback' ) ) {

			$activity = 'decline';
			// oversight request count becomes 0
			$update['af_oversight_count'] = 0;
			// declined oversight is flagged
			$update['af_is_declined'] = true;
			$filters['declined'] = 1;
			// if the oversight count was greater then 1
			if ( 0 < $record->af_oversight_count ) {
				$filters['needsoversight'] = -1;
			}

		} elseif ( 'abuse' === $flag ) {

			// Conditional formatting for abuse flag
			global $wgArticleFeedbackv5AbusiveThreshold,
				$wgArticleFeedbackv5HideAbuseThreshold;

			$results['abuse_count'] = $record->af_abuse_count;

			// Make the abuse count in the result reflect this vote.
			if ( $direction == 'increase' ) {
				$results['abuse_count']++;
			} else {
				$results['abuse_count']--;
			}
			// no negative numbers
			$results['abuse_count'] = max( 0, $results['abuse_count'] );

			// Return a flag in the JSON, that turns the link red.
			if ( $results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
				$results['abusive'] = 1;
			}

			// Adding a new abuse flag: abusive++
			if ( $direction == 'increase' ) {
				$activity = 'flag';
				$filters['abusive'] = 1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_abuse_count = af_abuse_count + 1";

				// Auto-hide after threshold flags
				if ( $record->af_abuse_count > $wgArticleFeedbackv5HideAbuseThreshold
				   && false == $record->af_is_hidden ) {
					// hide
					$update['af_is_hidden'] = true;
					$update['af_is_unhidden'] = false;
					// 0 is used for "autohidden" purposes, we'll explicitly set it to overwrite last hider
					$update['af_hide_user_id'] = 0;
					$update['af_hide_timestamp'] = $timestamp;

					$filters = $this->changeFilterCounts( $record, $filters, 'hide' );
					$results['abuse-hidden'] = 1;
					$implicit_hide = true;

					// tell front-end autohiding was done
					$results['autohidden'] = 1;
					// This is data for the "hidden by, oversighted by" red line
					$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink( null, $default_user );
					$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );
				}
			}

			// Removing the last abuse flag: abusive--
			elseif ( $direction == 'decrease' ) {
				$activity = 'unflag';
				$filters['abusive'] = -1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_abuse_count = GREATEST(CONVERT(af_abuse_count, SIGNED) -1, 0)";

				// Un-hide if we don't have 5 flags anymore
				if ( $record->af_abuse_count == 5 && true == $record->af_is_hidden ) {
					$update['af_is_hidden'] = false;
					$update['af_is_unhidden'] = true;

					$filters = $this->changeFilterCounts( $record, $filters, 'show' );

					$implicit_unhide = true;
				}
			} else {
				// TODO: real error here?
				$error = 'articlefeedbackv5-invalid-feedback-flag';
			}

		// NOTE: this is actually request/unrequest oversight and works similar to abuse
		} elseif ( 'oversight' === $flag && $this->isAllowed( 'aftv5-hide-feedback' ) ) {

			if ( $direction == 'increase' ) {
				$activity = 'request';
				$filters['needsoversight'] = 1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_oversight_count = af_oversight_count + 1";

				// autohide if not hidden
				if ( false == $record->af_is_hidden ) {
					$update['af_is_hidden'] = true;
					$update['af_is_unhidden'] = false;
					// 0 is used for "autohidden" purposes, we'll explicitly set it to overwrite last hider
					$update['af_hide_user_id'] = 0;
					$update['af_hide_timestamp'] = $timestamp;

					$filters = $this->changeFilterCounts( $record, $filters, 'hide' );
					$implicit_hide = true; // for logging
					// tell front-end autohiding was done
					$results['autohidden'] = 1;
					// This is data for the "hidden by, oversighted by" red line
					$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink( null, $default_user );
					$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );
				}

				// IF the previous setting was 0, send an email
				if ( $record->af_oversight_count < 1 ) {
					 $this->sendOversightEmail();
				}

			} elseif ( $direction == 'decrease' ) {
				$activity = 'unrequest';
				$filters['needsoversight'] = -1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_oversight_count = GREATEST(CONVERT(af_oversight_count, SIGNED) - 1, 0)";
			} else {
				// TODO: real error here?
				$error = 'articlefeedbackv5-invalid-feedback-flag';
			}

		// helpful and unhelpful flagging
		} elseif ( 'unhelpful' === $flag || 'helpful' === $flag ) {

			$results['toggle'] = $toggle;
			$helpful = $record->af_helpful_count;
			$unhelpful = $record->af_unhelpful_count;

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
					$unhelpful--;

				} elseif ( ( ( $flag == 'unhelpful' && $direction == 'increase' )
				 || ( $flag == 'helpful' && $direction == 'decrease' ) )
				) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = af_unhelpful_count + 1";
					$update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
					$helpful--;
					$unhelpful++;
				}

			} else {

				if ( 'unhelpful' === $flag && $direction == 'increase' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = af_unhelpful_count + 1";
					$unhelpful++;
				} elseif ( 'unhelpful' === $flag && $direction == 'decrease' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = GREATEST(0, CONVERT(af_unhelpful_count, SIGNED) - 1)";
					$unhelpful--;
				} elseif ( $flag == 'helpful' && $direction == 'increase' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_helpful_count = af_helpful_count + 1";
					$helpful++;
				} elseif ( $flag == 'helpful' && $direction == 'decrease' ) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
					$helpful--;
				}

			}

			$netHelpfulness = $helpful - $unhelpful;

			// increase helpful OR decrease unhelpful
			if ( ( ( $flag == 'helpful' && $direction == 'increase' )
			 || ( $flag == 'unhelpful' && $direction == 'decrease' ) )
			) {
				// net was -1: no longer unhelpful
				if ( $netHelpfulness == -1 ) {
					$filters['unhelpful'] = -1;
				}

				// net was 0: now helpful
				if ( $netHelpfulness == 0 ) {
					$filters['helpful'] = 1;
				}
			}

			// increase unhelpful OR decrease unhelpful
			if ( ( ( $flag == 'unhelpful' && $direction == 'increase' )
			 || ( $flag == 'helpful' && $direction == 'decrease' ) )
			) {
				// net was 1: no longer helpful
				if ( $netHelpfulness == 1 ) {
					$filters['helpful'] = -1;
				}

				// net was 0: now unhelpful
				if ( $netHelpfulness == 0 ) {
					$filters['unhelpful'] = 1;
				}
			}

		} else {
			$error = 'articlefeedbackv5-invalid-feedback-flag';
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

			$dbw->commit(); // everything went well, so we commit our db changes

			// helpfulness counts are NOT logged, no activity is set
			if ( isset( $activity ) ) {
				ApiArticleFeedbackv5Utils::logActivity( $activity, $this->pageId, $this->feedbackId, $notes, $this->isSystemCall() );
			}

			// handle implicit hide/show logging
			if ( isset( $implicit_hide ) && $implicit_hide ) {
				ApiArticleFeedbackv5Utils::logActivity( 'hidden' , $this->pageId, $this->feedbackId, '', true );
			}

			// Update helpful/unhelpful display count after submission.
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
						'af_id' => $params['feedbackid'],
					),
					__METHOD__
				);
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
			return ApiArticleFeedbackv5Utils::getUserLink( null, $default_user );
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
				'af_oversight_count' ),
			array( 'af_id' => $id )
		);
		return $record;
	}

	/**
	 * Helper function to manipulate all flags when hiding/showing a piece of feedback
	 *
	 * @param  object $record  existing feedback database record
	 * @param  array  $filters existing filters
	 * @param  string $action  'hide' or 'show'
	 * @return array  the filter array with new filter choices added
	 */
	protected function changeFilterCounts( $record, $filters, $action ) {
		// only filters that hide shouldn't manipulate are
		// all, deleted, undeleted, and notdeleted

		// use -1 (decrement) for hide, 1 for increment (show) - default is hide
		switch( $action ) {
			case 'show':
				$int = 1;
				// if we're showing, this will increment
				$filters['unhidden'] = 1;
				break;
			default:
				// if we're hiding, and was unhidden, decrement
				if ( true == $record->af_is_unhidden ) {
					$filters['unhidden'] = -1;
				}
				$int = -1;
				break;
		}

		// visible, invisible, unhidden
		$filters['visible'] = $int;
		$filters['invisible'] = -$int; // opposite of int

		// comment
		if ( true == $record->af_has_comment ) {
			$filters['comment'] = $int;
		}

		// abusive
		if ( $record->af_abuse_count > 1 ) {
			$filters['abusive'] = $int;
		}
		// helpful and unhelpful
		if ( $record->af_net_helpfulness > 1 ) {
			$filters['helpful'] = $int;
		} elseif ( $record->af_net_helpfulness < 1 ) {
			$filters['unhelpful'] = $int;
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

}

