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
#		if ( !ApiArticleFeedbackv5Utils::isFeedbackEnabled( $params ) ) {
#			$this->dieUsage( 'ArticleFeedback is not enabled on this page', 'invalidpage' );
#		}

		$feedbackId   = $this->getFeedbackId($params);
		$dbr          = wfGetDB( DB_SLAVE );
		$keys         = array();
		foreach($params as $key => $unused) { $keys[] = $key; }
		$user_answers = array();
		$pageId       = $params['pageid'];
		$bucket       = $params['bucket'];
		$revisionId   = $params['revid'];
		$answers      = $dbr->select(
			'aft_article_field',
			array('afi_id', 'afi_name', 'afi_data_type'),
			array('afi_name' => $keys),
			__METHOD__
		);

		foreach($answers as $answer) {
			$type  = $answer->afi_data_type;
			$value = $params[$answer->afi_name];
			if($value && $this->validateParam($value, $type)) {
				$user_answers[] = array(
					'aa_feedback_id'    => $feedbackId,
					'aa_field_id'       => $answer->afi_id,
					"aa_response_$type" => $value
				);
			}
		}
error_log('user answers are');
error_log(print_r($user_answers,1));

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

	protected function validateParams($value, $type) {
		# rating: int between 1 and 5 (inclusive)
		# boolean: 1 or 0
		# option:  option exists
		# text:    none (maybe xss encode)
		switch($type) {
			case 'rating':
				if(preg_match('/^(1|2|3|4|5)$/', $value)) {
					return 1;
				}
				break;
			case 'boolean':
				if(preg_match('/^(1|0)$/', $value)) {
					return 1;
				}
				break;
			case 'option':
				# TODO: check for option existance.
			case 'text':
				return 1;
				break;
			default:
				return 0;
				break;
		}

		return 0;
	}

	public function updateRollupTables($page, $revision) {
#		foreach( array( 'rating', 'boolean', 'select' ) as $type ) {
#		foreach( array( 'rating', 'boolean' ) as $type ) {
		foreach( array( 'rating' ) as $type ) {
			$this->updateRollup( $page, $revision, $type );
		}
	}

	# TODO: This breaks on the select/option_id type.
	private function updateRollup($pageId, $revId, $type) {
		global $wgArticleFeedbackv5RatingLifetime;
		$dbr   = wfGetDB( DB_SLAVE );
		$dbw   = wfGetDB( DB_MASTER );
		$limit = ApiArticleFeedbackv5Utils::getRevisionLimit($pageId);

		# sanity check
		if( $type != 'rating' && $type != 'select' 
		 && $type != 'boolean' ) { return 0; }

		$rows = $dbr->select(
			array( 'aft_article_answer', 'aft_article_feedback', 
			 'aft_article_field' ),
			array( 'aa_field_id', 
			 "SUM(aa_response_$type) AS earned", 
			 'COUNT(*) AS submits'),
			array(
				'afi_data_type'  => $type,
				'af_page_id'     => $pageId,
				'aa_feedback_id = af_id',
				'afi_id = aa_field_id',
				"af_revision_id >= $limit"
			),
			__METHOD__,
			array( 'GROUP BY' =>  'aa_field_id' )
		);

		if( $type == 'select' ) {
			$page_prefix = 'afsr_';
			$rev_prefix  = 'arfsr_';
		} else {
			$page_prefix = 'arr_';
			$rev_prefix  = 'afrr_';
		}
		$page_data  = array();
		$rev_data   = array();
		$rev_table  = 'aft_article_revision_feedback_'
		 .( $type == 'select' ? 'select' : 'ratings' ).'_rollup'; 
		$page_table = 'aft_article_feedback_'
		 .( $type == 'select' ? 'select' : 'ratings' ).'_rollup'; 

		foreach($rows as $row) {
			if($type == 'rating') {
				$points = $row->submits * 5;
			} else {
				$points = $row->submits;
			}

			if(!array_key_exists($row->aa_field_id, $page_data)) {
				$page_data[$row->aa_field_id] = array(
					$page_prefix.'page_id' => $pageId,
					$page_prefix.'total'   => 0,
					$page_prefix.'count'   => 0,
					$page_prefix.($type == 'select' ? 'option' : 'rating').'_id' => $row->aa_field_id
				);
			}

			$rev_data[] = array(
				$rev_prefix.'page_id'     => $pageId,
				$rev_prefix.'revision_id' => $revId,
				$rev_prefix.'total'       => $row->earned,
				$rev_prefix.'count'       => $points,
				$rev_prefix.($type == 'select' ? 'option' : 'rating').'_id' => $row->aa_field_id
			);
			$page_data[$row->aa_field_id][$page_prefix.'total'] += $row->earned;
			$page_data[$row->aa_field_id][$page_prefix.'count'] += $points;
		}

		# Hack becuse you can't use array keys or insert barfs.
		$tmp = array();
		foreach($page_data as $p) {
			$tmp[] = $p;
		}
		$page_data = $tmp;

		$dbw->begin();
		$dbw->delete( $rev_table, array(
			$rev_prefix.'page_id'     => $pageId,
			$rev_prefix.'revision_id' => $revId,
		) );
		$dbw->insert( $rev_table,  $rev_data );
		$dbw->delete( $page_table, array(
			$page_prefix.'page_id'     => $pageId,
		) );
		$dbw->insert( $page_table, $page_data );

		$dbw->commit();
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
			'af_page_id'         => $params['pageid'],
			'af_revision_id'     => $revId,
			'af_created'         => $timestamp,
			'af_user_id'         => $wgUser->getId(),
			'af_user_text'       => $wgUser->getName(),
			'af_user_anon_token' => $token,
			'af_bucket_id'       => $bucket,
		));

		return $dbw->insertID();
	}

	/**
	 * Inserts the user's rating for a specific revision
	 */
	private function saveUserRatings($data, $feedbackId, $bucket) {
		$dbw   = wfGetDB(DB_MASTER);
		$ctaId = $this->getCTAId($data, $bucket);

		$dbw->begin();
		$dbw->insert( 'aft_article_answer', $data, __METHOD__ );
		$dbw->update(
			'aft_article_feedback',
			array( 'af_cta_id' => $ctaId ),
			array( 'af_id'     => $feedbackId ),
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
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
			),
			'anontoken' => null,
			'bucket' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_MIN      => 1
			),
		);

		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach( $fields as $field ) {
			$ret[$field->afi_name] = array(
				ApiBase::PARAM_TYPE     => 'string',
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
		    $ret[$f->afi_name] = 'Optional feedback field, only appears on certain "buckets".';
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
			'api.php?action=articlefeedbackv5'
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}
}
