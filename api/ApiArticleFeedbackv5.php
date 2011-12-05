<?php
/**
 * ApiArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * This saves the ratings
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiArticleFeedbackv5 extends ApiBase {

	/**
	 * Constructor
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	/**
	 * Execute the API call: Save the form values
	 */
	public function execute() {
		global $wgUser, $wgArticleFeedbackv5SMaxage;
		$params = $this->extractRequestParams();

		// Anon token check
		$token = ApiArticleFeedbackv5Utils::getAnonToken( $params );

		// Is feedback enabled on this page check?
#		if ( !ApiArticleFeedbackv5Utils::isFeedbackEnabled( $params ) ) {
#			$this->dieUsage( 'ArticleFeedback is not enabled on this page', 'invalidpage' );
#		}

		$feedbackId   = $this->newFeedback( $params );
		$dbr          = wfGetDB( DB_SLAVE );
		$pageId       = $params['pageid'];
		$bucket       = $params['bucket'];
		$revisionId   = $params['revid'];
		$email_data   = array(
			'ratingData' => array(),
			'pageID'     => $pageId,
			'bucketId'   => $bucket
		);

		$user_answers = array();
		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach ( $fields as $field ) {
			if ( $field->afi_bucket_id != $bucket ) {
				continue;
			}
			if ( isset( $params[$field->afi_name] ) ) {
				$value = $params[$field->afi_name];
				$type  = $field->afi_data_type;
				if ( $value === '' ) {
					continue;
				}
				if ( $this->validateParam( $value, $type, $field->afi_id ) ) {
					$data = array(
						'aa_feedback_id' => $feedbackId,
						'aa_field_id'    => $field->afi_id,
					);
					foreach ( array( 'rating', 'text', 'boolean', 'option_id' ) as $t ) {
						$data["aa_response_$t"] = $t == $type ? $value : null;
					}
					$user_answers[] = $data;
					$email_data['ratingData'][$field->afi_name] = $value;
				} else {
					// TODO: ERROR
				}
			}
		}

		$ctaId = $this->saveUserRatings( $user_answers, $feedbackId, $bucket );
		$this->updateRollupTables( $pageId, $revisionId );

		if( $params['email'] ) {
			$this->captureEmail ( $params['email'], json_encode(
				$email_data
			) );
		}

		$squidUpdate = new SquidUpdate( array(
			wfAppendQuery( wfScript( 'api' ), array(
				'action'       => 'query',
				'format'       => 'json',
				'list'         => 'articlefeedback',
				'afpageid'     => $pageId,
				'afanontoken'  => '',
				'afuserrating' => 0,
				'maxage'       => 0,
				'smaxage'      => $wgArticleFeedbackv5SMaxage
			) )
		) );
		$squidUpdate->doUpdate();

		wfRunHooks( 'ArticleFeedbackChangeRating', array( $params ) );

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array(
				'result' => 'Success',
				'feedback_id' => $feedbackId,
				'cta_id' => $ctaId,
			)
		);
	}

	protected function captureEmail( $email, $json ) {
		# http://www.mediawiki.org/wiki/API:Calling_internally
		$params = new FauxRequest( array(
			'action' => 'emailcapture',
			'format' => 'json',
			'email'  => $email,
			'info'   => $json
		) );
		$api = new ApiMain( $params, true );
		$api->execute();
	}

	/**
	 * Validates a value against a field type
	 *
	 * @param  $value    mixed  the value (reference, as option_id switches out
	 *                          text for the id)
	 * @param  $type     string the field type
	 * @param  $field_id int    the field id
	 * @return bool      whether this is okay
	 */
	protected function validateParam( &$value, $type, $field_id ) {
		# rating: int between 1 and 5 (inclusive)
		# boolean: 1 or 0
		# option_id: option exists
		# text: none
		switch ( $type ) {
			case 'rating':
				if ( preg_match( '/^(1|2|3|4|5)$/', $value ) ) {
					return true;
				}
				break;
			case 'boolean':
				if ( preg_match( '/^(1|0)$/', $value ) ) {
					return true;
				}
				break;
			case 'option_id':
				$options = ApiArticleFeedbackv5Utils::getOptions();
				if ( !isset( $options[$field_id] ) ) {
					return false;
				}
				$flip = array_flip( $options[$field_id] );
				if ( isset( $flip[$value] ) ) {
					$value = $flip[$value];
					return true;
				}
				break;
			case 'text':
				return true;
			default:
				return false;
		}
		return false;
	}

	/**
	 * Update the rollup tables
	 *
	 * @param $page     int the page id
	 * @param $revision int the revision id
	 */
	public function updateRollupTables( $page, $revision ) {
		foreach( array( 'rating', 'boolean', 'option_id' ) as $type ) {
			$this->updateRollup( $page, $revision, $type );
		}
	}

	/**
	 * Update the rollup tables
	 *
	 * @param $page     int    the page id
	 * @param $revision int    the revision id
	 * @param $type     string the type (rating, select, or boolean)
	 */
	private function updateRollup( $pageId, $revId, $type ) {
		# sanity check
		if ( $type != 'rating' && $type != 'option_id' && $type != 'boolean' ) {
			return 0;
		}

		global $wgArticleFeedbackv5RatingLifetime;
		$dbr        = wfGetDB( DB_SLAVE );
		$dbw        = wfGetDB( DB_MASTER );
		$limit      = ApiArticleFeedbackv5Utils::getRevisionLimit( $pageId );
		$page_data  = array();
		$rev_data   = array();
		$rev_table  = 'aft_article_revision_feedback_'
			. ( $type == 'option_id' ? 'select' : 'ratings' )
			. '_rollup';
		$page_table = 'aft_article_feedback_'
			. ( $type == 'option_id' ? 'select' : 'ratings' )
			. '_rollup';

		if ( $type == 'option_id' ) {
			$page_prefix = 'afsr_';
			$rev_prefix  = 'arfsr_';
			$select      = array( 'aa_field_id', 'aa_response_option_id', 'COUNT(aa_response_option_id) AS earned', '0 AS submits' );
			$group       = array( 'GROUP BY' => 'aa_response_option_id' );
		} else {
			$page_prefix = 'arr_';
			$rev_prefix  = 'afrr_';
			$select      = array( 'aa_field_id', "SUM(aa_response_$type) AS earned", 'COUNT(*) AS submits' );
			$group       = array( 'GROUP BY' =>  'aa_field_id' );

		}

		$rows = $dbr->select(
			array(
				'aft_article_answer',
				'aft_article_feedback',
				'aft_article_field'
			),
			$select,
			array(
				'afi_data_type'  => $type,
				'af_page_id'     => $pageId,
				'aa_feedback_id = af_id',
				'afi_id = aa_field_id',
				"af_revision_id >= $limit",
			),
			__METHOD__,
			$group
		);

		# Fake the select counts, because we want to group by ughhh
		$totals = array();
		foreach ( $rows as $row ) {
			if( !array_key_exists( $row->aa_field_id, $totals ) ) {
				$totals[$row->aa_field_id] = 0;
			}
			$totals[$row->aa_field_id] += $row->earned;
		}

		foreach ( $rows as $row ) {
			if( $type == 'option_id' ) {
				$key   = $row->aa_response_option_id;
				$field = 'option_id';
				$value = $row->aa_response_option_id;
				$count = $totals[$row->aa_field_id];
			} else {
				$key   = $row->aa_field_id;
				$field = 'rating_id';
				$value = $row->aa_field_id;
				$count = $row->submits;
			}

			if ( !array_key_exists( $key, $page_data ) ) {
				$page_data[$key] = array(
					$page_prefix . 'page_id' => $pageId,
					$page_prefix . 'total'   => 0,
					$page_prefix . 'count'   => 0,
					$page_prefix . $field    => $value
				);
			}

			$rev_data[] = array(
				$rev_prefix . 'page_id'     => $pageId,
				$rev_prefix . 'revision_id' => $revId,
				$rev_prefix . 'total'       => $row->earned,
				$rev_prefix . 'count'       => $count,
				$rev_prefix . $field        => $value
			);

			$page_data[$key][$page_prefix . 'total'] += $row->earned;
			$page_data[$key][$page_prefix . 'count'] += $count;
		}

		if ( count( $page_data ) < 1 ) {
			return;
		}

		$dbw->begin();
		$dbw->delete( $rev_table, array(
			$rev_prefix . 'page_id'     => $pageId,
			$rev_prefix . 'revision_id' => $revId,
			$rev_prefix . $field        => array_keys( $page_data ),
		) );
		$dbw->insert( $rev_table, $rev_data );
		$dbw->delete( $page_table, array(
			$page_prefix . 'page_id' => $pageId,
			$page_prefix . $field    => array_keys( $page_data ),
		) );
		$dbw->insert( $page_table, array_values ( $page_data ) );
		$dbw->commit();
	}

	/**
	 * Creates a new feedback row and returns the id
	 *
	 * @param  $params array the parameters
	 * @return int the feedback id
	 */
	public function newFeedback( $params ) {
		global $wgUser;
		$dbw       = wfGetDB( DB_MASTER );
		$revId     = $params['revid'];
		$bucket    = $params['bucket'];
		$link      = $params['link'];
		$token     = ApiArticleFeedbackv5Utils::getAnonToken( $params );
		$timestamp = $dbw->timestamp();

		# make sure we have a page/user
		if ( !$params['pageid'] || !$wgUser) {
			return null;
		}

		# Fetch this if it wasn't passed in
		if ( !$revId ) {
			$title = Title::newFromID( $params['pageid'] );
			$revId = $title->getLatestRevID();
		}

		$dbw->insert( 'aft_article_feedback', array(
			'af_page_id'         => $params['pageid'],
			'af_revision_id'     => $revId,
			'af_created'         => $timestamp,
			'af_user_id'         => $wgUser->getId(),
			'af_user_text'       => $wgUser->getName(),
			'af_user_anon_token' => $token,
			'af_bucket_id'       => $bucket,
			'af_link_id'         => $link,
		) );

		return $dbw->insertID();
	}

	/**
	 * Inserts the user's rating for a specific revision
	 *
	 * @param  array $data       the data
	 * @param  int   $feedbackId the feedback id
	 * @param  int   $bucket     the bucket id
	 * @return int   the cta id
	 */
	private function saveUserRatings( $data, $feedbackId, $bucket ) {
		$dbw   = wfGetDB( DB_MASTER );
		$ctaId = $this->getCTAId( $data, $bucket );

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

	/**
	 * Picks a CTA to send the user to
	 *
	 * @param  $answers array the user's answers
	 * @param  $bucket  int   the bucket id
	 * @return int the cta id
	 */
	public function getCTAId( $answers, $bucket ) {
		return 1; # Hard-code this for now.
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		$ret = array(
			'pageid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'anontoken' => null,
			'bucket' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_MIN      => 0
			),
			'link' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_MIN      => 0
			),
			'email' => array(
				ApiBase::PARAM_TYPE     => 'string',
			)
		);

		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach ( $fields as $field ) {
			$ret[$field->afi_name] = array(
				ApiBase::PARAM_TYPE     => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
			);
		}

		return $ret;
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		$ret = array(
			'pageid'    => 'Page ID to submit feedback for',
			'revid'     => 'Revision ID to submit feedback for',
			'anontoken' => 'Token for anonymous users',
			'bucket'    => 'Which feedback widget was shown to the user',
			'link'      => 'Which link the user clicked on to get to the widget',
		);
		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach ( $fields as $f ) {
			$ret[$f->afi_name] = 'Optional feedback field, only appears on certain "buckets".';
		}
		return $ret;
	}

	/**
	 * Returns whether this API call is post-only
	 *
	 * @return bool
	 */
	public function mustBePosted() { return true; }

	/**
	 * Returns whether this is a write call
	 *
	 * @return bool
	 */
	public function isWriteMode() { return true; }

	/**
	 * TODO
	 * Gets a list of possible errors
	 *
	 * @return bool
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'anontoken' ),
			array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			array( 'code' => 'invalidpage', 'info' => 'ArticleFeedback is not enabled on this page' ),
		) );
	}

	/**
	 * Gets a description
	 *
	 * @return string
	 */
	public function getDescription() {
		return array(
			'Submit article feedback'
		);
	}

	/**
	 * TODO
	 * Gets a list of examples
	 *
	 * @return array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=articlefeedbackv5'
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

