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
		'aft-reader', // default "enable" level
		'aft-member',
		'aft-editor', // level when disabled by editor
		'aft-monitor',
		'aft-administrator',
		'aft-oversighter',
		'aft-noone', // default "disable" level
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
	 * @param int $articleId
	 * @return string
	 */
	public static function getDefaultPermissionLevel( $articleId ) {
		$enable = self::getLottery( $articleId );
		return $enable ? self::$permissions[0] : self::$permissions[count( self::$permissions ) - 1];
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
	 * Get the AFT restriction level linked to a page
	 *
	 * @param int $articleId
	 * @return object|false false if not restricted or details of restriction set
	 */
	public static function getRestriction( $articleId ) {
		if ( isset( self::$current[$articleId] ) ) {
			return self::$current[$articleId];
		}

		$dbr = wfGetDB( DB_SLAVE );

		$permission = $dbr->selectRow(
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
		if ( !$permission || !isset( $permission->pr_level ) || !self::isValidPermission( $permission->pr_level ) ) {
			return false;
		}

		self::$current[$articleId] = $permission;
		return $permission;
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
	 * @param string[optional] $reason
	 * @return bool
	 */
	public static function setRestriction( $articleId, $permission, $expiry, $reason = '' ) {
		// check if valid permission
		if ( !self::isValidPermission( $permission ) ) {
			return false;
		}

		global $wgUser;

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

		if ( $dbw->affectedRows() > 0 ) {
			$page = WikiPage::newFromID( $articleId );
			if ( $page ) {
				// make sure timestamp doesn't overlap with protection log's null revision (if any)
				$timestamp = Revision::getTimestampFromId( $page->getTitle(), $page->getLatest() );
				if ( $timestamp === wfTimestampNow() ) {
					sleep( 1 );
				}

				$page->insertNullRevision(
					'articlefeedbackv5-protection-title',
					array( 'articlefeedbackv5' => $permission ),
					array( 'articlefeedbackv5' => $expiry ),
					false,
					$reason,
					$wgUser
				);

				// insert into log
				$logEntry = new ManualLogEntry( 'articlefeedbackv5', 'protect' );
				$logEntry->setTarget( $page->getTitle() );
				$logEntry->setPerformer( $wgUser );
				$logEntry->setParameters( array( 'permission' => $permission, 'expiry' => $expiry ) );
				$logEntry->setComment( $reason );
				$logId = $logEntry->insert();
				$logEntry->publish( $logId );
			}
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

		$existingRestriction = self::getRestriction( $articleId );

		$requestExpiry = $wgRequest->getText( 'articlefeedbackv5-protection-expiration' );
		$requestExpirySelection = $wgRequest->getVal( 'articlefeedbackv5-protection-expiration-selection' );
		$existingExpiry = isset( $existingRestriction->pr_expiry ) ? $existingRestriction->pr_expiry : false;

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
