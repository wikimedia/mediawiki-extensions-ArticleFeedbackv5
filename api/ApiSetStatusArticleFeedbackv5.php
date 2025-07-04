<?php
/**
 * ApiSetStatusArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

use MediaWiki\Api\ApiBase;
use MediaWiki\MediaWikiServices;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * This class allows one to quickly enable/disable the AFTv5 form for a certain page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiSetStatusArticleFeedbackv5 extends ApiBase {
	/**
	 * @param MediaWiki\Api\ApiMain $query
	 * @param string $moduleName
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	public function execute() {
		$user = $this->getUser();
		$params = $this->extractRequestParams();

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdbmaster' );
		if ( !$pageObj->exists() ) {
			$this->dieWithError(
				'articlefeedbackv5-invalid-page-id',
				'notanarticle'
			);

		// check if current user has editor permission
		} elseif ( !$user->isAllowed( 'aft-editor' ) ) {
			$this->dieWithError(
				'articlefeedbackv5-insufficient-permissions',
				'nopermissions'
			);

		// check that no existing page restriction is set, or (if it is set),
		// check if it is not too tight (set tight by administrator, should not be overridden)
		} elseif (
			ArticleFeedbackv5Permissions::getProtectionRestriction( $pageObj->getId() ) !== false &&
			!$user->isAllowed( ArticleFeedbackv5Permissions::getAppliedRestriction( $pageObj->getId() )->pr_level )
		) {
			$this->dieWithError(
				'articlefeedbackv5-insufficient-permissions',
				'nopermissions'
			);

		} else {
			// enable: allow for all (= allow reader and up);
			// disable: disable for editor and below (= allow aft-administrator and up)
			$restriction = $params['enable'] ? 'aft-reader' : 'aft-editor';

			/*
			 * If the selected action (enable/disable) matches the default, just
			 * let the restriction expire.
			 * Reason for that is that editors can only "disable" for their own
			 * usertype (aft-editor) and lower, meaning that if they can disable,
			 * it will not be disabled for admins. If the default (based on lottery)
			 * is to not show the form at all, it makes more sense to have it
			 * back at that (by immediately expiring the permission level),
			 * resulting in it not being displayed for anyone.
			 */
			$default = ArticleFeedbackv5Permissions::getLottery( $pageObj->getId() );
			$expiry = $params['enable'] == $default ? wfTimestamp( TS_MW )
				: MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_REPLICA )->getInfinity();

			$success = ArticleFeedbackv5Permissions::setRestriction(
				$pageObj->getId(),
				$restriction,
				$expiry,
				$user
			);

			if ( !$success ) {
				$this->dieWithError(
					'articlefeedbackv5-error-unknown',
					'unknown'
				);
			}
		}

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			[]
		);
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return [
			'title' => null,
			'pageid' => [
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'integer'
			],
			'enable' => [
				ParamValidator::PARAM_TYPE     => [ '0', '1' ],
				ParamValidator::PARAM_REQUIRED => true,
			],
		];
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return [
			'api.php?action=articlefeedbackv5-set-status&pageid=1&enable=1'
		];
	}

	public function isWriteMode() {
		return true;
	}

	public function mustBePosted() {
		return true;
	}

}
