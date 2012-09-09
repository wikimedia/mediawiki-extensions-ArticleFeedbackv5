<?php
/**
 * ApiArticleFeedbackv5Utils class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

/**
 * Utility methods used by api calls
 *
 * ApiArticleFeedback and ApiQueryArticleFeedback don't descend from the same
 * parent, which is why these are all static methods instead of just a parent
 * class with inheritable methods. I don't get it either.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiArticleFeedbackv5Utils {

	/**
	 * Returns whether feedback is enabled for this page
	 *
	 * @param  $params array the params
	 * @return bool
	 */
	public static function isFeedbackEnabled( $params ) {
		global $wgArticleFeedbackv5Namespaces;
		$title = Title::newFromID( $params['pageid'] );
		if (
			// not an existing page?
			is_null( $title )
			// Namespace not a valid ArticleFeedback namespace?
			|| !in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces )
			// Page a redirect?
			|| $title->isRedirect()
		) {
			// ...then feedback disabled
			return false;
		}
		return true;
	}

	/**
	 * Check if a certain feedback was posted by the current user
	 *
	 * @param object $record The feedback record
	 * @return bool
	 */
	public static function isOwnFeedback( $record ) {
		global $wgRequest, $wgUser, $wgArticleFeedbackv5Tracking;

		// if logged in user, we can know for certain if feedback was posted when logged in
		if ( $wgUser->getId() && isset( $record->user ) && $wgUser->getId() == intval( $record->user ) ) {
			return true;
		}

		// if either the feedback was posted when not logged in, or the visitor is now not
		// logged in, compare the feedback's id with what's stored in a cookie
		$version = isset( $wgArticleFeedbackv5Tracking['version'] ) ? $wgArticleFeedbackv5Tracking['version'] : 0;
		$cookie = json_decode( $wgRequest->getCookie( 'feedback-ids', 'ext_articleFeedbackv5@' . $version . '-' ), true );
		if ( $cookie !== null && is_array( $cookie ) && isset( $record->id ) ) {
			return in_array( $record->id, $cookie );
		}

		return false;
	}

	/**
	 * Gets the known feedback fields
	 *
	 * @return ResultWrapper the rows in the aft_article_field table
	 */
	public static function getFields() {
		global $wgMemc;

		$key = wfMemcKey( 'articlefeedbackv5', 'getFields' );
		$cached = $wgMemc->get( $key );

		if ( $cached != '' ) {
			return $cached;
		} else {
			$rv = array();
			$dbr = wfGetDB( DB_SLAVE );
			$rows = $dbr->select(
				'aft_article_field',
				array(
					'afi_name',
					'afi_id',
					'afi_data_type',
					'afi_bucket_id'
				),
				null,
				__METHOD__
			);

			foreach ( $rows as $row ) {
				$rv[] = array(
					'afi_name' => $row->afi_name,
					'afi_id' => $row->afi_id,
					'afi_data_type' => $row->afi_data_type,
					'afi_bucket_id' => $row->afi_bucket_id
				);
			}

			// An hour? That might be reasonable for a cache time.
			$wgMemc->set( $key, $rv, 60 * 60 );
		}

		return $rv;
	}

	/**
	 * Gets the known feedback options
	 *
	 * Pulls all the rows in the aft_article_field_option table, then
	 * arranges them like so:
	 *   {field id} => array(
	 *       {option id} => {option name},
	 *   ),
	 *
	 * @return array the rows in the aft_article_field_option table
	 */
	public static function getOptions() {
		global $wgMemc;

		$key = wfMemcKey( 'articlefeedbackv5', 'getOptions' );
		$cached = $wgMemc->get( $key );

		if ( $cached != '' ) {
			return $cached;
		} else {
			$rv = array();
			$dbr = wfGetDB( DB_SLAVE );
			$rows = $dbr->select(
				'aft_article_field_option',
				array(
					'afo_option_id',
					'afo_field_id',
					'afo_name'
				),
				null,
				__METHOD__
			);
			foreach ( $rows as $row ) {
				$rv[$row->afo_field_id][$row->afo_option_id] = $row->afo_name;
			}
			// An hour? That might be reasonable for a cache time.
			$wgMemc->set( $key, $rv, 60 * 60 );
		}
		return $rv;
	}

	/**
	 * Get the total number of responses per filter
	 *
	 * @param  $pageId int [optional] the page ID
	 * @return array the counts
	 */
	public static function getFilterCounts( $pageId = 0 ) {
		$rv   = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			'aft_article_filter_count',
			array(
				'afc_filter_name',
				'afc_filter_count'
			),
			array(
				'afc_page_id' => $pageId
			),
			__METHOD__
		);
		foreach ( $rows as $row ) {
			$rv[ $row->afc_filter_name ] = $row->afc_filter_count;
		}
		return $rv;
	}

	/**
	 * Must pass in a useable database handle in a transaction - since all
	 * counts MUST be incremented in the same transaction as the data changing
	 * or the counts will be off
	 *
	 * @param $pageId      int   the ID of the page (page.page_id)
	 * @param $filters     array  an array of filter name => direction pairs
	 *                            direction 1 = increment, -1 = decrement
	 */
	public static function updateFilterCounts( $dbw, $pageId, $filters ) {
		wfProfileIn( __METHOD__ );

		// Don't do anything unless we have filters to process.
		if ( empty( $filters ) || count( $filters ) < 1 ) {
			wfProfileOut( __METHOD__ );
			return;
		}

		foreach ( $filters as $filter => $direction ) {
			// One for the page
			$rows[] = array(
				'afc_page_id'      => $pageId,
				'afc_filter_name'  => $filter,
				'afc_filter_count' => 0
			);
			// One for the central log
			$rows[] = array(
				'afc_page_id'      => 0,
				'afc_filter_name'  => $filter,
				'afc_filter_count' => 0
			);
		}

		// Try to insert the record, but ignore failures.
		// Ensures the count row exists.
		$dbw->insert(
			'aft_article_filter_count',
			$rows,
			__METHOD__,
			array( 'IGNORE' )
		);

		foreach ( $filters as $filter => $direction ) {
			$value = ( $direction > 0 ) ? 'afc_filter_count + 1' : 'GREATEST(0, CONVERT(afc_filter_count, SIGNED) - 1)';

			// Update each row with the new count for each page filter
			$dbw->update(
				'aft_article_filter_count',
				array( "afc_filter_count = $value" ),
				array(
					'afc_page_id'     => $pageId,
					'afc_filter_name' => $filter
				),
				__METHOD__
			);

			// Update each row with the new count for the central filters
			$dbw->update(
				'aft_article_filter_count',
				array( "afc_filter_count = $value" ),
				array(
					'afc_page_id'     => 0,
					'afc_filter_name' => $filter
				),
				__METHOD__
			);
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Creates a user link for a log row
	 *
	 * @param int $userId can be null or a user object
	 * @param string $userIp (name works too)
	 * @return anchor tag link to user
	 */
	public static function getUserLink( $userId, $userIp = null ) {
		if ( ( $userId instanceof User ) ) {
			// user is an object, all good, make link
			$user = $userId;
		} else {
			// if $userId is not an object
			$userId = (int) $userId;
			if ( $userId !== 0 ) { // logged-in users
				$user = User::newFromId( $userId );
			} elseif ( !is_null( $userIp ) ) { // IP users
				$userText = $userIp;
				$user = User::newFromName( $userText, false );
			} else { // magic user
				global $wgArticleFeedbackv5AutoHelp;
				$element = Linker::makeExternalLink(
					$wgArticleFeedbackv5AutoHelp,
					wfMessage( 'articlefeedbackv5-default-user' )->text()
				);
				return $element;
			}
		}

		$element = Linker::userLink( $user->getId(), $user->getName() );

		return $element;
	}

	/**
	 * Helper function to create a new red status line based on the last status line created
	 * action performed
	 *
	 * @param string $status
	 * @param int $userId
	 * @param string $timestamp
	 * @return string
	 */
	public static function renderStatusLine( $status, $userId, $timestamp ) {
		global $wgLang;
		return Html::rawElement(
			'span',
			array( 'class' => 'articleFeedbackv5-feedback-status-marker articleFeedbackv5-laststatus-' . $status ),
			wfMessage( 'articlefeedbackv5-status-' . $status )
				->rawParams( ApiArticleFeedbackv5Utils::getUserLink( $userId ) )
				->params( $wgLang->date( $timestamp ), $wgLang->time( $timestamp ) )
				->escaped()
		);
	}

	/**
	 * Returns the percentage helpful, given a helpful count and an unhelpful count
	 *
	 * @param  $helpful   int the number of helpful votes
	 * @param  $unhelpful int the number of unhelpful votes
	 * @return int        the percentage
	 */
	public static function percentHelpful( $helpful, $unhelpful ) {
		if ( $helpful > 0 || $unhelpful > 0 ) {
			return intval( ( $helpful / ( $helpful + $unhelpful ) ) * 100 );
		}
		return 0;
	}

	/**
	 * Helper function to create a mask line
	 *
	 * @param  $type      string the type (hidden or oversight)
	 * @param  $post_id   int    the feedback post id
	 * @param  $user_id   int    the user id
	 * @param  $timestamp string the timestamp, from the db
	 * @return string     the mask line
	 */
	public static function renderMaskLine( $type, $post_id, $user_id, $timestamp ) {
		if ( (int) $user_id !== 0 ) { // logged-in users
			$username = User::newFromId( $user_id )->getName();
		} else { // magic user
			$username = wfMessage( 'articlefeedbackv5-default-user' )->text();
		}
		$timestamp = new MWTimestamp( $timestamp );

		return wfMessage( 'articlefeedbackv5-mask-text-' . $type )
			->numParams( $post_id )
			->params( $username )
			->rawParams( $timestamp->getHumanTimestamp()->escaped() )
			->escaped();
	}

	/**
	 * Run comment through SpamRegex
	 *
	 * @param $value
	 * @param $pageId
	 * @return bool Will return boolean false if valid or true if flagged
	 */
	public static function validateSpamRegex( $value ) {
		// Respect $wgSpamRegex
		global $wgSpamRegex;
		if ( ( is_array( $wgSpamRegex ) && count( $wgSpamRegex ) > 0 )
			|| ( is_string( $wgSpamRegex ) && strlen( $wgSpamRegex ) > 0 ) ) {
			// In older versions, $wgSpamRegex may be a single string rather than
			// an array of regexes, so make it compatible.
			$regexes = ( array ) $wgSpamRegex;
			foreach ( $regexes as $regex ) {
				if ( preg_match( $regex, $value ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Run comment through SpamBlacklist
	 *
	 * @param $value
	 * @param $pageId
	 * @return bool Will return boolean false if valid or true if flagged
	 */
	public static function validateSpamBlacklist( $value, $pageId ) {
		// Check SpamBlacklist, if installed
		if ( function_exists( 'wfSpamBlacklistObject' ) ) {
			$spam = wfSpamBlacklistObject();
		} elseif ( class_exists( 'BaseBlacklist' ) ) {
			$spam = BaseBlacklist::getInstance( 'spam' );
		}
		if ( $spam ) {
			$title = Title::newFromText( 'ArticleFeedbackv5_' . $pageId );
			$ret = $spam->filter( $title, $value, '' );
			if ( $ret !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Run comment through AbuseFilter extension
	 *
	 * @param $value
	 * @param $pageId
	 * @param $callback Callback function to be called by AbuseFilter
	 * @return bool|string Will return boolean false if valid or error message (string) if flagged
	 */
	public static function validateAbuseFilter( $value, $pageId, $callback = null ) {
		// Check AbuseFilter, if installed
		if ( class_exists( 'AbuseFilter' ) ) {
			global $wgUser;

			// Set up variables
			$title = Title::newFromID( $pageId );
			$vars = new AbuseFilterVariableHolder;
			$vars->addHolder( AbuseFilter::generateUserVars( $wgUser ) );
			$vars->addHolder( AbuseFilter::generateTitleVars( $title , 'ARTICLE' ) );
			$vars->setVar( 'SUMMARY', 'Article Feedback 5' );
			$vars->setVar( 'ACTION', 'feedback' );
			$vars->setVar( 'new_wikitext', $value );
			$vars->setLazyLoadVar( 'new_size', 'length', array( 'length-var' => 'new_wikitext' ) );

			// Add custom action handlers
			if ( $callback && is_callable( $callback ) ) {
				global $wgAbuseFilterCustomActionsHandlers;

				$wgAbuseFilterCustomActionsHandlers['aftv5flagabuse'] = $callback;
				$wgAbuseFilterCustomActionsHandlers['aftv5hide'] = $callback;
				$wgAbuseFilterCustomActionsHandlers['aftv5requestoversight'] = $callback;
			}

			// Check the filters (mimics AbuseFilter::filterAction)
			global $wgArticleFeedbackv5AbuseFilterGroup;
			$vars->setVar( 'context', 'filter' );
			$vars->setVar( 'timestamp', time() );
			$results = AbuseFilter::checkAllFilters( $vars, $wgArticleFeedbackv5AbuseFilterGroup );
			if ( count( array_filter( $results ) ) == 0 ) {
				return false;
			}

			// Abuse filter consequences
			$matched = array_keys( array_filter( $results ) );
			list( $actionsTaken, $errorMsg ) = AbuseFilter::executeFilterActions( $matched, $title, $vars );

			// Send to the abuse filter log
			$dbr = wfGetDB( DB_SLAVE );
			global $wgRequest;
			$logTemplate = array(
				'afl_user' => $wgUser->getId(),
				'afl_user_text' => $wgUser->getName(),
				'afl_timestamp' => $dbr->timestamp( wfTimestampNow() ),
				'afl_namespace' => $title->getNamespace(),
				'afl_title' => $title->getDBkey(),
				'afl_ip' => $wgRequest->getIP()
			);
			$action = $vars->getVar( 'ACTION' )->toString();
			AbuseFilter::addLogEntries( $actionsTaken, $logTemplate, $action, $vars, $wgArticleFeedbackv5AbuseFilterGroup );

			// Local consequences
			foreach ( $actionsTaken as $id => $actions ) {
				foreach ( array( 'disallow', 'warn' ) as $level ) {
					if ( in_array( $level, $actions ) ) {
						return $errorMsg;
					}
				}
			}
		}

		return false;
	}
}
