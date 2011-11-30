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
		parent::__construct( $query, $moduleName, 'af' );
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		$params   = $this->extractRequestParams();
		#error_log(print_r($params,1));
		$html     = '';
		$result   = $this->getResult();
                $path     = array( 'query', $this->getModuleName() );
                $pageId   = $params['pageid'];
                $feedback = $this->fetchFeedback(
			$params['pageid'],
			$params['filter'],
			$params['sort'],
			$params['limit'],
			$params['offset']
		);

                foreach ( $feedback as $record ) {
			$html .= $this->renderFeedback($record);
                }

                $result->addValue( $path, 'feedback', $html );

                $result->setIndexedTagName_internal( 
			array( 'query', $this->getModuleName() ), 'aa' 
		);

	}

	public function fetchOverallRating( $pageId ) {
		$rv   = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			array( 'aft_article_feedback_ratings_rollup',
				'aft_article_field' ),
			array( 'arr_total / arr_count AS rating',
				'afi_name'
			),
			array( 'arr_page_id' => $pageId,
				'arr_rating_id = afi_id',
				"afi_name IN ('found', 'rating')"
			)
		);

		foreach( $rows as $row ) {
			if( $row->afi_name == 'found' ) {
				$rv['found']  = ( int ) ( 100 * $row->rating );
			} elseif( $row->afi_name == 'rating' ) {
				# Or should this be round/ceil/floor/float?
				$rv['rating'] = ( int ) $row->rating;
			}
		}

		return $rv;
	}


	public function fetchFeedback( $pageId, 
	 $filter = 'visible', $order = 'newest', $limit = 5, $offset = 0 ) {
		$dbr  = wfGetDB( DB_SLAVE );
		$ids  = array();
		$rows = array();
		$rv   = array();
		$order;
		$where;

		switch($order) {
			case 'newest':
				$order = 'af_id DESC';
				break;
			default:
				$order = 'af_id DESC';
				break;
		}

		switch($filter) {
			case 'all':
				$where = array();
				break;
			case 'visible':
# TODO: add the column to control how a thing is hidden.
#				$where = array(
#				break;
			default:
				$where = array();
				break;
		}

		$where['af_page_id'] = $pageId;

		/* I'd really love to do this in one big query, but MySQL
		   doesn't support LIMIT inside IN() subselects, and since 
		   we don't know the number of answers for each feedback
		   record until we fetch them, this is the only way to make
		   sure we get all answers for the exact IDs we want. */
		$id_query = $dbr->select(
			'aft_article_feedback', 'af_id', $where, __METHOD__,
			array( 
				'LIMIT'    => $limit, 
				'OFFSET'   => $offset,
				'ORDER BY' => $order 
			)
		);
		foreach($id_query as $id) {
			$ids[] = $id->af_id;
		}

		$rows  = $dbr->select(
			array( 'aft_article_feedback', 'aft_article_answer', 
				'aft_article_field', 'aft_article_field_option'
			),
			array( 'af_id', 'af_bucket_id', 'afi_name', 'afo_name',
				'aa_response_text', 'aa_response_boolean', 
				'aa_response_rating', 'aa_response_option_id',
				'afi_data_type', 'af_created', 'af_user_text'
			),
			array( 'af_id' => $ids ),
			__METHOD__,
			array( 'ORDER BY' => $order ),
			array(
				'aft_article_field'        => array(
					'JOIN', 'afi_id = aa_field_id'
				),
				'aft_article_answer'       => array(
					'LEFT JOIN', 'af_id = aa_feedback_id'
				),
				'aft_article_field_option' => array(
					'LEFT JOIN', 
					'aa_response_option_id = afo_option_id'
				)
			)
		);

		foreach($rows as $row) {
			if(!array_key_exists($row->af_id, $rv)) {
				$rv[$row->af_id] = array();
				$rv[$row->af_id][0] = $row;
			}
			$rv[$row->af_id][$row->afi_name] = $row;
		}

		return $rv;
	}

	protected function renderFeedback( $record ) {
		$id = $record[0]->af_id;
		$rv = "<div class='aft5-feedback'><p>Feedback #$id"
		.', @'.$record[0]->af_created.'</p>';
		switch( $record[0]->af_bucket_id ) {
			case 1: $rv .= $this->renderBucket1( $record ); break;
			case 2: $rv .= $this->renderBucket2( $record ); break;
			case 3: $rv .= $this->renderBucket3( $record ); break;
			case 4: $rv .= $this->renderBucket4( $record ); break;
			case 5: $rv .= $this->renderBucket5( $record ); break;
			case 6: $rv .= $this->renderBucket6( $record ); break;
			default: return 'Invalid bucket id';
		}
		$rv .= "<p>
		<a href='#' class='aft5-hide-link' id='aft5-hide-link-$id'>Hide this</a>
		<a href='#' class='aft5-abuse-link' id='aft5-abuse-link-$id'>Flag as abuse</a>
		</p>
		</div><hr>";
		return $rv;
	}

	private function renderBucket1( $record ) { 
		$name  = $record[0]->af_user_text;
		$found = $record['found']->aa_response_boolean ? 'found'
		 : 'did not find';
		return "$name $found what they were looking for:"
		.'<blockquote>'.$record['comment']->aa_response_text
		.'</blockquote>';
	}

	private function renderBucket2( $record ) { 
		$name = $record[0]->af_user_text;
		$type = $record['tag']->afo_name;
		return "$name had a $type:"
		.'<blockquote>'.$record['comment']->aa_response_text
		.'</blockquote>';
	}

	private function renderBucket3( $record ) { 
		$name   = $record[0]->af_user_text;
		$rating = $record['rating']->aa_response_rating;
		return "$name rated this page $rating/5:"
		.'<blockquote>'.$record['comment']->aa_response_text
		.'</blockquote>';
	}

	private function renderBucket4( $record ) { 
		return 'User was presented with the CTA-only form.';
	}

	private function renderBucket5( $record ) { 
		$name  = $record[0]->af_user_text;
		$rv = "$name had this to say about robocop:<ul>";
		foreach( $record as $answer ) {
			if( $answer->afi_data_type == 'rating' ) {
				$rv .= "<li>".$answer->afi_name.': '.$answer->aa_response_rating."</li>";
			}
		}
		$rv .= "</ul>";

		return $rv;
	}

	private function renderBucket0( $record ) { 
		# Future-proof this for when the bucket ID changes.
		return $this->renderBucket6( $record );
	}

	private function renderBucket6( $record ) { 
		return 'User was not shown a feedback form.';
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'pageid'    => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'sort'      => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array( 
				 'oldest', 'newest', 'etc' )
			),
			'filter'    => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array( 
				 'all', 'hidden', 'visible' )
			),
			'limit'     => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'offset'    => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
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
			'pageid' => 'Page ID to get feedback ratings for',
			'sort'   => 'Key to sort records by',
			'filter' => 'What filtering to apply to list',
			'limit'  => 'Number of records to show',
			'offset' => 'How many to skip (for pagination)',
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
	protected function getExamples() {
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
		return __CLASS__ . ': $Id: ApiViewRatingsArticleFeedbackv5.php 103439 2011-11-17 03:19:01Z rsterbin $';
	}

}
