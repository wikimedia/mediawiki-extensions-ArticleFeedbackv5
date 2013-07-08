<?php
/**
 * Permissions for ArticleFeedback
 *
 * @file
 * @ingroup Extensions
 */

class ArticleFeedbackv5Permissions {
	/**
	 * The AFT permission levels
	 *
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Access_and_permissions
	 * @var array
	 */
	public static $permissions = array(
		/*
		 * When adding a new first permission level, also update
		 * $.aftUtils.getDefaultPermissionLevel, which resembles
		 * self::getDefaultPermissionLevel
		 */
		'aft-reader', // default "enable" level
		'aft-member',
		'aft-editor', // level when disabled by editor
		'aft-monitor',
		'aft-administrator',
		'aft-oversighter',
		'aft-noone', // default "disable" level
		/*
		 * When adding a new last permission level, also update
		 * $.aftUtils.getDefaultPermissionLevel, which resembles
		 * self::getDefaultPermissionLevel
		 */
	);

	/**
	 * The current permission level(s) & expiry for a page
	 *
	 * @var array
	 */
	protected static $current = array();

	/**
	 * A page's default permission level is lottery-based. Lottery is a
	 * percentage, 0-100, of articles where AFTv5 is enabled by default.
	 * This will return a boolean true for articles that "win" the lottery, and
	 * false for others (based on the last digits of a page id).
	 *
	 * This is equivalent to $.aftUtils.lottery
	 *
	 * @param int $articleId
	 * @return bool
	 */
	public static function getLottery( $articleId ) {
		$title = Title::newFromID( $articleId );
		if ( is_null( $title ) ) {
			return false;
		}

		global $wgArticleFeedbackv5LotteryOdds;

		$odds = $wgArticleFeedbackv5LotteryOdds;
		if ( is_array( $odds ) ) {
			if ( isset( $odds[$title->getNamespace()] ) ) {
				$odds = $odds[$title->getNamespace()];
			} else {
				$odds = 0;
			}
		}

		return (int) $articleId % 1000 >= 1000 - ( (float) $odds * 10 );
	}

	/**
	 * Depending on whether or not an article "wins" the lottery, returns the
	 * appropriate default permission level (enable = most permissive,
	 * disable = least permissive).
	 *
	 * This is equivalent to $.aftUtils.getDefaultPermissionLevel
	 *
	 * @param int $articleId
	 * @return string
	 */
	public static function getDefaultPermissionLevel( $articleId ) {
		$enable = self::getLottery( $articleId );
		return $enable ? reset( self::$permissions ) : end( self::$permissions );
	}

	/**
	 * Validate a permission level
	 *
	 * @param $permission
	 * @return bool
	 */
	public static function isValidPermission( $permission ) {
		return in_array( $permission, self::$permissions );
	}

	/**
	 * Get the AFT restriction level linked to a page.
	 *
	 * This will return the restrictions selected for a page via ?action=protect,
	 * or false if none are currently set (= not yet set or expired)
	 * If you're looking for the final restriction level applied to the page,
	 * call getAppliedRestriction(), which will return the default permission
	 * level in case none are set via ?action=protect.
	 *
	 * @param int $articleId
	 * @return ResultWrapper|false false if not restricted or details of restriction set
	 */
	public static function getProtectionRestriction( $articleId ) {
		// check if opt-in/-out is enabled
		global $wgArticleFeedbackv5EnableProtection;
		if ( !$wgArticleFeedbackv5EnableProtection ) {
			return false;
		}

		if ( isset( self::$current[$articleId] ) ) {
			return self::$current[$articleId];
		}

		$dbr = wfGetDB( DB_SLAVE );

		$restriction = $dbr->selectRow(
			'page_restrictions',
			array( 'pr_level', 'pr_expiry' ),
			array(
				'pr_page' => $articleId,
				'pr_type' => 'aft',
				'pr_expiry = "infinity" OR pr_expiry >= ' . $dbr->addQuotes( $dbr->encodeExpiry( wfTimestampNow() ) )
			),
			__METHOD__
		);

		// check if valid result
		if ( !isset( $restriction->pr_level ) || !self::isValidPermission( $restriction->pr_level ) ) {
			$restriction = false;
		}

		self::$current[$articleId] = $restriction;
		return $restriction;
	}

	/**
	 * Get the AFT restriction level linked to a page.
	 *
	 * This will return the final restriction level applied to a page.
	 * If you're looking for the restrictions selected for a page via
	 * ?action=protect, call getProtectionRestriction(), which will only return
	 * those, or false if none are currently set (= not yet set or expired)
	 *
	 * @param int $articleId
	 * @return ResultWrapper The restriction currently applied to the page
	 */
	public static function getAppliedRestriction( $articleId ) {
		$restriction = self::getProtectionRestriction( $articleId );

		if ( $restriction === false ) {
			$restriction = (object) array(
				'pr_level' => self::getDefaultPermissionLevel( $articleId ),
				'pr_expiry' => wfGetDB( DB_SLAVE )->getInfinity()
			);
		}

		return $restriction;
	}

	/**
	 * Set the AFT restriction level linked to a page
	 *
	 * This will be (ab)using the existing page_restrictions table because:
	 * - it's basically a page restriction
	 * - core code won't mind us playing around there, it only touches $wgRestrictionTypes types
	 *
	 * @param int $articleId
	 * @param string $permission
	 * @param string $expiry
	 * @return bool
	 */
	public static function setRestriction( $articleId, $permission, $expiry ) {
		// check if opt-in/-out is enabled
		global $wgArticleFeedbackv5EnableProtection;
		if ( !$wgArticleFeedbackv5EnableProtection ) {
			return false;
		}

		// make sure an articleId was passed
		if ( !$articleId ) {
			return false;
		}

		// check if valid permission
		if ( !self::isValidPermission( $permission ) ) {
			return false;
		}

		$dbw = wfGetDB( DB_MASTER );
		$dbr = wfGetDB( DB_SLAVE );

		$record = $dbr->selectField(
			'page_restrictions',
			array( 'pr_page', 'pr_type' ),
			array(
				'pr_page' => $articleId,
				'pr_type' => 'aft'
			)
		);

		// insert new restriction entry
		$vars = array(
			'pr_page' => $articleId,
			'pr_type' => 'aft',
			'pr_level' => $permission,
			'pr_cascade' => 0,
			'pr_expiry' => $dbw->encodeExpiry( $expiry )
		);

		if ( $record ) {
			$dbw->update(
				'page_restrictions',
				$vars,
				array(
					'pr_page' => $articleId,
					'pr_type' => 'aft'
				)
			);
		} else {
			$dbw->insert(
				'page_restrictions',
				$vars
			);
		}

		return true;
	}

	/**
	 * Get expiry values to build the form
	 *
	 * @param int $articleId
	 * @return array
	 */
	public static function getExpiry( $articleId ) {
		global $wgRequest;

		$requestExpiry = $wgRequest->getText( 'articlefeedbackv5-protection-expiration' );
		$requestExpirySelection = $wgRequest->getVal( 'articlefeedbackv5-protection-expiration-selection' );
		$existingExpiry = self::getAppliedRestriction( $articleId )->pr_expiry;

		if ( $requestExpiry ) {
			// Custom expiry takes precedence
			$mExpiry = $requestExpiry;
			$mExpirySelection = 'othertime';
		} elseif ( $requestExpirySelection ) {
			// Expiry selected from list
			$mExpiry = '';
			$mExpirySelection = $requestExpirySelection;
		} else {
			// Existing expiry is infinite, use "infinite" in drop-down
			$mExpiry = '';
			$mExpirySelection = 'infinite';
		}

		return array( $existingExpiry, $mExpiry, $mExpirySelection );
	}
}
