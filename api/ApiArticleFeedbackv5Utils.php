<?php
/**
 * ApiViewRatingsArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
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
	 * Returns the revision limit for a page
	 *
	 * @param  $pageId int the page id
	 * @return int the revision limit
	 */
	public static function getRevisionLimit( $pageId ) {
		global $wgArticleFeedbackv5RatingLifetime;
		$dbr = wfGetDB( DB_SLAVE );
		$revision = $dbr->selectField(
			'revision', 'rev_id',
			array( 'rev_page' => $pageId ),
			__METHOD__,
			array(
				'ORDER BY' => 'rev_id DESC',
				'LIMIT' => 1,
				'OFFSET' => $wgArticleFeedbackv5RatingLifetime - 1
			)
		);
		return $revision ? intval( $revision ) : 0;
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
	 * Must pass in a useable database handle in a transaction - since all
	 * counts MUST be incremented in the same transaction as the data changing
	 * or the counts will be off
	 *
	 * @param $pageId      int   the ID of the page (page.page_id)
	 * @param $filters     array  an array of filter name => direction pairs
	 *                            direction 1 = increment, -1 = decrement
	 */
	public static function updateFilterCounts( $dbw, $pageId, $filters ) {

		// Don't do anything unless we have filters to process.
		if ( empty( $filters ) || count( $filters ) < 1 ) {
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
	}

	/**
	 * Adds an activity item to the global log under the articlefeedbackv5
	 *
	 * @param $type      string the type of activity we'll be logging
	 * @param $pageId    int    the id of the page so we can look it up
	 * @param $itemId    int    the id of the feedback item, used to build permalinks
	 * @param $notes     string any notes that were stored with the activity
	 * @param $doer      int    id of user who did the action, null will use currently logged in user
	 * @param $params    array  of parameters that can be passed into the msg thing - used for "perpetrator" for log entry
	 */
	public static function logActivity( $type, $pageId, $itemId, $notes, $doer = null, $params = array() ) {

		// These are our valid activity log actions
		$valid = array( 'oversight', 'unoversight', 'hidden', 'unhidden',
				'decline', 'request', 'unrequest', 'flag', 'unflag', 'autoflag', 'autohide',
				'feature', 'unfeature', 'resolve', 'unresolve' );

		// suppress
		$suppress = array( 'oversight', 'unoversight', 'decline', 'request', 'unrequest');

		// if we do not have a valid action, return immediately
		if ( !in_array( $type, $valid ) ) {
			return;
		}

		// log type might be afv5 or suppress
		$logtype = 'articlefeedbackv5';
		$increment = 'af_activity_count';

		if ( in_array( $type, $suppress ) ) {
			$logtype = 'suppress';
			$increment = 'af_suppress_count';
		}

		// we only have the page id, we need the string page name for the permalink
		$title_object = Title::newFromID( $pageId );

		// no title object? no page? well then no logging
		if ( !$title_object ) {
			return;
		}

		// get the string name of the page
		$page_name = $title_object->getDBKey();

		// to build our permalink, use the feedback entry key + the page name (isn't page name a title? but title is an object? confusing)
		$permalink = SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$page_name/$itemId" );

		// Make sure our notes are not too long - we won't error, just hard substr it
		global $wgArticleFeedbackv5MaxActivityNoteLength, $wgLang;

		$notes = $wgLang->truncate( $notes, $wgArticleFeedbackv5MaxActivityNoteLength );

		// if this is an automatic action, we create our special extension doer and send
		if ( $doer ) {
			if ( $doer > 0) {
				$default_user = wfMessage( 'articlefeedbackv5-default-user' )->text();
				$doer = User::newFromName( $default_user );
			} else {
				$doer = User::newFromId( $doer );
			}
			// I cannot see how this could fail, but if it does do not log
			if ( !$doer ) {
				return;
			}
		} else {
			$doer = null;
		}

		$log = new LogPage( $logtype, false );
		// comments become the notes section from the feedback
		$log->addEntry( $type, $permalink, $notes, $params, $doer );

		// update our log count by 1
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();

		$dbw->update(
			'aft_article_feedback',
			array( $increment .' = ' .$increment . ' + 1' ),
			array(
				'af_id' => $itemId
			),
			__METHOD__
		);

		$dbw->commit();
	}

	/**
	 * Creates a user link for a log row
	 *
	 * @param int $user_id can be null or a user object
	 * @param string $user_ip (name works too)
	 * @return anchor tag link to user
	 */
	public static function getUserLink( $user_id, $user_ip = null ) {
		// if $user is not an object
		if ( !( $user_id instanceof User ) ) {
			$userId = (int) $user_id;
			if ( $userId !== 0 ) { // logged-in users
				$user = User::newFromId( $userId );
			} elseif ( is_null($user_ip) || $userId == 0 ) { // magic user
				global $wgArticleFeedbackv5AutoHelp;
				$element = Linker::makeExternalLink(
					$wgArticleFeedbackv5AutoHelp,
					wfMessage( 'articlefeedbackv5-default-user' )->text());
					return $element;
			} else { // IP users
				$userText = $user_ip;
				$user = User::newFromName( $userText, false );
			}
		} else {
			// user is an object, all good, make link
			$user = $user_id;
		}

		$element = Linker::userLink(
				$user->getId(),
				$user->getName()
			);
		return $element;
	}

	/**
	 * Helper function to create a new red status line based on the last status line created
	 * action performed
	 */
	public static function renderStatusLine( $status, $user_id, $timestamp ) {
		global $wgLang;
		return Html::rawElement( 'span', array(
			'class' => 'articleFeedbackv5-feedback-status-marker ' .
				'articleFeedbackv5-laststatus-' . $status
			),
			wfMessage( 'articlefeedbackv5-status-' . $status )
				->rawParams( ApiArticleFeedbackv5Utils::getUserLink( $user_id ) )
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
	 * Helper function to create an "X ago" timestamp from a date
	 *
	 * @param  $timestamp string the timestamp, from the db
	 * @return string     the x-ago timestamp string
	 */
	public static function renderTimeAgo( $timestamp ) {
		global $wgLang;

		$blocks = array(
			array( 'total' => 60 * 60 * 24 * 365, 'name' => 'years' ),
			array( 'total' => 60 * 60 * 24 * 30, 'name' => 'months' ),
			array( 'total' => 60 * 60 * 24 * 7, 'name' => 'weeks' ),
			array( 'total' => 60 * 60 * 24, 'name' => 'days' ),
			array( 'total' => 60 * 60, 'name' => 'hours' ),
			array( 'total' => 60, 'name' => 'minutes' ) );

		$since = wfTimestamp( TS_UNIX ) - wfTimestamp( TS_UNIX, $timestamp );
		$displayTime = 0;
		$displayBlock = '';

		// get the largest time block, 1 minute 35 seconds -> 2 minutes
		for ( $i = 0, $count = count( $blocks ); $i < $count; $i++ ) {
			$seconds = $blocks[$i]['total'];
			$displayTime = floor( $since / $seconds );

			if ( $displayTime > 0 ) {
				$displayBlock = $blocks[$i]['name'];
				// round up if the remaining time is greater than
				// half of the time unit
				if ( ( $since % $seconds ) >= ( $seconds / 2 ) ) {
					$displayTime++;

					// advance to upper unit if possible, eg, 24 hours to 1 day
					if ( isset( $blocks[$i -1] ) && $displayTime * $seconds ==  $blocks[$i -1]['total'] ) {
						$displayTime = 1;
						$displayBlock = $blocks[$i -1]['name'];
					}
				}
				break;
			}
		}

		$date = '';
		if ( $displayTime > 0 ) {
			if ( in_array( $displayBlock, array( 'years', 'months', 'weeks' ) ) ) {
				$messageKey = 'articlefeedbackv5-timestamp-' . $displayBlock;
			} else {
				$messageKey = $displayBlock;
			}
			$date = wfMessage( $messageKey )->params( $wgLang->formatNum( $displayTime ) )->escaped();
		} else {
			$date = wfMessage( 'articlefeedbackv5-timestamp-seconds' )->escaped();
		}

		return $date;
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
		global $wgLang;
		if ( (int) $user_id !== 0 ) { // logged-in users
			$username = User::newFromId( $user_id )->getName();
		} else { // magic user
			$username = wfMessage( 'articlefeedbackv5-default-user' )->text();
		}
		return wfMessage( 'articlefeedbackv5-mask-text-' . $type )
			->numParams( $post_id )
			->params( $username )
			->rawParams( ApiArticleFeedbackv5Utils::renderTimeAgo( $timestamp ) )
			->escaped();
	}

}

