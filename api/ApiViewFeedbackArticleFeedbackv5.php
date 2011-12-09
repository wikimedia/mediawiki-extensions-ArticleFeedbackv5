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
		$params   = $this->extractRequestParams();
		$html     = '';
		$result   = $this->getResult();
		$pageId   = $params['pageid'];
		$length   = 0;
		$count    = $this->fetchFeedbackCount(
		 $params['pageid'], $params['filter'] );
		$feedback = $this->fetchFeedback(
			$params['pageid'],
			$params['filter'],
			$params['sort'],
			$params['limit'],
			$params['offset']
		);

		foreach ( $feedback as $record ) {
			$html .= $this->renderFeedback($record);
			$length++;
		}

		$result->addValue(  $this->getModuleName(), 'length', $length );
		$result->addValue(  $this->getModuleName(), 'count', $count );
		$result->addValue(  $this->getModuleName(), 'feedback', $html );
	}

	public function fetchFeedbackCount( $pageId, $filter ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$where = $this->getFilterCriteria( $filter );

		$where['af_page_id'] = $pageId;

		// TODO: Not this.
		return $dbr->selectField(
			array( 'aft_article_feedback' ),
			array( 'COUNT(*) AS count' ),
			$where
		);
	}

	public function fetchFeedback( $pageId,
	 $filter = 'visible', $order = 'newest', $limit = 25, $offset = 0 ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$ids   = array();
		$rows  = array();
		$rv    = array();
		$where = $this->getFilterCriteria( $filter );
		$order;

		# Newest first is the only option right now.
		switch($order) {
			case 'oldest': 
				$order = 'af_id ASC';
				break;
			case 'newest':
			default:
				$order = 'af_id DESC';
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

		if( !count( $ids ) ) {
			return array();
		}

		$rows  = $dbr->select(
			array( 'aft_article_feedback', 'aft_article_answer',
				'aft_article_field', 'aft_article_field_option',
				'user'
			),
			array( 'af_id', 'af_bucket_id', 'afi_name', 'afo_name',
				'aa_response_text', 'aa_response_boolean',
				'aa_response_rating', 'aa_response_option_id',
				'afi_data_type', 'af_created', 'user_name', 
				'af_user_ip', 'af_hide_count', 'af_abuse_count'
			),
			array( 'af_id' => $ids ),
			__METHOD__,
			array( 'ORDER BY' => $order ),
			array(
				'user'        => array(
					'LEFT JOIN', 'user_id = af_user_id'
				),
				'aft_article_field'        => array(
					'LEFT JOIN', 'afi_id = aa_field_id'
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

		foreach( $rows as $row ) {
			if( !array_key_exists( $row->af_id, $rv ) ) {
				$rv[$row->af_id]    = array();
				$rv[$row->af_id][0] = $row;
				$rv[$row->af_id][0]->user_name = $row->user_name ? $row->user_name : $row->af_user_ip;
			}
			$rv[$row->af_id][$row->afi_name] = $row;
		}
		
		return $rv;
	}

	private function getFilterCriteria( $filter ) {
		$where = array();
		switch( $filter ) {
			case 'all':
				$where = array();
				break;
			case 'invisible':
				$where = array( 'af_hide_count > 0' );
 				break;
			case 'visible':
			default:
				$where = array( 'af_hide_count' => 0 );
				break;
		}
		return $where;
	}

	protected function renderFeedback( $record ) {
		$id = $record[0]->af_id;
		$rv = "<div class='aft5-feedback'><p>"
		.wfMessage( 'articlefeedbackv5-form-header', $id, $record[0]->af_created )->escaped()
		.'</p>';
		switch( $record[0]->af_bucket_id ) {
			case 1: $rv .= $this->renderBucket1( $record ); break;
			case 2: $rv .= $this->renderBucket2( $record ); break;
			case 3: $rv .= $this->renderBucket3( $record ); break;
			case 4: $rv .= $this->renderBucket4( $record ); break;
			case 5: $rv .= $this->renderBucket5( $record ); break;
			case 6: $rv .= $this->renderBucket6( $record ); break;
			default: $rv .= $this->renderNoBucket( $record ); break;
		}
		$rv .= "<p>"
		.wfMessage( 'articlefeedbackv5-form-optionid', $record[0]->af_bucket_id )->escaped()
		." | "
		."<a href='#' class='aft5-hide-link' id='aft5-hide-link-$id'>"
		.wfMessage( 'articlefeedbackv5-form-hide', $record[0]->af_hide_count )->escaped()
		.'</a> | '
//204
		."<a href='#' class='aft5-abuse-link' id='aft5-abuse-link-$id'>"
		.wfMessage( 'articlefeedbackv5-form-abuse', $record[0]->af_abuse_count )->escaped()
		."</a></p></div><hr>";
		return $rv;
	}

	private function renderBucket1( $record ) {
		$name  = htmlspecialchars( $record[0]->user_name );
		if( $record['found']->aa_response_boolean ) {
			$found = wfMessage(
				'articlefeedbackv5-form1-header-found',
				$name
			)->escaped();
		} else {
			$found = wfMessage(
				'articlefeedbackv5-form1-header-not-found',
				$name
			)->escaped();
		}
		return "$found
		<blockquote>".htmlspecialchars( $record['comment']->aa_response_text )
		.'</blockquote>';
	}

	private function renderBucket2( $record ) {
		$name = htmlspecialchars( $record[0]->user_name );
		$type = htmlspecialchars( $record['tag']->afo_name );
		return wfMessage( 'articlefeedbackv5-form2-header', $name, $type )->escaped()
		.'<blockquote>'.htmlspecialchars( $record['comment']->aa_response_text )
		.'</blockquote>';
	}

	private function renderBucket3( $record ) {
		$name   = htmlspecialchars( $record[0]->user_name );
		$rating = htmlspecialchars( $record['rating']->aa_response_rating );
		return wfMessage( 'articlefeedbackv5-form3-header', $name, $rating )->escaped()
		.'<blockquote>'.htmlspecialchars( $record['comment']->aa_response_text )
		.'</blockquote>';
	}

	private function renderBucket4( $record ) {
		return wfMessage( 'articlefeedbackv5-form4-header' )->escaped();
	}

	private function renderBucket5( $record ) {
		$name = htmlspecialchars( $record[0]->user_name );
		$rv   = wfMessage( 'articlefeedbackv5-form5-header', $name )->escaped();
		$rv .= '<ul>';
		foreach( $record as $key => $answer ) {
			if( $answer->afi_data_type == 'rating' && $key != '0' ) {
				$rv .= "<li>"
				.htmlspecialchars( $answer->afi_name  )
				.': '
				.htmlspecialchars( $answer->aa_response_rating )
				."</li>";
			}
		}
		$rv .= "</ul>";

		return $rv;
	}

	private function renderBucket0( $record ) {
		# Future-proof this for when the bucket ID changes to 0.
		return $this->renderBucket6( $record );
	}

	private function renderNoBucket( $record ) {
		return wfMessage( 'articlefeedbackv5-form-invalid' )->escaped();
	}

	private function renderBucket6( $record ) {
		return wfMessage( 'articlefeedbackv5-form-not-shown' )->escaped();
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
				 'oldest', 'newest' )
			),
			'filter'    => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'all', 'invisible', 'visible' )
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
		return __CLASS__ . ': $Id: ApiViewRatingsArticleFeedbackv5.php 103439 2011-11-17 03:19:01Z rsterbin $';
	}

}
