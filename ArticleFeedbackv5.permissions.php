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
		'aft-reader',
		'aft-member',
		'aft-editor',
		'aft-monitor',
		'aft-administrator',
		'aft-oversighter'
	);

	/**
	 * The current permission level(s) & expiry for a page
	 *
	 * @var array
	 */
	protected static $current = array();

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
	 * @return object
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
				'pr_expiry = "infinity" OR pr_expiry >= ' . $dbr->addQuotes( $dbr->encodeExpiry( wfTimestamp( TS_MW, strtotime( 'now' ) ) ) )
			),
			__METHOD__
		);

		// check if valid result; if not, return defaults
		if ( !isset( $permission->pr_level ) || !self::isValidPermission( $permission->pr_level ) ) {
			$permission = (object) array( 'pr_level' => self::$permissions[0], 'pr_expiry' => 'infinity' );
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
	 * @return bool
	 */
	public static function setRestriction( $articleId, $permission, $expiry ) {
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
		if ( $permission != self::$permissions[0] ) {
			if ( $record ) {
				$dbw->update(
					'page_restrictions',
					array(
						'pr_page' => $articleId,
						'pr_type' => 'aft',
						'pr_level' => $permission,
						'pr_cascade' => 0,
						'pr_expiry' => $dbw->encodeExpiry( $expiry )
					),
					array(
						'pr_page' => $articleId,
						'pr_type' => 'aft'
					)
				);
			} else {
				$dbw->insert(
					'page_restrictions',
					array(
						'pr_page' => $articleId,
						'pr_type' => 'aft',
						'pr_level' => $permission,
						'pr_cascade' => 0,
						'pr_expiry' => $dbw->encodeExpiry( $expiry )
					)
				);
			}
		} else {
			// exception: aft-reader is considered the default value and is equal to
			// when no record is in restrictions table - don't both adding it in then
			if ( $record ) {
				$dbw->delete(
					'page_restrictions',
					array(
						'pr_page' => $articleId,
						'pr_type' => 'aft'
					)
				);

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

		$requestExpiry = $wgRequest->getText( 'articlefeedbackv5-protection-expiration' );
		$requestExpirySelection = $wgRequest->getVal( 'articlefeedbackv5-protection-expiration-selection' );
		$existingExpiry = self::getRestriction( $articleId )->pr_expiry;

		if ( $requestExpiry ) {
			// Custom expiry takes precedence
			$mExpiry = $requestExpiry;
			$mExpirySelection = 'othertime';
		} elseif ( $requestExpirySelection ) {
			// Expiry selected from list
			$mExpiry = '';
			$mExpirySelection = $requestExpirySelection;
		} elseif ( $existingExpiry == 'infinity' ) {
			// Existing expiry is infinite, use "infinite" in drop-down
			$mExpiry = '';
			$mExpirySelection = 'infinite';
		} else {
			// Use existing expiry in its own list item
			$mExpiry = '';
			$mExpirySelection = $existingExpiry;
		}

		return array( $existingExpiry, $mExpiry, $mExpirySelection );
	}
}
