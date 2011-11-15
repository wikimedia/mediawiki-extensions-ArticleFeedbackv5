<?php
/**
 * ApiQueryArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * This class loads data.  The other one saves it.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiQueryArticleFeedbackv5 extends ApiQueryBase {

	/**
	 * Constructor
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'af' );
	}

	/**
	 * Execute the API call: initialize a brand new request
	 *
	 * JS passes in a page id and, sometimes, a revision id.  Return back the
	 * correct bucket id.
	 *
	 * NB: This call used to return a feedback id and any associated answers as
	 * well as the bucket id (each user was allowed one rating/comment saved
	 * per page per revision); it no longer does, as per the 11/10 meeting --
	 * instead, we'll store everything the user submits.
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		global $wgArticleFeedbackv5RatingTypes, $wgUser;
		$params     = $this->extractRequestParams();
		$bucket     = $this->getBucket( $params );
		$result     = $this->getResult();

		if ( !$params['revid'] ) {
			$params['revid'] = ApiArticleFeedbackv5Utils::getRevisionId( $params['pageid'] );
		}
		if ( !$params['pageid'] || !$params['revid'] ) {
			return null;
		}

		// $this->logHit( $params['pageid'], $params['revid'], $bucket );

		$result->addValue( 'form', 'pageId', $params['pageid'] );
		$result->addValue( 'form', 'bucketId', $bucket );

		// $feedbackId = $this->getFeedbackId($params, $bucket);
		// $result->addValue('form', 'feedbackId', $feedbackId);
	}

	/**
	 * Determine into which bucket this request should fall
	 *
	 * @TODO Base this on last 2 digits of IP address per requirements; when we
	 * have markup, we can add other buckets
	 *
	 * @param  array $params [optional] the params passed in
	 * @return int   the bucket id
	 */
	protected function getBucket( $params = array() ) {
		$allowedBuckets = array( 1, 5, 6 );
		if ( !empty( $params['bucketrequested'] )
			&& is_numeric( $params['bucketrequested'] )
			&& in_array( $params['bucketrequested'],  $allowedBuckets ) ) {
			$bucket = $params['bucketrequested'];
// error_log('Using requested bucket');
		} else {
			// Randomize for now; use the designated algorithm later
			$bucket = $allowedBuckets[rand( 0, count( $allowedBuckets ) - 1 )];
// error_log('Using random bucket');
// error_log(var_export($params, true));
		}
		return $bucket;
	}

	/**
	 * Log that this bucket was served for this page and revision
	 *
	 * @param $page     int the page id
	 * @param $revision int the revision id
	 * @param $bucket   int the bucket id
	 */
	private function logHit( $page, $revision, $bucket ) {
		$dbr  = wfGetDB( DB_SLAVE );
		$dbw = wfGetDB( DB_MASTER );
		$date = date( 'Y-m-d' );

		if ( !$page && !$revision ) {
			return;
		}

		// Select hit counter row
		$hits = $dbr->selectField(
			'aft_article_hits',
			'aah_hits',
			array(
				'aah_page_id'   => $page,
				'aah_date'      => $date,
				'aah_bucket_id' => $bucket,
			)
		);

		// If there's a row, update it.
		if ( $hits ) {
			$dbw->update(
				'aft_article_hits',
				array( 'aah_hits' => ( $hits + 1 ) ),
				array(
					'aah_page_id'   => $page,
					'aah_date'      => $date,
					'aah_bucket_id' => $bucket,
				)
			);
		} else {
			// Otherwise, there's no row, insert one.
			$dbw->insert('aft_article_hits', array(
				'aah_page_id'   => $page,
				'aah_date'      => $date,
				'aah_bucket_id' => $bucket,
				'aah_hits'      => 1
			));
		}
	}

	/**
	 * Gets the user's feedback for this page.
	 *
	 * Only works on userids, NOT IP adderesses. Idea being that IPs can move,
	 * and we don't want your comments being shown to a different person who
	 * took your IP.  ALSO take revision limit into account.
	 *
	 * NB: Mostly deprecated; do not use in new code.
	 *
	 * @param  $feedbackId the feedback id
	 * @return array       the previous answers
	 */
	protected function getUserRatings( $feedbackId ) {
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
				'aa_revision >= ' . $this->getRevisionLimit(),
				'aaaa_feedback_id' => $feedbackId,
				'aa_user_id'       => $wgUser->getId(),
				'aa_is_submitted'  => 1,
			)
		);

		foreach ( $rows as $row ) {
			$method = 'response_'.$row->aaf_data_type;
			$feeedback[] = array(
				'name'  => $row->aaf_name,
				'value' => $row->$method
			);
		}
		return $feedback;
	}

	/**
	 * Get the revision number of the oldest revision still being counted in
	 * totals
	 *
	 * @param  $pageId int ID of page to check revisions for
	 * @return int     oldest valid revision number or 0 of all revisions are valid
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

	/**
	 * Gets the cache mode
	 *
	 * @param  $params array the params passed in
	 * @return string  the cache mode ('anon-public-user-private' or 'public')
	 */
	public function getCacheMode( $params ) {
		if ( $params['userrating'] ) {
			return 'anon-public-user-private';
		} else {
			return 'public';
		}
	}

	/**
	 * TODO
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'userrating' => 0,
			'anontoken'  => null,
			'bucketrequested' => null,
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
			),
		);
	}

	/**
	 * TODO
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		return array(
			'pageid'          => 'Page ID to get feedback ratings for',
			'revid'           => 'Rev ID to get feedback ratings for',
			'anontoken'       => 'Token for anonymous users',
			'bucketrequested' => 'The bucket number requested in the url',
		);
	}

	/**
	 * TODO
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
	 * TODO
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
	 * TODO
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedbackv5&afpageid=1',
		);
	}

	/**
	 * TODO
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

}

