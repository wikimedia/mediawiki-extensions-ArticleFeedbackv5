<?php
/**
 * ArticleFeedbackv5Utils class
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
class ArticleFeedbackv5Utils {
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
		if ( is_array( $odds ) && array_key_exists( $title->getNamespace(), $odds ) ) {
			$odds = $odds[$title->getNamespace()];
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
		if ( $wgUser->getId() && isset( $record->aft_user ) && $wgUser->getId() == intval( $record->aft_user ) ) {
			return true;
		}

		// if either the feedback was posted when not logged in, or the visitor is now not
		// logged in, compare the feedback's id with what's stored in a cookie
		$version = isset( $wgArticleFeedbackv5Tracking['version'] ) ? $wgArticleFeedbackv5Tracking['version'] : 0;
		$cookie = json_decode( $wgRequest->getCookie( 'feedback-ids', 'ext_articleFeedbackv5@' . $version . '-' ), true );
		if ( $cookie !== null && is_array( $cookie ) && isset( $record->aft_id ) ) {
			return in_array( $record->aft_id, $cookie );
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
	 * Returns the percentage helpful, given a helpful count and an unhelpful count
	 *
	 * @param  $helpful   int the number of helpful votes
	 * @param  $unhelpful int the number of unhelpful votes
	 * @return int        the percentage
	 */
	public static function percentHelpful( $helpful, $unhelpful ) {
		if ( $helpful + $unhelpful > 0 ) {
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
			global $wgUser, $wgArticleFeedbackv5AbuseFilterGroup;

			// Add custom action handlers
			if ( $callback && is_callable( $callback ) ) {
				global $wgAbuseFilterCustomActionsHandlers;

				$wgAbuseFilterCustomActionsHandlers['aftv5flagabuse'] = $callback;
				$wgAbuseFilterCustomActionsHandlers['aftv5hide'] = $callback;
				$wgAbuseFilterCustomActionsHandlers['aftv5request'] = $callback;
			}

			// Set up variables
			$title = Title::newFromID( $pageId );
			$vars = new AbuseFilterVariableHolder;
			$vars->addHolder( AbuseFilter::generateUserVars( $wgUser ) );
			$vars->addHolder( AbuseFilter::generateTitleVars( $title , 'ARTICLE' ) );
			$vars->setVar( 'SUMMARY', 'Article Feedback 5' );
			$vars->setVar( 'ACTION', 'feedback' );
			$vars->setVar( 'new_wikitext', $value );
			$vars->setLazyLoadVar( 'new_size', 'length', array( 'length-var' => 'new_wikitext' ) );

			$status = AbuseFilter::filterAction( $vars, $title, $wgArticleFeedbackv5AbuseFilterGroup );

			return $status->isOK() ? false : $status->getErrorsArray();
		}

		return false;
	}
}
