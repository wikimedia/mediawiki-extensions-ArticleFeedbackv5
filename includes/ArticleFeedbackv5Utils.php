<?php
/**
 * ArticleFeedbackv5Utils class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
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

use MediaWiki\Context\RequestContext;
use MediaWiki\Extension\AbuseFilter\AbuseFilterServices;
use MediaWiki\Extension\Notifications\Model\Event as EchoEvent;
use MediaWiki\Extension\SpamBlacklist\BaseBlacklist;
use MediaWiki\Linker\Linker;
use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\Title\Title;
use MediaWiki\WikiMap\WikiMap;

class ArticleFeedbackv5Utils {
	/**
	 * @var \Wikimedia\Rdbms\LoadBalancer[]
	 */
	protected static $lb = [];

	/**
	 * @var array<string,bool>
	 */
	public static $written = [];

	/**
	 * @param string|false $wiki The wiki ID, or false for the current wiki
	 * @return \Wikimedia\Rdbms\LoadBalancer
	 */
	public static function getLB( $wiki = false ) {
		if ( !isset( static::$lb[$wiki] ) ) {
			static::$lb[$wiki] = MediaWikiServices::getInstance()->getDBLoadBalancerFactory()->getMainLB( $wiki );
		}

		return static::$lb[$wiki];
	}

	/**
	 * Wrapper function for wfGetDB.
	 *
	 * @param int $db Index of the connection to get. May be DB_PRIMARY for the
	 *            primary database (for write queries), DB_REPLICA for potentially lagged read
	 *            queries, or an integer >= 0 for a particular server.
	 * @param string[]|string $groups Query groups. An array of group names that this query
	 *                belongs to. May contain a single string if the query is only
	 *                in one group.
	 * @param string|false $wiki The wiki ID, or false for the current wiki
	 * @return \Wikimedia\Rdbms\IDatabase
	 */
	public static function getDB( $db, $groups = [], $wiki = false ) {
		$lb = static::getLB( $wiki );

		$wikiId = ( $wiki === false ) ? WikiMap::getCurrentWikiId() : $wiki;

		if ( $db === DB_PRIMARY ) {
			// mark that we're writing data
			static::$written[$wikiId] = true;
		} elseif ( isset( static::$written[$wikiId] ) && static::$written[$wikiId] ) {
			if ( $db === DB_REPLICA ) {
				/*
				 * Let's keep querying primary database to make sure we have up-to-date
				 * data (waiting for replicas to sync up might take some time)
				 */
				$db = DB_PRIMARY;
			}
		}

		return $lb->getConnection( $db, $groups, $wiki );
	}

	/**
	 * Get the full, prefixed, name that data is saved at in cookie.
	 * The cookie name is prefixed by the extension name and a version number,
	 * to avoid collisions with other extensions or code versions.
	 *
	 * @param string $suffix
	 * @return string
	 */
	public static function getCookieName( $suffix ) {
		return 'AFTv5-' . $suffix;
	}

	/**
	 * Returns whether feedback is enabled for this page.
	 *
	 * This is equivalent to $.aftUtils.verify
	 * When changing conditions, make sure to change them there too.
	 *
	 * See jquery.articleFeedbackv5.utils.js for full implementation;
	 * this is more of a safety check.
	 *
	 * @param int $pageId The page ID
	 * @param User $user
	 * @return bool
	 */
	public static function isFeedbackEnabled( $pageId, User $user ) {
		global $wgArticleFeedbackv5Namespaces,
				$wgArticleFeedbackv5EnableProtection;

		$title = Title::newFromID( $pageId );
		if ( $title === null ) {
			return false;
		}

		$restriction = ArticleFeedbackv5Permissions::getProtectionRestriction( $title->getArticleID() );

		// only on pages in namespaces where it is enabled
		$enable = in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces );

		// check if user is not blocked
		$enable = $enable && !$user->getBlock();

		// check if a, to this user sufficient, permission level is defined
		if ( $wgArticleFeedbackv5EnableProtection && isset( $restriction->pr_level ) ) {
			$enable = $enable && $user->isAllowed( $restriction->pr_level );
		} else {
			$enable = ( $enable &&
				// check if a, to this user sufficient, default permission level (based on lottery) is defined
				$user->isAllowed( ArticleFeedbackv5Permissions::getDefaultPermissionLevel( $pageId ) ) ) ||
				// or check whitelist
				self::isWhitelisted( $pageId );
		}

		// category is not blacklisted
		$enable = $enable && !self::isBlacklisted( $pageId );

		// not disabled via preferences
		$enable = $enable && !MediaWikiServices::getInstance()
			->getUserOptionsLookup()->getOption( $user, 'articlefeedback-disable' );

		// not viewing a redirect
		$enable = $enable && !$title->isRedirect();

		return $enable;
	}

	/**
	 * Get an array of unprefixed categories linked to a page, in both
	 * underscored and spaced format (to make sure that no matter how they're
	 * defined in config, we'll find the correct match)
	 *
	 * @param int $pageId
	 * @return string[]
	 */
	protected static function getPageCategories( $pageId ) {
		$title = Title::newFromID( $pageId );
		if ( $title === null ) {
			return [];
		}

		$categories = [];
		foreach ( $title->getParentCategories() as $category => $page ) {
			// get category title without prefix
			$category = Title::newFromDBkey( $category );
			if ( $category ) {
				$category = $category->getDBkey();
				// make both underscored or spaces category names work
				$categories[] = str_replace( ' ', '_', $category );
				$categories[] = str_replace( '_', ' ', $category );
			}
		}

		return $categories;
	}

	/**
	 * Check if an article is whitelisted (by means of a whitelist category)
	 *
	 * This is equivalent to $.aftUtils.whitelist
	 *
	 * @param int $pageId
	 * @return bool
	 */
	public static function isWhitelisted( $pageId ) {
		global $wgArticleFeedbackv5Categories;

		$categories = self::getPageCategories( $pageId );

		return (bool)array_intersect( $categories, $wgArticleFeedbackv5Categories );
	}

	/**
	 * Check if an article is blacklisted (by means of a blacklist category)
	 *
	 * This is equivalent to $.aftUtils.blacklist
	 *
	 * @param int $pageId
	 * @return bool
	 */
	public static function isBlacklisted( $pageId ) {
		global $wgArticleFeedbackv5BlacklistCategories;

		$categories = self::getPageCategories( $pageId );

		return (bool)array_intersect( $categories, $wgArticleFeedbackv5BlacklistCategories );
	}

	/**
	 * Check if a certain feedback was posted by the current user.
	 *
	 * Additional $unsafe parameter can be used to determine if untrusted data
	 * (in this case a cookie, that could be manipulated) can be evaluated.
	 * This can come in useful to restrict anonymous users (who have no fixed
	 * user id) from evaluating their own feedback, but should never be trusted.
	 *
	 * @param ArticleFeedbackv5Model $record The feedback record
	 * @param MediaWiki\User\User $curUser
	 * @param MediaWiki\Request\WebRequest $request
	 * @param bool $unsafe True if untrusted data can be evaluated
	 * @return bool
	 */
	public static function isOwnFeedback(
		$record,
		$curUser,
		$request,
		$unsafe = false
	) {
		// if logged in user, we can know for certain if feedback was posted when logged in
		if ( $curUser->getId() && $record->aft_user !== null && $curUser->getId() == intval( $record->aft_user ) ) {
			return true;
		}

		if ( $unsafe ) {
			/*
			 * If either the feedback was posted when not logged in, or the visitor is now not
			 * logged in, compare the feedback's id with what's stored in a cookie.
			 */
			$cookie = $request->getCookie( self::getCookieName( 'feedback-ids' ) );
			$cookieArray = $cookie !== null ? json_decode( $cookie, true ) : null;
			if ( $cookieArray !== null && is_array( $cookieArray ) && $record->aft_id !== null ) {
				return in_array( $record->aft_id, $cookieArray );
			}
		}

		return false;
	}

	/**
	 * Creates a user link for a log row
	 *
	 * @param User|int|null $userId can be null or a user object
	 * @param string|null $userIp (name works too)
	 * @return string Anchor tag link to user
	 */
	public static function getUserLink( $userId, $userIp = null ) {
		if ( ( $userId instanceof User ) ) {
			// user is an object, all good, make link
			$user = $userId;
		} else {
			// if $userId is not an object
			$userId = (int)$userId;
			if ( $userId !== 0 ) { // logged-in users
				$user = User::newFromId( $userId );
			} elseif ( $userIp !== null ) { // IP users
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
	 * @param int $helpful The number of helpful votes
	 * @param int $unhelpful The number of unhelpful votes
	 * @return int The percentage
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
	 * @param string $feedbackId the feedback post id
	 * @param int $userId the user id
	 * @param string|null $timestamp the timestamp, from the db
	 * @return string the mask line
	 */
	public static function renderMaskLine( $type, $feedbackId, $userId, $timestamp = null ) {
		if ( (int)$userId !== 0 ) { // logged-in users
			$username = User::newFromId( $userId )->getName();
		} else { // magic user
			$username = wfMessage( 'articlefeedbackv5-default-user' )->text();
		}
		$timestamp = new MWTimestamp( $timestamp );

		// Give grep a chance to find the usages:
		// articlefeedbackv5-mask-text-oversight, articlefeedbackv5-mask-text-hide,
		// articlefeedbackv5-mask-text-inappropriate
		return wfMessage( 'articlefeedbackv5-mask-text-' . $type )
			->params( static::formatId( $feedbackId ), $username,
				RequestContext::getMain()->getLanguage()->getHumanTimestamp( $timestamp ) )
			->escaped();
	}

	/**
	 * Run comment through SpamRegex, both the $wg* global configuration variable
	 * and if installed, the anti-spam extension of the same name as well
	 *
	 * @param string $value
	 * @return bool Will return boolean false if valid or true if flagged
	 */
	public static function validateSpamRegex( $value ) {
		// Respect $wgSpamRegex
		global $wgSpamRegex;

		// Apparently this has to use the name SpamRegex specifies in its extension.json
		// rather than the shorter directory name...
		$spamRegexExtIsInstalled = ExtensionRegistry::getInstance()->isLoaded( 'Regular Expression Spam Block' );

		// If and only if the config var is neither an array nor a string nor
		// do we have the extension installed, bail out then and *only* then.
		// It's entirely possible to have the extension installed without
		// the config var being explicitly changed from the default value.
		if (
			!(
				( is_array( $wgSpamRegex ) && count( $wgSpamRegex ) > 0 ) ||
				( is_string( $wgSpamRegex ) && strlen( $wgSpamRegex ) > 0 )
			) &&
			!$spamRegexExtIsInstalled
		) {
			return false;
		}

		// In older versions, $wgSpamRegex may be a single string rather than
		// an array of regexes, so make it compatible.
		$regexes = (array)$wgSpamRegex;

		// Support [[mw:Extension:SpamRegex]] if it's installed (T347215)
		if ( $spamRegexExtIsInstalled ) {
			$phrases = SpamRegex::fetchRegexData( SpamRegex::TYPE_TEXTBOX );
			if ( $phrases && is_array( $phrases ) ) {
				$regexes = array_merge( $regexes, $phrases );
			}
		}

		foreach ( $regexes as $regex ) {
			if ( preg_match( $regex, $value ) ) {
				// $value contains spam
				return true;
			}
		}

		return false;
	}

	/**
	 * Run comment through SpamBlacklist
	 *
	 * @param string $value
	 * @param int $pageId
	 * @param User $user
	 * @return bool Will return boolean false if valid or true if flagged
	 */
	public static function validateSpamBlacklist( $value, $pageId, User $user ) {
		// Check SpamBlacklist, if installed
		if ( ExtensionRegistry::getInstance()->isLoaded( 'SpamBlacklist' ) ) {
			$spam = BaseBlacklist::getSpamBlacklist();
			$title = Title::newFromText( 'ArticleFeedbackv5_' . $pageId );

			$options = new ParserOptions( $user );
			$output = MediaWikiServices::getInstance()->getParser()->parse( $value, $title, $options );
			$links = array_keys( $output->getExternalLinks() );

			$ret = $spam->filter( $links, $title, $user );
			if ( $ret !== false ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Run comment through AbuseFilter extension
	 *
	 * @param string $value
	 * @param int $pageId
	 * @param MediaWiki\User\User $user
	 * @return array[]|false Will return boolean false if valid or error message array if flagged
	 */
	public static function validateAbuseFilter( $value, $pageId, $user ) {
		// Check AbuseFilter, if installed
		if ( ExtensionRegistry::getInstance()->isLoaded( 'Abuse Filter' ) ) {
			global $wgArticleFeedbackv5AbuseFilterGroup;

			// Set up variables
			$title = Title::newFromID( $pageId );
			if ( !$title ) {
				// XXX Can this happen?
				return false;
			}

			$vars = AbuseFilterServices::getVariableGeneratorFactory()
				->newGenerator()
				->addUserVars( $user )
				->addTitleVars( $title, 'page' )
				->addGenericVars()
				->getVariableHolder();
			$vars->setVar( 'summary', 'Article Feedback 5' );
			$vars->setVar( 'action', 'feedback' );
			$vars->setVar( 'new_wikitext', $value );
			$vars->setLazyLoadVar( 'new_size', 'length', [ 'length-var' => 'new_wikitext' ] );

			$runnerFactory = AbuseFilterServices::getFilterRunnerFactory();
			$runner = $runnerFactory->newRunner( $user, $title, $vars, $wgArticleFeedbackv5AbuseFilterGroup );
			$status = $runner->run();

			return $status->isOK() ? false : $status->getErrorsArray();
		}

		return false;
	}

	/**
	 * Pretty-format an id: the full 32 char length may be visually overwhelming
	 *
	 * @param string $id The full-length id
	 * @return string
	 */
	public static function formatId( $id ) {
		return MediaWikiServices::getInstance()->getContentLanguage()->truncateForVisual( $id, 10 );
	}

	/**
	 * Fire feedback-watch notification.
	 *
	 * @param ArticleFeedbackv5Model $feedback
	 * @param MediaWiki\User\User $agent
	 * @param string $flag
	 * @param int $logId
	 * @return bool
	 */
	public static function notifyWatch( ArticleFeedbackv5Model $feedback, $agent, $flag, $logId ) {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			return false;
		}

		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			return false;
		}

		EchoEvent::create( [
			'type' => 'feedback-watch',
			'title' => $page,
			'extra' => [
				'aft-id' => $feedback->aft_id,
				'aft-page' => $feedback->aft_page,
				'aft-comment' => $feedback->aft_comment,
				'aft-user' => $feedback->aft_user,
				'aft-moderation-flag' => $flag,
				'aft-moderation-log-id' => $logId,
			],
			'agent' => $agent,
		] );

		return true;
	}

	/**
	 * Fire feedback-moderated notification.
	 *
	 * @param ArticleFeedbackv5Model $feedback
	 * @param MediaWiki\User\User $agent
	 * @param string $flag
	 * @param int $logId
	 * @return bool
	 */
	public static function notifyModerated( ArticleFeedbackv5Model $feedback, $agent, $flag, $logId ) {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Echo' ) ) {
			return false;
		}

		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			return false;
		}

		EchoEvent::create( [
			'type' => 'feedback-moderated',
			'title' => $page,
			'extra' => [
				'aft-id' => $feedback->aft_id,
				'aft-page' => $feedback->aft_page,
				'aft-comment' => $feedback->aft_comment,
				'aft-user' => $feedback->aft_user,
				'aft-moderation-flag' => $flag,
				'aft-moderation-log-id' => $logId,
			],
			'agent' => $agent,
		] );

		return true;
	}
}
