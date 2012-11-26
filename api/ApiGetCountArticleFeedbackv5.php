<?php
/**
 * ApiGetCountArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

/**
 * This class gets the amount of feedback for a certain page/filter.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiGetCountArticleFeedbackv5 extends ApiBase {
	/**
	 * Constructor
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		wfProfileIn( __METHOD__ );

		$params   = $this->extractRequestParams();
		$result   = $this->getResult();

		// Add metadata
		$count = ArticleFeedbackv5Model::getCount( $params['filter'], $params['pageid'] );

		$result->addValue( $this->getModuleName(), 'pageid', $params['pageid'] );
		$result->addValue( $this->getModuleName(), 'filter', $params['filter'] );
		$result->addValue( $this->getModuleName(), 'count', $count );

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		$filters = array_keys( ArticleFeedbackv5Model::$lists );

		return array(
			'pageid'        => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_DFLT     => 0
			),
			'filter'        => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => $filters,
				ApiBase::PARAM_DFLT     => $filters[0]
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
			'pageid'      => 'Page ID to get feedback ratings for',
			'filter'      => 'What filtering to apply to list',
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'Get the amount of feedback for a certain page/filter'
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	public function getExamples() {
		return array(
			'api.php?action=articlefeedbackv5-get-count&afpageid=1&filter=featured',
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
}
