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
		$renderer = new ArticleFeedbackv5Render( false, $central, $highlight );

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

		} else {
			/*
			 * Because a lot of the arguments for both getWatchlistList & getList
			 * are optional, they'll be built using Reflection to ensure that every
			 * arguments is optional, even if another one (e.g. the last one) is
			 * specified (e.g. through reflection, we can read the default values
			 * for all other parameters).
			 * $map will serve as an temporary aid to overcome the differences
			 * between this API's parameter names and the methods' argument names.
			 */
			$arguments = array();
			$map = array(
				'name' => $params['filter'],
				'shard' => $params['pageid'],
				'user' => $this->getUser(),
				'offset' => $params['offset'],
				'sort' => $params['sort'],
				'order' => $params['sortdirection']
			);

			$method = $params['watchlist'] ? 'getWatchlistList' : 'getList';
			$function = new ReflectionMethod( 'ArticleFeedbackv5Model', $method );
			foreach ( $function->getParameters() as $parameter ) {
				$name = $parameter->getName();
				if ( $map[$name] === null && $parameter->isOptional() ) {
					$arguments[] = $parameter->getDefaultValue();
				} else {
					$arguments[] = $map[$name];
				}
			}

			return call_user_func_array( array( 'ArticleFeedbackv5Model', $method ), $arguments );
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
				ApiBase::PARAM_TYPE     => array( 'DESC', 'ASC' )
			),
			'filter'        => array(
				ApiBase::PARAM_REQUIRED => true,
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
			'pageid'        => 'Page ID to get feedback ratings for',
			'watchlist'     => "Load feedback from user's watchlisted pages (1) or from all pages (0)",
			'sort'          => 'Key to sort records by',
			'sortdirection' => 'Direction (ASC|DESC) to sort the feedback by',
			'filter'        => 'What filtering to apply to list',
			'feedbackid'    => 'The ID of a specific feedback item to fetch',
			'offset'        => 'Offset to start grabbing data at',
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
			'api.php?action=query&list=articlefeedbackv5-view-feedback&afvfpageid=1&afvfsort=relevance&afvfsortdirection=ASC&afvffilter=visible-relevant',
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
