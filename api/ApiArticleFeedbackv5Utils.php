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
	 * Returns whether feedback is enabled for this page.
	 * See jquery.articleFeedbackv5.verify.js for full implementation;
	 * this is more of a safety check.
	 *
	 * @param $pageId int the page id
	 * @return bool
	 */
	public static function isFeedbackEnabled( $pageId ) {
		global $wgArticleFeedbackv5Namespaces,
				$wgArticleFeedbackv5BlacklistCategories,
				$wgArticleFeedbackv5Categories,
				$wgArticleFeedbackv5LotteryOdds,
				$wgUser;

		$title = Title::newFromID( $pageId );
		if ( is_null( $title ) ) {
			return false;
		}

		$categories = array();
		foreach ( $title->getParentCategories() as $category => $page ) {
			// get category title without prefix
			$category = Title::newFromDBkey( $category );
			if ( $category ) {
				$category = $category->getDBkey();
				$categories[] = str_replace( ' ', '_', $category );
				$categories[] = str_replace( '_', ' ', $category );
			}
		}

		$odds = $wgArticleFeedbackv5LotteryOdds;
		if ( gettype( $odds ) === 'object' && isset( $odds->{$title->getNamespace()} ) ) {
			$odds = $odds->{$title->getNamespace()};
		}

		$enable = true;

		// only on pages in namespaces where it is enabled
		$enable &= in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces );

		// check if user has the required permissions
		$enable &= $wgUser->isAllowed( ArticleFeedbackv5Permissions::getRestriction( $title->getArticleID() )->pr_level ) && !$wgUser->isBlocked();

		// category is not blacklisted
		$enable &= !array_intersect( $categories, $wgArticleFeedbackv5BlacklistCategories );

		// category is whitelisted or article is in lottery
		$enable &=
			array_intersect( $categories, $wgArticleFeedbackv5Categories ) ||
			(int) $pageId % 1000 >= 1000 - ( (int) $odds * 10 );

		// not disabled via preferences
		$enable &= !$wgUser->getOption( 'articlefeedback-disable' );

		// not viewing a redirect
		$enable &= !$title->isRedirect();

		return $enable;
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
		return
			Html::rawElement(
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
	 * @param string $type the type (hidden or oversight)
	 * @param int $post_id the feedback post id
	 * @param int $user_id the user id
	 * @param string[optional] $timestamp the timestamp, from the db
	 * @return string the mask line
	 */
	public static function renderMaskLine( $type, $post_id, $user_id, $timestamp = null ) {
		if ( (int) $user_id !== 0 ) { // logged-in users
			$username = User::newFromId( $user_id )->getName();
		} else { // magic user
			$username = wfMessage( 'articlefeedbackv5-default-user' )->text();
		}
		$timestamp = new MWTimestamp( $timestamp );

		return wfMessage( 'articlefeedbackv5-mask-text-' . $type )
			->params( $post_id, $username )
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

	/**
	 * Because fetching the amount of activity from db is quite expensive, this
	 * method will just increment the data that is in cache already (instead of
	 * purging the cache data to have it re-read from DB, which should be last-resort)
	 *
	 * @param int $feedbackId
	 * @param string $action
	 */
	public static function incrementActivityCount( $feedbackId, $action ) {
		global $wgMemc;

		// get permission level that should be updated
		$permission = ArticleFeedbackv5Model::$actions[$action]['permissions'];

		$key = wfMemcKey( 'articlefeedbackv5', 'getActivityCount', $permission, $feedbackId );
		$count = $wgMemc->get( $key );

		/*
		 * if the data is not (yet) in cache, don't bother fetching it from db yet,
		 * that'll happen in due time, when it's actually requested
		 */
		if ( $count !== false ) {
			$wgMemc->set( $key, $count + 1 );
		}
	}

	/**
	 * Get the amount of activity (that is within the user's permissions) that has
	 * been posted already
	 *
	 * @param ArticleFeedbackv5Model $feedback
	 * @param User[optional] $user
	 * @return int
	 */
	public static function getActivityCount( ArticleFeedbackv5Model $feedback, User $user = null ) {
		global $wgArticleFeedbackv5Permissions, $wgMemc;
		$total = 0;

		if ( !$user ) {
			global $wgUser;
			$user = $wgUser;
		}

		foreach( $wgArticleFeedbackv5Permissions as $permission ) {
			if ( $user->isAllowed( $permission ) && !$wgUser->isBlocked() ) {
				// get count for this specific permission level from cache
				$key = wfMemcKey( 'articlefeedbackv5', 'getActivityCount', $permission, $feedback->id );
				$count = $wgMemc->get( $key );

				// unavailable in cache, get from db & save to cache
				if ( $count == false ) {
					$count = self::getActivityCountFromDB( $feedback, $permission );
					$wgMemc->set( $key, $count );
				}

				$total += $count;
			}
		}

		return $total;
	}

	/**
	 * Get amount of activity for a certain feedback post for a certain permission level.
	 * This should not be called directly, as it's a relatively expensive call; the result
	 * should be cached (@see self::getActivityCount)
	 *
	 * @param ArticleFeedbackv5Model $feedback
	 * @param string $permission
	 * @return int
	 * @throws MWException
	 */
	private static function getActivityCountFromDB( ArticleFeedbackv5Model $feedback, $permission ) {
		global $wgLogActionsHandlers;
		$dbr = wfGetDB( DB_SLAVE );

		$feedbackId = $feedback->id;
		$page = Title::newFromID( $feedback->page );
		if ( !$page ) {
			throw new MWException( 'Page for feedback does not exist', 'invalidfeedbackid' );
		}
		$title = $page->getDBKey();

		// get action-specific where-clause for requested permission level
		$actions = array();
		foreach( ArticleFeedbackv5Model::$actions as $action => $options ) {
			if ( $options['permissions'] == $permission ) {
				if ( isset( $wgLogActionsHandlers["suppress/$action"] ) ) {
					$type = 'suppress';
				} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$action"] ) ) {
					$type = 'articlefeedbackv5';
				} else {
					continue;
				}
				$actions[] = 'log_type = '.$dbr->addQuotes( $type ).' AND log_action = '.$dbr->addQuotes( $action );
			}
		}

		if ( !$actions ) {
			return 0;
		}

		$where[] = implode( ' OR ', $actions );
		$where['log_namespace'] = NS_SPECIAL;
		$where['log_title'] = "ArticleFeedbackv5/$title/$feedbackId";

		return (int) $dbr->selectField(
			'logging',
			'COUNT(log_id)',
			$where,
			__METHOD__
		);
	}
}
