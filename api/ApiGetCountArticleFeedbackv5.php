<?php
/**
 * ApiGetCountArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

use MediaWiki\Api\ApiBase;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * This class gets the amount of feedback for a certain page/filter.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiGetCountArticleFeedbackv5 extends ApiBase {
	/**
	 * @param MediaWiki\Api\ApiMain $query
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
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'integer',
				ParamValidator::PARAM_DEFAULT     => 0
			],
			'filter' => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => $filters,
				ParamValidator::PARAM_DEFAULT     => ( $filters[0] ?? '' )
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
