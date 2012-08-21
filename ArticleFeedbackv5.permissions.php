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
	 * @return bool|object
	 */
	public static function getRestriction( $articleId ) {
		if ( array_key_exists( $articleId, self::$current ) ) {
			return self::$current[$articleId];
		}

		$dbr = wfGetDB( DB_SLAVE );

		$permission = $dbr->selectRow(
			'page_restrictions',
			array( 'pr_level', 'pr_expiry' ),
			array(
				'pr_page' => $articleId,
				'pr_type' => 'aft',
				'pr_expiry = "infinity" OR pr_expiry >= ' . $dbr->encodeExpiry( wfTimestamp( TS_MW, strtotime( 'now' ) ) )
			),
			__METHOD__
		);

		// check if valid result; if not, return defaults
		if ( !isset( $permission->pr_level ) || !self::isValidPermission( $permission->pr_level ) ) {
			$permission = (object) array( 'pr_level' => self::$permissions[0], 'pr_expiry' => null );
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
	 * @return bool|string
	 */
	public static function setRestriction( $articleId, $permission, $expiry ) {
		// check if valid permission
		if ( !self::isValidPermission( $permission ) ) {
			return false;
		}

		$dbw = wfGetDB( DB_MASTER );

		// delete existing entry
		$dbw->delete(
			'page_restrictions',
			array(
				'pr_page' => $articleId,
				'pr_type' => 'aft'
			)
		);

		// insert new restriction entry
		// exception: aft-reader is considered the default value and is equal to
		// when no record is in restrictions table - don't both adding it in then
		if ( $permission != self::$permissions[0] ) {
			$dbw->insert(
				'page_restrictions',
				array(
					'pr_page' => $articleId,
					'pr_type' => 'aft',
					'pr_level' => $permission,
					'pr_cascade' => 0,
					'pr_expiry' => $dbw->encodeExpiry( $expiry )
				),
				__METHOD__
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
	protected static function getExpiry( $articleId ) {
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
		} elseif ( $existingExpiry ) {
			// Use existing expiry in its own list item
			$mExpiry = '';
			$mExpirySelection = $existingExpiry;
		} else {
			// Final default: infinite
			$mExpiry = '';
			$mExpirySelection = 'infinite';
		}

		return array( $existingExpiry, $mExpiry, $mExpirySelection );
	}

	/**
	 * Add an AFT entry to article's protection levels
	 *
	 * Basically, all this code will do the same as adding a value to $wgRestrictionTypes
	 * However, that would use the same permission types as the other entries, whereas the
	 * AFT permission levels should be different.
	 *
	 * Parts of code are heavily "inspired" by ProtectionForm.
	 *
	 * @param Page $article
	 * @param $output
	 * @return bool
	 */
	public static function onProtectionForm( Page $article, &$output ) {
		global $wgLang;

		$articleId = $article->getTitle()->getArticleId();

		// on a per-page basis, AFT can only be restricted from these levels
		$levels = array(
			'aft-reader' => 'articlefeedbackv5-protection-permission-reader',
			'aft-member' => 'articlefeedbackv5-protection-permission-member',
			'aft-editor' => 'articlefeedbackv5-protection-permission-editor',
			'aft-administrator' => 'articlefeedbackv5-protection-permission-administrator'
		);

		// build permissions dropdown
		$existingPermissions = self::getRestriction( $articleId )->pr_level;
		$id = 'articlefeedbackv5-protection-level';
		$attribs = array(
			'id' => $id,
			'name' => $id,
			'size' => count( $levels )
		);
		$permissionsDropdown = Xml::openElement( 'select', $attribs );
		foreach( $levels as $key => $label ) {
			// possible labels: articlefeedbackv5-protection-permission-(all|reader|editor)
			$permissionsDropdown .= Xml::option( wfMessage( $label )->escaped(), $key, $key == $existingPermissions );
		}
		$permissionsDropdown .= Xml::closeElement( 'select' );

		$scExpiryOptions = wfMessage( 'protect-expiry-options' )->inContentLanguage()->text();
		$showProtectOptions = ( $scExpiryOptions !== '-' );

		list(
			$mExistingExpiry,
			$mExpiry,
			$mExpirySelection
		) = self::getExpiry( $articleId );

		if( $showProtectOptions ) {
			$expiryFormOptions = '';

			// add option to re-use existing expiry
			if ( $mExistingExpiry && $mExistingExpiry != 'infinity' ) {
				$timestamp = $wgLang->timeanddate( $mExistingExpiry, true );
				$d = $wgLang->date( $mExistingExpiry, true );
				$t = $wgLang->time( $mExistingExpiry, true );
				$expiryFormOptions .=
					Xml::option(
						wfMessage( 'protect-existing-expiry', $timestamp, $d, $t )->escaped(),
						'existing',
						$mExpirySelection == 'existing'
					);
			}

			// add regular expiry options
			$expiryFormOptions .= Xml::option( wfMessage( 'protect-othertime-op' )->escaped(), 'othertime' );
			foreach( explode( ',', $scExpiryOptions ) as $option ) {
				if ( strpos( $option, ':' ) === false ) {
					$show = $value = $option;
				} else {
					list( $show, $value ) = explode( ':', $option );
				}

				$expiryFormOptions .= Xml::option(
					htmlspecialchars( $show ),
					htmlspecialchars( $value ),
					$mExpirySelection == $value
				);
			}

			// build expiry dropdown
			$protectExpiry = Xml::tags( 'select',
				array(
					'id' => 'articlefeedbackv5-protection-expiration-selection',
					'name' => 'articlefeedbackv5-protection-expiration-selection',
					// when selecting anything other than "othertime", clear the input field for other time
					'onchange' => 'javascript:if ( $( this ).val() != "othertime" ) $( "#articlefeedbackv5-protection-expiration" ).val( "" );',
				),
				$expiryFormOptions );
			$mProtectExpiry = Xml::label( wfMessage( 'protectexpiry' )->escaped(), 'mwProtectExpirySelection-aft' );
		}

		// build custom expiry field
		$attribs = array(
			'id' => 'articlefeedbackv5-protection-expiration',
			// when entering an other time, make sure "othertime" is selected in the dropdown
			'onkeyup' => 'javascript:if ( $( this ).val() ) $( "#articlefeedbackv5-protection-expiration-selection" ).val( "othertime" );',
			'onchange' => 'javascript:if ( $( this ).val() ) $( "#articlefeedbackv5-protection-expiration-selection" ).val( "othertime" );'
		);

		$protectOther = Xml::input( 'articlefeedbackv5-protection-expiration', 50, $mExpiry, $attribs );
		$mProtectOther = Xml::label( wfMessage( 'protect-othertime' )->escaped(), "mwProtect-aft-expires" );

		// build output
		$output .= "
				<tr>
					<td>".
						Xml::openElement( 'fieldset' ) .
							Xml::element( 'legend', null, wfMessage( 'articlefeedbackv5-protection-level' )->text() ) .
							Xml::openElement( 'table', array( 'id' => 'mw-protect-table-aft' ) ) . "
								<tr>
									<td>$permissionsDropdown</td>
								</tr>
								<tr>
									<td>";

		if( $showProtectOptions ) {
			$output .= "				<table>
											<tr>
												<td class='mw-label'>$mProtectExpiry</td>
												<td class='mw-input'>$protectExpiry</td>
											</tr>
										</table>";
		}

		$output .= "					<table>
											<tr>
												<td class='mw-label'>$mProtectOther</td>
												<td class='mw-input'>$protectOther</td>
											</tr>
										</table>
									</td>
								</tr>" .
							Xml::closeElement( 'table' ) .
						Xml::closeElement( 'fieldset' ) . "
					</td>
				</tr>";

		return true;
	}

	/**
	 * Write AFT's article's protection levels to DB
	 *
	 * Parts of code are heavily "inspired" by ProtectionForm.
	 *
	 * @param Page $article
	 * @param string $errorMsg
	 * @return bool
	 */
	public static function onProtectionSave( Page $article, &$errorMsg ) {
		global $wgRequest;

		$requestPermission = $wgRequest->getVal( 'articlefeedbackv5-protection-level' );
		$requestExpiry = $wgRequest->getText( 'articlefeedbackv5-protection-expiration' );
		$requestExpirySelection = $wgRequest->getVal( 'articlefeedbackv5-protection-expiration-selection' );

		// fetch permissions set to edit page ans make sure that AFT permissions are no tighter than these
		$editPermission = $article->getTitle()->getRestrictions( 'edit' );
		if ( $editPermission ) {
			$availablePermissions = User::getGroupPermissions( $editPermission );
			if ( !in_array( $requestPermission, $availablePermissions ) ) {
				$errorMsg .= wfMessage( 'articlefeedbackv5-protection-level-error' )->escaped();
				return false;
			}
		}

		if ( $requestExpirySelection == 'existing' ) {
			$expirationTime = self::getRestriction( $article->getTitle()->getArticleId() )->pr_expiry;
		} else {
			if ( $requestExpirySelection == 'othertime' ) {
				$value = $requestExpiry;
			} else {
				$value = $requestExpirySelection;
			}

			if ( $value == 'infinite' || $value == 'indefinite' || $value == 'infinity' ) {
				$expirationTime = wfGetDB( DB_SLAVE )->getInfinity();
			} else {
				$unix = strtotime( $value );

				if ( !$unix || $unix === -1 ) {
					$expirationTime = false;
				} else {
					// @todo FIXME: Non-qualified absolute times are not in users specified timezone
					// and there isn't notice about it in the ui
					$expirationTime = wfTimestamp( TS_MW, $unix );
				}
			}
		}

		self::setRestriction(
			$article->getTitle()->getArticleID(),
			$requestPermission,
			$expirationTime
		);

		return true;
	}
}
