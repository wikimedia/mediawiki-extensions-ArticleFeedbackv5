<?php
# This file loads the data and all. The other one saves it.
class ApiQueryArticleFeedback extends ApiQueryBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'af' );
	}
	
	# split these two off into their own modules, instead of doing this.
	public function execute() {
error_log('hi');
		$params     = $this->extractRequestParams();
		global $wgArticleFeedbackRatingTypes;

		if($params['subaction'] == 'showratings') {
			$this->executeFetchRatings();
		} else {
			$this->executeNewForm();
		}
	}

	# Initialize a brand new request 
	protected function executeNewForm() {
		global $wgUser;
		$params     = $this->extractRequestParams();
		$bucket     = $this->getBucket();
		$result     = $this->getResult();

		if(!$params['pageid'] || !$params['revid']) {
		    return null;
		}

		$result->addValue('form', 'pageId',     $params['pageid']); 
		$result->addValue('form', 'bucketId',   $bucket);
		$this->logBucket($params['pageid'], $params['revid'], $bucket);

		/* Commented out, because we're allowing unlimited comments.
		$userData   = array();
		$tmp        = $this->getFeedbackId($params, $bucket);
		$feedbackId = $tmp['feedbackId'];
		if($feedbackId && $tmp['userId'] == $wgUser->getId()) {
			$userData   = $this->getUserRatings($feedbackId);
		}
		$result->addValue('form', 'feedbackId', $feedbackId);
		foreach($userData as $row) {
			$key = $val['key'];
			$val = $val['value'];
			$result->addValue(array('form', 'data'), $key,  $val);
		}
		*/
	}

	protected function executeFetchRatings() {
		$params        = $this->extractRequestParams();
		$result        = $this->getResult();
		$revisionLimit = $this->getRevisionLimit( $params['pageid'] );
		$bucket        = $this->getBucket();
		$pageId	       = $params['pageid'];
		$rows          = $this->fetchRevisionRollup($pageId, $revisionLimit);
		$historical    = $this->fetchPageRollup($pageId);
		$ratings       = array(
			'pageid'  => $params['pageid'],
			'ratings' => array(),
			'status'  => 'current'
		);

		foreach ( $rows as $row ) {
			$overall = 0;
			foreach($historical as $ancient) {
				if($ancient->aaf_name == $row->aaf_name) {
					$overall = $ancient->reviews;
				}
			}
			$ratings['ratings'][] = array(
				'ratingdesc' => $row->field_name,
				'ratingid'   => (int) $row->field_id,
				'total'      => (int) $row->points,
				'count'      => (int) $row->reviews,
				'countall'   => (int) $overall
			);
		}

		foreach ( $ratings as $r ) {
			$result->addValue( 
				array('query', $this->getModuleName()), null, $r
			);
		}

		$result->setIndexedTagName_internal( array( 'query', $this->getModuleName() ), 'aa' );
	}

	protected function getBucket() {
		#TODO base this on last 2 digits of IP address per requirements
		return 5;
	}

	private function logBucket($page, $revision, $bucket) {
	    $dbw = wfGetDB( DB_MASTER );
	    $dbr = wfGetDB( DB_SLAVE );

	    # Select hit counter row
	    $row = $dbr->select(
		'aft_article_hits',
		'aah_counter',
		array(
		    'aah_page_id'     => $page,
		    'aah_revision_id' => $revision,
		    'aah_bucket_id'   => $bucket,
		)
	    );

	    # If there's a row, update it.
	    if($row) {
		$dbw->insert(
		    'aft_article_hits' 
		    array( 'aah_counter' => ($row['aah_counter'} + 1) ),
		    array(
			'aah_page_id'     => $page,
			'aah_revision_id' => $revision,
			'aah_bucket_id'   => $bucket,
		    )
		);
		
	    } 

	    # Otherwise, there's no row, insert one.
	    $dbw->insert('aft_article_hits' array(
		'aah_page_id'	  => $page,
		'aah_revision_id' => $revision,
		'aah_bucket_id'   => $bucket,
		'aah_counter'     => 1
	    ));
	}

	public function fetchPageRollup($pageId, $revisionLimit = 0) {
		return $this->fetchRollup($pageId, $revisionLimit, 'page');
	}

	public function fetchRevisionRollup($pageId, $revisionLimit = 0) {
		return $this->fetchRollup($pageId, $revisionLimit, 'revision');
	}

	private function fetchRollup($pageId, $revisionLimit, $type) {
		$dbr   = wfGetDB( DB_SLAVE );
		$where = array();

		if($type == 'page') {
			$table   = 'article_feedback_ratings_rollup';
			$prefix  = 'aap';
		} else {
			$table   = 'article_revision_feedback_ratings_rollup';
			$prefix  = 'afr';
			$where[] = 'afr_revision >= '.$revisionLimit; #lmao
		}
		$where[$prefix.'_page_id']  = $pageId;
		$where[] = $prefix.'_rating_id = aaf_id';

		$rows  = $dbr->select(
			array( $table, 'article_field' ),
			array(
				'aaf_name AS field_name',
				$prefix.'_rating_id AS field_id',
				'SUM('.$prefix.'_total) AS points',
				'SUM('.$prefix.'_count) AS reviews',
			),
			$where,
			__METHOD__,
			array(
				'GROUP BY' => $prefix.'_rating_id, aaf_name'
			)
		);

		return $rows;
	}

	# Either returns this users feedbackID for this page/rev, or saves 
	# a new one and returns that.
	protected function getFeedbackId($params, $bucket) {
		global $wgUser;
		$token	   = ApiArticleFeedbackUtils::getAnonToken($params);
		$dbr       = wfGetDB( DB_SLAVE );
		$timestamp = $dbr->timestamp();
		$revId     = $params['revid'];
		
		# make sure we have the page/revision/user
		if(!$params['pageid'] || !$wgUser) { return array(); }

		# Fetch this if it wasn't passed in
		if(!$revId) {
			$revId = $dbr->selectField(
				'revision', 'rev_id',
				array('rev_page' => $params['pageid']),
				__METHOD__,
				array(
					'ORDER BY' => 'rev_id DESC',
					'LIMIT'    => 1
				)
			);
		}

		# check for existing feedback for this rev/page/user
		$feedbackId = $dbr->selectField(
			'article_feedback', 'aa_id',
			array(
				'aa_page_id'   => $params['pageid'],
				'aa_revision'  => $revId,
				'aa_user_text' => $wgUser->getName()
			),
			__METHOD__,
			array(
				'ORDER BY' => 'aa_id DESC',
				'LIMIT'    => 1
			)
		);
		if($feedbackId) { 
			return array(
				'feedbackId' => $feedbackId,
				'userId'     => $wgUser->getId()
			); 
		}

		# insert new row if we don't already have one
		$dbw = wfGetDB( DB_MASTER );
		$dbw->insert('article_feedback', array(
			'aa_page_id'         => $params['pageid'],
                        'aa_revision'        => $revId,
                        'aa_created'         => $timestamp,
                        'aa_user_id'         => $wgUser->getId(),
                        'aa_user_text'       => $wgUser->getName(),
                        'aa_user_anon_token' => $token,
                        'aa_design_bucket'   => $bucket,
		), __METHOD__);
		return array(
			'feedbackId' => $dbw->insertID(),
			'userId'     => $wgUser->getId()
		); 
	}

	# Gets the user's feedback for this page. Only works on userids,
	# NOT IP adderesses. Idea being that IPs can move, and we don't want
	# your comments being shown to a different person who took your IP.
	# ALSO take revision limit into account.
	protected function getUserRatings($feedbackId) {
		global $wgUser;
		$dbr      = wfGetDB( DB_SLAVE );
		$feedback = array();
		$rows     = $dbr->select(
			array('article_answer', 'article_field', 
			 'article_feedback'),
			array('aaaa_response_rating', 'aaaa_response_text', 
			 'aaaa_response_bool', 'aaaa_response_option_id', 
			 'aaf_name', 'aaf_data_type'),
			array(
				'aa_revision >= '.$this->getRevisionLimit(),
				'aaaa_feedback_id' => $feedbackId,
				'aa_user_id'       => $wgUser->getId(),
				'aa_is_submitted'  => 1,
			),
			__METHOD__
		);

		foreach($rows as $row) {
			$method = 'response_'.$row->aaf_data_type;
			$feeedback[] = array(
				'name'  => $row->aaf_name,
				'value' => $row->$method
			);
		}
		return $feedback;
	}

	/**
	 * Get the revision number of the oldest revision still being counted in totals.
	 *
	 * @param $pageId Integer: ID of page to check revisions for
	 * @return Integer: Oldest valid revision number or 0 of all revisions are valid
	 */
	protected function getRevisionLimit( $pageId ) {
		global $wgArticleFeedbackRatingLifetime;

		$revision = $this->getDB()->selectField(
			'revision',
			'rev_id',
			array( 'rev_page' => $pageId ),
			__METHOD__,
			array(
				'ORDER BY' => 'rev_id DESC',
				'LIMIT'    => 1,
				'OFFSET'   => $wgArticleFeedbackRatingLifetime - 1
			)
		);
		if ( $revision ) {
			return intval( $revision );
		}
		return 0;
	}

	public function getCacheMode( $params ) {
		if ( $params['userrating'] ) {
			return 'anon-public-user-private';
		} else {
			return 'public';
		}
	}

	public function getAllowedParams() {
		return array(
			'userrating' => 0,
			'anontoken'  => null,
			'subaction'  => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array('showratings','newform'),
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

	public function getParamDescription() {
		return array(
			'pageid' => 'Page ID to get feedback ratings for',
			'userrating' => "Whether to get the current user's ratings for the specified page",
			'anontoken' => 'Token for anonymous users',
		);
	}

	public function getDescription() {
		return array(
			'List article feedback ratings for a specified page'
		);
	}

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedback&afpageid=1',
			'api.php?action=query&list=articlefeedback&afpageid=1&afuserrating=1',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
