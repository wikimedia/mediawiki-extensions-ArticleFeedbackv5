<?php
/**
 * ApiFlagFeedbackArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 */

/**
 * This class pulls the individual ratings/comments for the feedback page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiFlagFeedbackArticleFeedbackv5 extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	/**
	 * Execute the API call
	 *
	 * This single api call covers all cases where flags are being applied to
	 * a piece of feedback
	 *
	 * A feedback request consists of
	 * 1. 
	 */
	public function execute() {

		// default values for information to be filled in
		$filters   = array();
		$update    = array();
		$results   = array();

		// get important values from our parameters
		$params     = $this->extractRequestParams();
		$pageId     = $params['pageid'];
		$feedbackId = $params['feedbackid'];
		$flag       = $params['flagtype'];
		$notes      = $params['note'];
		$toggle     = $params['toggle'];
		$direction  = isset( $params['direction'] ) ? $params['direction'] : 'increase';
		$where      = array( 'af_id' => $feedbackId );

		// woah, we were not checking for permissions (that could have been script kiddy bad)
		global $wgUser;

		// we use ONE db connection that talks to master
		$dbw     = wfGetDB( DB_MASTER );
		$dbw->begin();
		$timestamp = $dbw->timestamp();

		// load feedback record, bail if we don't have one
		$record = $this->fetchRecord( $dbw, $feedbackId );

		if ( $record === false || !$record->af_id ) {
			// no-op, because this is already broken
			$error = 'articlefeedbackv5-invalid-feedback-id';

		} elseif ( 'delete' == $flag && $wgUser->isAllowed( 'aftv5-delete-feedback' )) {

			// deleting means to "mark as oversighted" and "delete" it
			// oversighting also auto-hides the item

			// increase means "oversight this"
			if( $direction == 'increase' ) {
				$activity = 'oversight';

				// delete
				$update['af_is_deleted'] = true;
				$update['af_is_undeleted'] = false;
				// only store the oversighter on delete/oversight
				$update['af_oversight_user_id'] = $wgUser->getId();
				$update['af_oversight_timestamp'] = $timestamp;
				// delete specific filters
				$filters['deleted'] = 1;
				$filters['notdeleted'] = -1;
				if (true == $record->af_is_undeleted ) {
					$filters['undeleted'] = -1;
				}

				// This is data for the "hidden by, oversighted by" red line
				$results['oversight-user'] = ApiArticleFeedbackv5Utils::getUserLink($wgUser);
				$results['oversight-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );

				// autohide if not hidden
				if (false == $record->af_is_hidden ) {
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
					$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink(null, 'Article Feedback V5');
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

		} elseif ( 'hide' == $flag && $wgUser->isAllowed( 'aftv5-hide-feedback' )) {

			// increase means "hide this"
			if( $direction == 'increase' ) {
				$activity = 'hidden';

				// hide
				$update['af_is_hidden'] = true;
				$update['af_is_unhidden'] = false;
				// only store the hider on hide not show
				$update['af_hide_user_id'] = $wgUser->getId();
				$update['af_hide_timestamp'] = $timestamp;
				$filters = $this->changeFilterCounts( $record, $filters, 'hide' );

				// This is data for the "hidden by, oversighted by" red line
				$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink($wgUser);
				$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );

			} else {
			// decrease means "unhide this"
				$activity = 'unhidden';

				$update['af_is_hidden'] = false;
				$update['af_is_unhidden'] = true;

				$filters = $this->changeFilterCounts( $record, $filters, 'show' );
			}

		} elseif( 'resetoversight' === $flag && $wgUser->isAllowed( 'aftv5-delete-feedback' )) {

			$activity = 'decline';
			// oversight request count becomes 0
			$update['af_oversight_count'] = 0;
			// declined oversight is flagged
			$update['af_is_declined'] = true;
			$filters['declined'] = 1;
			// if the oversight count was greater then 1
			if(0 < $record->af_oversight_count) {
				$filters['needsoversight'] = -1;
			}

		} elseif( 'abuse' === $flag) {

			// Conditional formatting for abuse flag
			global $wgArticleFeedbackv5AbusiveThreshold,
				$wgArticleFeedbackv5HideAbuseThreshold;

			$results['abuse_count'] = $record->af_abuse_count;

			// Make the abuse count in the result reflect this vote.
			if( $direction == 'increase' ) {
				$results['abuse_count']++; 
			} else { 
				$results['abuse_count']--; 
			}
			// no negative numbers
			$results['abuse_count'] = max(0, $results['abuse_count']);

			// Return a flag in the JSON, that turns the link red.
			if( $results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
				$results['abusive'] = 1;
			}

			// Adding a new abuse flag: abusive++
			if($direction == 'increase') {
				$activity = 'flag';
				$filters['abusive'] = 1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_abuse_count = af_abuse_count + 1";

				// Auto-hide after threshold flags
				if( $record->af_abuse_count > $wgArticleFeedbackv5HideAbuseThreshold
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
					$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink(null, 'Article Feedback V5');
					$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );
				}
			}
	
			// Removing the last abuse flag: abusive--
			elseif($direction == 'decrease') {
				$activity = 'unflag';
				$filters['abusive'] = -1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_abuse_count = GREATEST(CONVERT(af_abuse_count, SIGNED) -1, 0)";

				// Un-hide if we don't have 5 flags anymore
				if( $record->af_abuse_count == 5 && true == $record->af_is_hidden ) {
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
		} elseif( 'oversight' === $flag && $wgUser->isAllowed( 'aftv5-hide-feedback' )) {

			if($direction == 'increase') {
				$activity = 'request';
				$filters['needsoversight'] = 1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_oversight_count = af_oversight_count + 1";

				// autohide if not hidden
				if (false == $record->af_is_hidden ) {
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
					$results['hide-user'] = ApiArticleFeedbackv5Utils::getUserLink(null, 'Article Feedback V5');
					$results['hide-timestamp'] = wfTimestamp( TS_RFC2822, $timestamp );
				}

				// IF the previous setting was 0, send an email
				if ( $record->af_oversight_count < 1) {

					 $this->sendOversightEmail( $record->af_page_id , $feedbackId );

				}
			} elseif($direction == 'decrease') {
				$activity = 'unrequest';
				$filters['needsoversight'] = -1;
				// NOTE: we are bypassing traditional sql escaping here
				$update[] = "af_oversight_count = GREATEST(CONVERT(af_oversight_count, SIGNED) - 1, 0)";
			} else {
				// TODO: real error here?
				$error = 'articlefeedbackv5-invalid-feedback-flag';
			}

		// helpful and unhelpful flagging
		} elseif( 'unhelpful' === $flag || 'helpful' === $flag) {

			$results['toggle'] = $toggle;
			$helpful = $record->af_helpful_count;
			$unhelpful = $record->af_unhelpful_count;

			// if toggle is on, we are decreasing one and increasing the other atomically
			// means one less http request and the counts don't mess up
			if (true == $toggle) {

				if( ( ($flag == 'helpful' && $direction == 'increase' )
				 || ($flag == 'unhelpful' && $direction == 'decrease' ) )
				) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_helpful_count = af_helpful_count + 1";
					$update[] = "af_unhelpful_count = GREATEST(0, CONVERT(af_unhelpful_count, SIGNED) - 1)";
					$helpful++;
					$unhelpful--;

				} elseif ( ( ($flag == 'unhelpful' && $direction == 'increase' )
				 || ($flag == 'helpful' && $direction == 'decrease' ) )
				) {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = af_unhelpful_count + 1";
					$update[] = "af_helpful_count = GREATEST(0, CONVERT(af_helpful_count, SIGNED) - 1)";
					$helpful--;
					$unhelpful++;
				}

			} else {

				if ( 'unhelpful' === $flag && $direction == 'increase') {
					// NOTE: we are bypassing traditional sql escaping here
					$update[] = "af_unhelpful_count = af_unhelpful_count + 1";
					$unhelpful++;
				} elseif ( 'unhelpful' === $flag && $direction == 'decrease') {
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
			if( ( ($flag == 'helpful' && $direction == 'increase' )
			 || ($flag == 'unhelpful' && $direction == 'decrease' ) )
			) {
				// net was -1: no longer unhelpful
				if( $netHelpfulness == -1 ) {
					$filters['unhelpful'] = -1;
				}
	
				// net was 0: now helpful
				if( $netHelpfulness == 0 ) {
					$filters['helpful'] = 1;
				}
			}

			// increase unhelpful OR decrease unhelpful
			if( ( ($flag == 'unhelpful' && $direction == 'increase' )
			 || ($flag == 'helpful' && $direction == 'decrease' ) )
			) {
				// net was 1: no longer helpful
				if( $netHelpfulness == 1 ) {
					$filters['helpful'] = -1;
				}
	
				// net was 0: now unhelpful
				if( $netHelpfulness == 0 ) {
					$filters['unhelpful'] = 1;
				}
			}

		} else {
			$error = 'articlefeedbackv5-invalid-feedback-flag';
		}

		// we were valid
		if ( !isset($error) ) {

			$success = $dbw->update(
				'aft_article_feedback',
				$update,
				$where,
				__METHOD__
			);

			// Update the filter count rollups.
			ApiArticleFeedbackv5Utils::updateFilterCounts( $dbw, $pageId, $filters );

			$dbw->commit(); // everything went well, so we commit our db changes

			// helpfulness counts are NOT logged, no activity is set
			if (isset($activity)) {
				ApiArticleFeedbackv5Utils::logActivity( $activity , $pageId, $feedbackId, $notes );
			}

			// handle implicit hide/show logging
			if ( isset( $implicit_hide ) && $implicit_hide ) {
				ApiArticleFeedbackv5Utils::logActivity( 'hidden' , $pageId, $feedbackId, '', true);
			}

			// Update helpful/unhelpful display count after submission.
			if ( $flag == 'helpful' || $flag == 'unhelpful' ) {

				// no negative numbers please
				$helpful = max(0, $helpful);
				$unhelpful = max(0, $unhelpful);

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

		if ( $error ) {
			$results['result'] = 'Error';
			$results['reason'] = $error;
		} else {
			$results['result'] = 'Success';
			$results['reason'] = null;
		}

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			$results
		);
	}

	/**
	 * Helper function to grab a record from the database with information
	 * about the current feedback row
	 *
	 * @param object $dbw connection to database
	 * @param int $id id of the feedback to fetch
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
				'af_oversight_count'),
			array( 'af_id' => $id )
		);
		return $record;
	}

	/**
	 * Helper function to manipulate all flags when hiding/showing a piece of feedback
	 *
	 * @param object $record existing feedback database record
	 * @param array $filters existing filters
	 * @param string $action 'hide' or 'show'
	 * @return array the filter array with new filter choices added
	 */
	protected function changeFilterCounts( $record, $filters, $action ) {
		// only filters that hide shouldn't manipulate are
		// all, deleted, undeleted, and notdeleted

		// use -1 (decrement) for hide, 1 for increment (show) - default is hide
		switch($action) {
			case 'show':
				$int = 1;
				// if we're showing, this will increment
				$filters['unhidden'] = 1;
				break;
			default:
				// if we're hiding, and was unhidden, decrement
				if(true == $record->af_is_unhidden) {
					$filters['unhidden'] = -1;
				}
				$int = -1;
				break;
		}

		// visible, invisible, unhidden
		$filters['visible'] = $int;
		$filters['invisible'] = -$int; // opposite of int

		// comment
		if(true == $record->af_has_comment) {
			$filters['comment'] = $int;
		}

		// abusive
		if( $record->af_abuse_count > 1 ) {
			$filters['abusive'] = $int;
		}
		// helpful and unhelpful
		if( $record->af_net_helpfulness > 1 ) {
			$filters['helpful'] = $int;
		} elseif( $record->af_net_helpfulness < 1 ) {
			$filters['unhelpful'] = $int;
		}

		return $filters;
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'pageid'     => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'feedbackid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'flagtype'   => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'abuse', 'hide', 'helpful', 'unhelpful', 'delete', 'oversight', 'resetoversight' )
			),
			'direction' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'increase', 'decrease' )
			),
			'note' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			),
			'toggle' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'boolean'
			)
		);
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		return array(
			'feedbackid'  => 'FeedbackID to flag',
			'type'        => 'Type of flag to apply - hide or abuse',
			'note'        => 'Information on why the feedback activity occurred',
			'toggle'      => 'The flag is being toggled atomically, only useful for (un)helpful'
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'Flag a feedbackID as abusive or hidden.'
		);
	}

	/**
	 * Gets any possible errors
	 *
	 * @return array the errors
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?list=articlefeedbackv5-view-feedback&affeedbackid=1&aftype=abuse',
		);
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

	public function isWriteMode() { return true; }
	public function mustBePosted() { return true; }

	/**
	 * Helper function to dig out page url and title, feedback permalink, and
	 * requestor page url and name - if all this data can be retrieved properly
	 * it shoves an email job into the queue for sending to the oversighters'
	 * mailing list - only called for NEW oversight requests
	 *
	 * @param int $page_id page id to grab info on
	 * @param int $feedback_id identifier for the feedback item
	 */
	protected function sendOversightEmail( $page_id, $feedback_id) {
		global $wgUser;

		// jobs need a title object
		$title_object = Title::newFromID( $page_id );

		if ( !$title_object ) {
			return; // no title object, no mail
		}

		// get the string name of the page
		$page_name = $title_object->getDBKey();

		// make a title out of our user (sigh)
		$user_page = Title::makeTitle( NS_USER, $wgUser->getName() );

		if ( !$user_page ) {
			return; // no user title object, no mail
		}

		// to build our permalink, use the feedback entry key + the page name (isn't page name a title? but title is an object? confusing)
		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$page_name/$feedback_id" );

		if ( !$permalink ) {
			return; // no proper permalink? no mail
		}

		// build our params
		$params = array( 'user_name' => $wgUser->getName(),
				'user_url' => $user_page->getCanonicalUrl(),
				'page_name' => $title_object->getText(),
				'page_url' => $title_object->getCanonicalUrl(),
				'permalink' => $permalink->getCanonicalUrl());

		$job = new ArticleFeedbackv5MailerJob( $title_object, $params );
		$job->insert();
	}
}
