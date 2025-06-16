<?php
/**
 * ApiViewFeedbackArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 */

use MediaWiki\Api\ApiQueryBase;
use MediaWiki\User\UserOptionsManager;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * This class pulls the individual ratings/comments for the feedback page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiViewFeedbackArticleFeedbackv5 extends ApiQueryBase {

	/** @var UserOptionsManager */
	private $userOptionsManager;

	/**
	 * @param MediaWiki\Api\ApiQuery $query
	 * @param string $moduleName
	 * @param UserOptionsManager $userOptionsManager
	 */
	public function __construct(
		$query,
		$moduleName,
		UserOptionsManager $userOptionsManager
	) {
		parent::__construct( $query, $moduleName, 'afvf' );
		$this->userOptionsManager = $userOptionsManager;
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		/*
		 * To bust caches, this GET value may be added to the querystring. Codewise,
		 * we won't really use it for anything, but we don't want it to output a
		 * "Unrecognized parameter" warning either, so let's make sure ApiMain
		 * considers it used ;)
		 */
		$this->getMain()->getVal( '_' );

		$params   = $this->extractRequestParams();
		$result   = $this->getResult();
		$html     = '';
		$data     = [];
		$length   = 0;
		$wantHTML = ( isset( $params['mode'] ) && $params['mode'] === 'html' );

		// no page id = central feedback page = null (getAllowedParams will have messed up null values)
		if ( !$params['pageid'] && !$params['title'] ) {
			$pageId = null;

		// get page id
		} else {
			$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
			if ( !$pageObj->exists() ) {
				$this->dieWithError( 'notanarticle' );
			} else {
				$pageId = $pageObj->getId();
			}
		}

		// Save filter in user preference
		$user = $this->getUser();
		$this->userOptionsManager->setOption( $user, 'aftv5-last-filter', $params['filter'] );
		$user->saveSettings();

		$records = $this->fetchData( $params, $pageId );

		// build renderer
		$highlight = (bool)$params['feedbackid'];
		$central = !$pageId;
		if ( $wantHTML ) {
			$renderer = new ArticleFeedbackv5Render( $user, false, $central, $highlight );
		} else {
			$renderer = new ApiArticleFeedbackv5Render( $user, false, $central, $highlight );
		}

		// Build data
		if ( $records ) {
			// @phan-suppress-next-line PhanTypeSuspiciousNonTraversableForeach
			foreach ( $records as $record ) {
				if ( $wantHTML ) {
					$html .= $renderer->run( $record );
				} else {
					$data[] = $renderer->run( $record );
				}
				$length++;
			}
		}

		$filterCount = ArticleFeedbackv5Model::getCount( 'featured', $pageId );
		$totalCount = ArticleFeedbackv5Model::getCount( '*', $pageId );

		// Add metadata
		$result->addValue( $this->getModuleName(), 'length', $length );
		$result->addValue( $this->getModuleName(), 'count', $totalCount );
		$result->addValue( $this->getModuleName(), 'filtercount', $filterCount );
		$result->addValue( $this->getModuleName(), 'offset', $records ? $records->nextOffset() : null );
		$result->addValue( $this->getModuleName(), 'more', $records ? $records->hasMore() : false );
		$result->addValue( $this->getModuleName(), 'feedback', ( $wantHTML ? $html : $data ) );
	}

	/**
	 * @param array $params
	 * @param int|null $pageId Page ID or null for the central feedback page
	 * @return DataModelList|ArticleFeedbackv5Model|array
	 */
	protected function fetchData( $params, $pageId ) {
		// permalink page
		if ( $params['feedbackid'] ) {
			/** @var ArticleFeedbackv5Model $record */
			$record = ArticleFeedbackv5Model::get( $params['feedbackid'], $pageId );
			if ( $record ) {
				return new DataModelList(
					[ [ 'id' => $record->aft_id, 'shard' => $record->aft_page ] ],
					ArticleFeedbackv5Model::class,
					$this->getUser()
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
			$arguments = [];
			$map = [
				'name' => $params['filter'],
				'shard' => $pageId,
				'user' => $this->getUser(),
				'offset' => $params['offset'],
				'sort' => $params['sort'],
				'order' => $params['sortdirection']
			];

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

			return call_user_func_array( [ 'ArticleFeedbackv5Model', $method ], $arguments );
		}

		return [];
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return [
			'title'         => null,
			'pageid'        => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				// @note Special:ArticleFeedbackv5 can call this API module with an empty
				// 'pageid' param; if the type is 'integer' instead of 'string', then the
				// calls on that special page will fail with this error:
				// Invalid value "" for integer parameter "afvfpageid".
				ParamValidator::PARAM_TYPE     => 'string'
			],
			'watchlist'     => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'integer'
			],
			'sort'          => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => array_keys( ArticleFeedbackv5Model::$sorts )
			],
			'sortdirection' => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => [ 'DESC', 'ASC', 'desc', 'asc' ]
			],
			'filter'        => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => array_keys( ArticleFeedbackv5Model::$lists )
			],
			'feedbackid'   => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'string'
			],
			'offset'       => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'string'
			],
			'mode'       => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_TYPE     => [ 'html', 'structured' ]
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
			'api.php?action=query&list=articlefeedbackv5-view-feedback&afvfpageid=1&afvfsort=relevance&afvfsortdirection=ASC&afvffilter=visible-relevant',
		];
	}
}
