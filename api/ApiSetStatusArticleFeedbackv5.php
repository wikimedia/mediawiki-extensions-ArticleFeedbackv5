<?php
/**
 * ApiSetStatusArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

/**
 * This class allows one to quickly enable/disable the AFTv5 form for a certain page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiSetStatusArticleFeedbackv5 extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	public function execute() {
		wfProfileIn( __METHOD__ );

		global $wgUser;

		$params = $this->extractRequestParams();
		$results = array();

		// check if page exists
		if ( Title::newFromID( $params['pageid'] ) === null ) {
			$results['result'] = 'Error';
			$results['reason'] = 'articlefeedbackv5-invalid-page-id';

		// check if current user has editor permission
		} elseif ( !$wgUser->isAllowed( 'aft-editor' ) ) {
			$results['result'] = 'Error';
			$results['reason'] = 'articlefeedbackv5-insufficient-permissions';

		// check if existing page restriction is not too tight (set tight by administrator, should not be overridden)
		} elseif ( ArticleFeedbackv5Permissions::getRestriction( $params['pageid'] )->pr_level === 'aft-administrator' ) {
			$results['result'] = 'Error';
			$results['reason'] = 'articlefeedbackv5-insufficient-permissions';

		} else {
			// enable: allow for all (= allow reader and up);
			// disable: disable for editor and below (= allow aft-administrator and up)
			$restriction = $params['enable'] ? 'aft-reader' : 'aft-editor';

			$success = ArticleFeedbackv5Permissions::setRestriction(
				$params['pageid'],
				$restriction,
				wfGetDB( DB_SLAVE )->getInfinity()
			);

			/*
			 * @todo: So, we now have "it" playing nicely with existing page protection
			 * stuff. The problem now is that, even if page protection does not
			 * disable AFTv5, it still may not be enabled (= if it does not have
			 * lottery or category)
			 * THINK!
			 */

			if ( !$success ) {
				$results['result'] = 'Error';
				$results['reason'] = 'articlefeedbackv5-error-unknown';
			} else {
				$results['result'] = 'Success';
				$results['reason'] = null;
			}
		}

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			$results
		);

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'pageid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'enable' => array(
				ApiBase::PARAM_TYPE     => array( 0, 1 ),
				ApiBase::PARAM_REQUIRED => true,
			),
		);
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		return array(
			'pageid' => 'PageID to enable/disable AFTv5 for',
			'enable' => '1 to enable, 0 to disable AFTv5',
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'Enable/disable AFTv5 for a certain page.'
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=articlefeedbackv5-set-status&pageid=1&enable=1'
		);
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': version 1.5';
	}

	public function isWriteMode() { return true; }

	public function mustBePosted() { return true; }

}
