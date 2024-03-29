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
	 * @param ApiMain $query
	 * @param string $moduleName
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		$params   = $this->extractRequestParams();
		$result   = $this->getResult();

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
		if ( !$pageObj->exists() ) {
			$this->dieWithError( 'notanarticle' );
		}
		$pageId = $pageObj->getId();

		// Add metadata
		$count = ArticleFeedbackv5Model::getCount( $params['filter'], $pageId );

		$result->addValue( $this->getModuleName(), 'pageid', $pageId );
		$result->addValue( $this->getModuleName(), 'filter', $params['filter'] );
		$result->addValue( $this->getModuleName(), 'count', $count );
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		$filters = array_keys( ArticleFeedbackv5Model::$lists );

		return [
			'title' => null,
			'pageid' => [
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_DFLT     => 0
			],
			'filter' => [
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => $filters,
				ApiBase::PARAM_DFLT     => ( $filters[0] ?? '' )
			],
		];
	}

	/**
	 * @deprecated since MediaWiki 1.25
	 *
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	public function getExamples() {
		return [
			'api.php?action=articlefeedbackv5-get-count&afpageid=1&filter=featured',
		];
	}
}
