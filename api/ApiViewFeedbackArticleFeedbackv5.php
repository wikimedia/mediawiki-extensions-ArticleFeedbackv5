<?php
/**
 * ApiViewFeedbackArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 */

/**
 * This class pulls the individual ratings/comments for the feedback page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiViewFeedbackArticleFeedbackv5 extends ApiQueryBase {
	/**
	 * Constructor
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'afvf' );
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		wfProfileIn( __METHOD__ );

		$params   = $this->extractRequestParams();
		$result   = $this->getResult();
		$html     = '';
		$length   = 0;

		// no page id = central feedback page = null (getAllowedParams will have messed up null values)
		if ( !$params['pageid'] ) {
			$params['pageid'] = null;
		}

		// Save filter in user preference
		$user = $this->getUser();
		$user->setOption( 'aftv5-last-filter', $params['filter'] );
		$user->saveSettings();

		$records = $this->fetchData( $params );

		// build renderer
		$highlight = (bool) $params['feedbackid'];
		$central = !(bool) $params['pageid'];
		$renderer = new ArticleFeedbackv5Render( $user, false, $central, $highlight );

		// Build html
		if ( $records ) {
			foreach ( $records as $record ) {
				$html .= $renderer->run( $record );
				$length++;
			}
		}

		$filterCount = ArticleFeedbackv5Model::getCount( 'featured', $params['pageid'] );
		$totalCount = ArticleFeedbackv5Model::getCount( '*', $params['pageid'] );

		// Add metadata
		$result->addValue( $this->getModuleName(), 'length', $length );
		$result->addValue( $this->getModuleName(), 'count', $totalCount );
		$result->addValue( $this->getModuleName(), 'filtercount', $filterCount );
		$result->addValue( $this->getModuleName(), 'offset', $records ? $records->nextOffset() : 0 );
		$result->addValue( $this->getModuleName(), 'more', $records ? $records->hasMore() : false );
		$result->addValue( $this->getModuleName(), 'feedback', $html );

		wfProfileOut( __METHOD__ );
	}

	/**
	 * @param array $params
	 * @return DataModelList
	 */
	protected function fetchData( $params ) {
		// permalink page
		if ( $params['feedbackid'] ) {
			$record = ArticleFeedbackv5Model::get( $params['feedbackid'], $params['pageid'] );
			if ( $record ) {
				return new DataModelList(
					array( array( 'id' => $record->aft_id, 'shard' => $record->aft_page ) ),
					'ArticleFeedbackv5Model'
				);
			}

		// watchlist page
		} elseif ( $params['watchlist'] ) {
			return ArticleFeedbackv5Model::getWatchlistList(
				$params['filter'],
				$this->getUser(),
				$params['offset'],
				$params['sort'],
				$params['sortdirection']
			);

		// list page
		} else {
			return ArticleFeedbackv5Model::getList(
				$params['filter'],
				$params['pageid'],
				$params['offset'],
				$params['sort'],
				$params['sortdirection']
			);
		}

		return array();
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'pageid'        => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'watchlist'     => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'sort'          => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array_keys( ArticleFeedbackv5Model::$sorts )
			),
			'sortdirection' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array( 'desc', 'asc' )
			),
			'filter'        => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array_keys( ArticleFeedbackv5Model::$lists )
			),
			'feedbackid'   => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			),
			'offset'       => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
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
			'sort'        => 'Key to sort records by',
			'filter'      => 'What filtering to apply to list',
			'offset'      => 'Offset to start grabbing data at',
			'feedbackid'  => 'A specific id to fetch',
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'List article feedback for a specified page'
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
	public function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedbackv5-view-feedback&afpageid=1',
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
