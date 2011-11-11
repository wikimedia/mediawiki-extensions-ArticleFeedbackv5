<?php
# This file loads the data and all. The other one saves it.
class ApiQueryArticleFeedbackv5 extends ApiQueryBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'af' );
	}

	# split these two off into their own modules, instead of doing this.
	public function execute() {
		$params     = $this->extractRequestParams();
		global $wgArticleFeedbackv5RatingTypes;

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

		if(!$params['revid']) {
			$params['revid'] = ApiArticleFeedbackv5Utils::getRevisionId($params['pageid']);
		}

		if(!$params['pageid'] || !$params['revid']) {
			return null;
		}


		$this->logHit($params['pageid'], $params['revid'], $bucket);

		$result->addValue('form', 'pageId', $params['pageid']);
		$result->addValue('form', 'bucketId', $bucket);
		# Not doing this, per 11/10 meeting, for scalability reasons.
		#$feedbackId = $this->getFeedbackId($params, $bucket);
		#$result->addValue('form', 'feedbackId', $feedbackId);
	}

	protected function executeFetchRatings() {
		$params        = $this->extractRequestParams();
		$result        = $this->getResult();
		$revisionLimit = ApiArticleFeedbackv5Utils::getRevisionId( $params['pageid'] );
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

	private function logHit($page, $revision, $bucket) {
		$dbr  = wfGetDB( DB_SLAVE );
		$dbw = wfGetDB( DB_MASTER );
		$date = date('Y-m-d');

		if(!$page && !$revision) {
		return;
		}

		# Select hit counter row
		$hits = $dbr->selectField(
		'aft_article_hits',
		'aah_hits',
		array(
			'aah_page_id'   => $page,
			'aah_date'      => $date,
			'aah_bucket_id' => $bucket,
		)
		);

		# If there's a row, update it.
		if($hits) {
		$dbw->update(
			'aft_article_hits',
			array( 'aah_hits' => ($hits + 1) ),
			array(
			'aah_page_id'   => $page,
			'aah_date'      => $date,
			'aah_bucket_id' => $bucket,
			)
		);
		} else {
		# Otherwise, there's no row, insert one.
		$dbw->insert('aft_article_hits', array(
			'aah_page_id'   => $page,
			'aah_date'      => $date,
			'aah_bucket_id' => $bucket,
			'aah_hits'      => 1
		));
		}
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
			$where[] = 'afr_revision_id >= '.$revisionLimit;
		}
		$where[$prefix.'_page_id']  = $pageId;
		$where[] = $prefix.'_rating_id = aaf_id';

		$rows  = $dbr->select(
			array( 'aft_'.$table, 'aft_article_field' ),
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

	# Mostly deprecated
	# Gets the user's feedback for this page. Only works on userids,
	# NOT IP adderesses. Idea being that IPs can move, and we don't want
	# your comments being shown to a different person who took your IP.
	# ALSO take revision limit into account.
	protected function getUserRatings($feedbackId) {
		global $wgUser;
		$dbr      = wfGetDB( DB_SLAVE );
		$feedback = array();
		$rows     = $dbr->select(
			array('aft_article_answer', 'aft_article_field',
			 'aft_article_feedback'),
			array('aaaa_response_rating', 'aaaa_response_text',
			 'aaaa_response_bool', 'aaaa_response_option_id',
			 'aaf_name', 'aaf_data_type'),
			array(
				'aa_revision >= '.$this->getRevisionLimit(),
				'aaaa_feedback_id' => $feedbackId,
				'aa_user_id'       => $wgUser->getId(),
				'aa_is_submitted'  => 1,
			)
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
		global $wgArticleFeedbackv5RatingLifetime;

		$revision = $this->getDB()->selectField(
			'revision',
			'rev_id',
			array( 'rev_page' => $pageId ),
			__METHOD__,
			array(
				'ORDER BY' => 'rev_id DESC',
				'LIMIT'    => 1,
				'OFFSET'   => $wgArticleFeedbackv5RatingLifetime - 1
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

	# TODO
	public function getParamDescription() {
		return array(
			'pageid'    => 'Page ID to get feedback ratings for',
			'revid'     => 'Rev ID to get feedback ratings for',
			'anontoken' => 'Token for anonymous users',
		);
	}

	# TODO
	public function getDescription() {
		return array(
			'List article feedback ratings for a specified page'
		);
	}

	# TODO
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	# TODO
	protected function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedbackv5&afpageid=1',
		);
	}

	# TODO
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
