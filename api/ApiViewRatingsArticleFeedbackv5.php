<?php
/**
 * ApiViewRatingsArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * This class pulls the aggregated ratings for display in Bucket #5
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiViewRatingsArticleFeedbackv5 extends ApiQueryBase {

	/**
	 * Constructor
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'af' );
	}

	/**
	 * Execute the API call: Pull the aggregated ratings
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		global $wgArticleFeedbackv5RatingTypes;

		$params        = $this->extractRequestParams();
		$result        = $this->getResult();
		$result_path   = array( 'query', $this->getModuleName() );
		$revisionId    = ApiArticleFeedbackv5Utils::getRevisionId( $params['pageid'] );
		$pageId	       = $params['pageid'];
		$rollup        = $this->fetchPageRollup( $pageId );

		$result->addValue( $result_path, 'pageid', $params['pageid'] );
		$result->addValue( $result_path, 'status', 'current' );

		$info = array();
		foreach ( $rollup as $row ) {
			$info[$row->field_name] = array(
				'ratingdesc' => $row->field_name,
				'ratingid'   => (int) $row->field_id,
				'total'      => (int) $row->points,
				'count'      => (int) $row->reviews,
			);
		}
		$result->addValue( $result_path, 'rollup', $info );

		$result->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'aa' );
	}

	/**
	 * Pulls the page rollup row
	 *
	 * @param  $pageId        int the page id
	 * @return array          the rollup row
	 */
	public function fetchPageRollup( $pageId ) {
		return $this->fetchRollup( $pageId, 0, 'page' );
	}

	/**
	 * Pulls the revision rollup row
	 *
	 * @param  $pageId        int the page id
	 * @param  $revisionLimit int [optional] go back only to this revision
	 * @return array          the rollup row
	 */
	public function fetchRevisionRollup( $pageId, $revisionLimit = 0 ) {
		return $this->fetchRollup( $pageId, $revisionLimit, 'revision' );
	}

	/**
	 * Pulls a rollup row
	 *
	 * @param  $pageId        int    the page id
	 * @param  $revisionLimit int    go back only to this revision
	 * @param  $type          string the type of row to fetch ('page' or 'revision')
	 * @return array          the rollup row
	 */
	private function fetchRollup( $pageId, $revisionLimit, $type ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$where = array();

		if ( $type == 'page' ) {
			$table   = 'article_feedback_ratings_rollup';
			$prefix  = 'arr';
		} else {
			$table   = 'article_revision_feedback_ratings_rollup';
			$prefix  = 'afrr';
			$where[] = 'afrr_revision_id >= ' . $revisionLimit;
		}
		$where[$prefix . '_page_id']  = $pageId;
		$where[] = $prefix . '_rating_id = afi_id';

		$rows  = $dbr->select(
			array( 'aft_' . $table, 'aft_article_field' ),
			array(
				'afi_name AS field_name',
				$prefix . '_rating_id AS field_id',
				'SUM(' . $prefix . '_total) AS points',
				'SUM(' . $prefix . '_count) AS reviews',
			),
			$where,
			__METHOD__,
			array(
				'GROUP BY' => $prefix . '_rating_id, afi_name'
			)
		);

		return $rows;
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'userrating' => 0,
			'anontoken'  => null,
			'userid'     => null,
			'subaction'  => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array( 'showratings', 'newform' ),
			),
			'revid'     => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer',
			),
			'pageid'     => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer',
			)
		);
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		return array(
			'pageid'    => 'Page ID to get feedback ratings for',
			'revid'     => 'Rev ID to get feedback ratings for',
			'anontoken' => 'Token for anonymous users',
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'List article feedback ratings for a specified page'
		);
	}

	/**
	 * Gets any possible errors
	 *
	 * @return array the errors
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedbackv5-view-ratings&afpageid=1',
		);
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

}

