<?php
# This file saves the data and all. The other one loads it.
class ApiArticleFeedbackv5 extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	public function execute() {
		global $wgUser, $wgArticleFeedbackv5SMaxage;
		$params = $this->extractRequestParams();

error_log('saving form');
error_log(print_r($params,1));

		// Anon token check
		$token = ApiArticleFeedbackv5Utils::getAnonToken( $params );

		// Is feedback enabled on this page check?
		if ( !ApiArticleFeedbackv5Utils::isFeedbackEnabled( $params ) ) {
			$this->dieUsage( 'ArticleFeedback is not enabled on this page', 'invalidpage' );
		}

		$feedbackId   = $this->getFeedbackId($params);
error_log("feedback id is $feedbackId");
		$dbr          = wfGetDB( DB_SLAVE );
		$keys         = array();
		foreach($params as $key => $unused) { $keys[] = $key; }
		$user_answers = array();
		$pageId       = $params['pageid'];
		$bucket       = $params['bucket'];
		$revisionId   = $params['revid'];
		$answers      = $dbr->select(
			'aft_article_field',
			array('aaf_id', 'aaf_name', 'aaf_data_type'),
			array('aaf_name' => $keys),
			__METHOD__
		);

		foreach($answers as $answer) {
			$type = $answer->aaf_data_type;
			$user_answers[] = array(
				'aaaa_feedback_id'    => $feedbackId,
				'aaaa_field_id'       => $answer->aaf_id,
				"aaaa_response_$type" => $params[$answer->aaf_name]
			);
		}

		$ctaId = $this->saveUserRatings($user_answers, $feedbackId, $bucket);
		$this->updateRollupTables($pageId, $revisionId);

		$squidUpdate = new SquidUpdate(array(
			wfAppendQuery(wfScript('api'), array(
				'action'       => 'query',
				'format'       => 'json',
				'list'         => 'articlefeedback',
				'afpageid'     => $pageId,
				'afanontoken'  => '',
				'afuserrating' => 0,
				'maxage'       => 0,
				'smaxage'      => $wgArticleFeedbackv5SMaxage
			))
		));
		$squidUpdate->doUpdate();

		wfRunHooks('ArticleFeedbackChangeRating', array($params));

		$this->getResult()->addValue(null, $this->getModuleName(),
			array('result' => 'Success')
		);
	}

	public function updateRollupTables($page, $revision) {
		$this->updateRatingRollup($page, $revision);
		$this->updateSelectRollup($page, $revision);
	}

	public function updateRatingRollup($page, $rev) {
		$this->__updateRollup($page, $rev, 'ratings', 'page');
		$this->__updateRollup($page, $rev, 'ratings', 'revision');
	}

	public function updateSelectRollup($page, $rev) {
		$this->__updateRollup($page, $rev, 'select', 'page');
		$this->__updateRollup($page, $rev, 'select', 'revision');
	}

	# page and rev and page and revision ids
	# type is either ratings or select, the two rollups we have
	# scope is either page or revision
	private function __updateRollup($page, $rev, $type, $scope) {
		# sanity check
		if($type != 'ratings' && $type != 'select') { return 0; }
		if($scope != 'page' && $scope != 'revision') { return 0; }

		# TODO
		$table = 'article_'.$rev.'_feedback_'.$type.'_rollup';
	}

	public function getFeedbackId($params) {
		global $wgUser;
		$dbw       = wfGetDB( DB_MASTER );
		$revId     = $params['revid'];
		$bucket    = $params['revid'];
		$token     = ApiArticleFeedbackv5Utils::getAnonToken($params);
		$timestamp = $dbw->timestamp();

		# make sure we have a page/user
		if(!$params['pageid'] || !$wgUser) { return null; }

		# Fetch this if it wasn't passed in
		if(!$revId) {
			$revId = ApiArticleFeedbackv5Utils::getRevisionId($params['pageid']);
error_log('rev id?');
		}

		$dbw->insert('aft_article_feedback', array(
			'aa_page_id'         => $params['pageid'],
			'aa_revision_id'     => $revId,
			'aa_created'         => $timestamp,
			'aa_user_id'         => $wgUser->getId(),
			'aa_user_text'       => $wgUser->getName(),
			'aa_user_anon_token' => $token,
			'aa_bucket_id'       => $bucket,
		));

		return $dbw->insertID();
	}


	/**
	 * Inserts the user's rating for a specific revision
	 */
	private function saveUserRatings($data, $feedbackId, $bucket) {
		$dbw   = wfGetDB(DB_MASTER);
		$ctaId = $this->getCTAId($data, $bucket);

		# TODO: Move these deleted rows to an archive table or flag
		# them as archived or something.
		$dbw->begin();
		$dbw->insert('aft_article_answer', $data, __METHOD__);
		$dbw->update(
			'aft_article_feedback',
			array( 'aa_cta_id'    => $ctaId ),
			array( 'aa_id' => $feedbackId ),
			__METHOD__
		);
		$dbw->commit();

		return $ctaId;
	}

	public function getCTAId($answers, $bucket) {
	    return 1; # Hard-code this for now.
	}

	public function getAllowedParams() {
		$ret = array(
			'pageid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => false,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => false,
			),
			'anontoken' => null,
			'bucket' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => false,
				ApiBase::PARAM_MIN => 0
			),
			'expertise' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
		);

		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach( $fields as $field ) {
			$ret[$field->aaf_name] = array(
				ApiBase::PARAM_TYPE     => 'text',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
			);
		}

		return $ret;
	}

	public function getParamDescription() {
		$fields = ApiArticleFeedbackv5Utils::getFields();
		$ret    = array(
			'pageid'    => 'Page ID to submit feedback for',
			'revid'     => 'Revision ID to submit feedback for',
			'anontoken' => 'Token for anonymous users',
			'bucket'    => 'Which rating widget was shown to the user',
			'expertise' => 'What kinds of expertise does the user claim to have',
		);

		foreach( $fields as $f ) {
		    $ret[$f->aaf_name] = 'Optional feedbackl field, only appears in certain "buckets".';
		}

		return $ret;
	}

	public function mustBePosted() { return true; }

	public function isWriteMode() { return true; }

	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'anontoken' ),
			array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			array( 'code' => 'invalidpage', 'info' => 'ArticleFeedback is not enabled on this page' ),
		) );
	}

	public function getDescription() {
		return array(
			'Submit article feedback'
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=articlefeedback'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
