<?php
/**
 * ArticleFeedbackv5_LegacyToShard class
 *
 * @package    ArticleFeedbackv5
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . '/maintenance/Maintenance.php'
	: dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Move all relevant legacy data stored in aft_article_*
 * tables to the aft_feedback table that will be sharded.
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_LegacyToShard extends LoggedUpdateMaintenance {
	/**
	 * Batch size
	 *
	 * @var int
	 */
	private $limit = 50;

	/**
	 * The number of entries completed
	 *
	 * @var int
	 */
	private $completeCount = 0;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->mDescription = 'Move all relevant legacy data stored in aft_article_* tables to the aft_feedback table that will be sharded.';
	}

	/**
	 * Execute the script
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
		$dbr = $this->getDB( DB_SLAVE );
		if ( !$dbr->tableExists( 'aft_article_feedback' ) ) {
			// not necessary to run, there is no source data
			return;
		} elseif ( !$dbr->tableExists( 'aft_article_feedback' ) ) {
			// not possible to run, there is no target
			$this->output( "Target table 'aft_feedback' does not exist.\n" );
			return;
		}

		$this->output( "Moving legacy ArticleFeedbackv5 entries to sharded table.\n" );

		$continue = 0;
		while ( $continue !== null ) {
			$continue = $this->moveBatch( $continue );
			wfWaitForSlaves();

			if ( $continue ) {
				$this->output( "--moved to entry #$continue\n" );
			}
		}

		$this->output( "Done. Moved " . $this->completeCount . " ArticleFeedbackv5 entries.\n" );

		return true;
	}

	/**
	 * Move a batch of AFT entries
	 *
	 * @param $continue int      [optional] the pull the next batch starting at
	 *                           this aft_id
	 */
	public function moveBatch( $continue ) {
		global
			$wgArticleFeedbackv5HideAbuseThreshold,
			$wgArticleFeedbackv5DisplayBuckets,
			$wgArticleFeedbackv5CTABuckets,
			$wgArticleFeedbackv5LinkBuckets,
			$wgArticleFeedbackv5MaxCommentLength;

		$dbr = $this->getDB( DB_SLAVE );

		$rows = $dbr->select(
			array(
				'aft_article_feedback',
				'rating' => 'aft_article_answer',
				'answer' => 'aft_article_answer',
				'long_answer' => 'aft_article_answer_text',
				'page',
			),
			array(
				'af_id',
				'page_title',
				'af_page_id', // page
				'af_revision_id', // page_revision
				'af_user_id', // user
				'af_user_ip', // user_text
				'af_user_anon_token', // user_token
				'af_form_id', // form
				'af_cta_id', // cta
				'af_link_id', // link
				'rating' => 'rating.aa_response_boolean', // rating
				'comment' => "SUBSTR(IFNULL(long_answer.aat_response_text, answer.aa_response_text), 1, $wgArticleFeedbackv5MaxCommentLength)", // comment
				'af_created' // timestamp
			),
			array(
				'af_form_id' => array_keys( $wgArticleFeedbackv5DisplayBuckets['buckets'] ),
				'af_cta_id' => array_keys( $wgArticleFeedbackv5CTABuckets['buckets'] ),
				'af_link_id' => array_keys( $wgArticleFeedbackv5LinkBuckets['buckets'] ),
				"af_id > $continue"
			),
			__METHOD__,
			array(
				'LIMIT'    => $this->limit,
				'ORDER BY' => 'af_id'
			),
			array(
				'rating' => array(
					'INNER JOIN',
					array(
						'rating.aa_feedback_id = af_id',
						'rating.aa_field_id' => array( '1', '16' )
					)
				),
				'answer' => array(
					'LEFT JOIN',
					array(
						'answer.aa_feedback_id = af_id',
						'answer.aa_field_id' => array( '2', '17' )
					)
				),
				'long_answer' => array(
					'LEFT JOIN',
					array( 'long_answer.aat_id = answer.aat_id' )
				),
				'page' => array(
					'INNER JOIN',
					array(
						'page_id = af_page_id',
					)
				)
			)
		);

		$continue = null;

		foreach ( $rows as $row ) {
			$continue = $row->af_id;

			// build feedback entry
			$feedback = new ArticleFeedbackv5Model;
			$feedback->aft_id = $row->af_id;
			$feedback->aft_page = $row->af_page_id;
			$feedback->aft_page_revision = $row->af_revision_id;
			$feedback->aft_user = $row->af_user_id;
			$feedback->aft_user_text = $row->af_user_ip ? $row->af_user_ip : User::newFromId( $row->af_user_id )->getName();
			$feedback->aft_user_token = $row->af_user_anon_token;
			$feedback->aft_form = $row->af_form_id;
			$feedback->aft_cta = $row->af_cta_id;
			$feedback->aft_link = $row->af_link_id ? $row->af_link_id : 'X';
			$feedback->aft_rating = $row->rating;
			$feedback->aft_comment = isset( $row->comment ) ? $row->comment : '';
			$feedback->aft_timestamp = $row->af_created;

			// build username from user id
			if ( $feedback->aft_user && !$feedback->aft_user_text ) {
				$feedback->aft_user->text = $feedback->getUser()->getName();
			}

			$logging = $dbr->select(
				array( 'logging' ),
				array( 'log_action' ),
				array(
					'log_type' => array( 'articlefeedbackv5', 'suppress' ),
					'log_action' => array(
						'oversight', 'unoversight', 'decline', 'request', 'unrequest',
						'hidden', 'hide', 'unhidden', 'unhide', 'flag', 'unflag', 'autoflag', 'autohide',
						'feature', 'unfeature', 'resolve', 'unresolve', 'helpful',
						'unhelpful', 'undo-helpful', 'undo-unhelpful', 'clear-flags'
					),
					'log_namespace' => NS_SPECIAL,
					'log_page' => 0,
					'log_title' => "ArticleFeedbackv5/$row->page_title/$row->af_id"
				),
				__METHOD__,
				array(
					'SORT BY' => 'log_id ASC'
				)
			);

			foreach ( $logging as $log ) {
				switch ( $log->log_action ) {
					case 'oversight':
						$feedback->aft_oversight = 1;
						$feedback->aft_request = 0;
						if ( !$feedback->isHidden() ) {
							$feedback->aft_hide = 1;
							$feedback->aft_autohide = 1;
						}
						break;
					case 'unoversight':
						$feedback->aft_oversight = 0;
						$feedback->aft_request = 0;
						if ( $feedback->aft_hide && $feedback->aft_autohide ) {
							$feedback->aft_autohide = 0;
							$feedback->aft_hide = 0;
						}
						break;
					case 'decline':
						$feedback->aft_decline = 1;
						if ( $feedback->aft_hide && $feedback->aft_autohide ) {
							$feedback->aft_hide = 0;
							$feedback->aft_autohide = 0;
						}
						break;
					case 'request':
						$feedback->aft_request = 1;
						$feedback->aft_decline = 0;
						if ( !$feedback->isHidden() ) {
							$feedback->aft_hide = 1;
							$feedback->aft_autohide = 1;
						}
						break;
					case 'unrequest':
						$feedback->aft_request = 0;
						if ( $feedback->aft_hide && $feedback->aft_autohide ) {
							$feedback->aft_hide = 0;
							$feedback->aft_autohide = 0;
						}
						break;
					case 'flag':
					case 'autoflag':
						$feedback->{"aft_$log->log_action"}++;
						if ( $feedback->aft_flag + $feedback->aft_autoflag > $wgArticleFeedbackv5HideAbuseThreshold && !$feedback->isHidden() ) {
							$feedback->aft_hide = 1;
							$feedback->aft_autohide = 1;
						}
						break;
					case 'unflag':
						if ( $feedback->aft_flag <= 0 ) {
							$feedback->aft_autoflag = 0;
						} else {
							$feedback->aft_flag--;
						}
						if ( $feedback->aft_flag + $feedback->aft_autoflag < $wgArticleFeedbackv5HideAbuseThreshold && $feedback->aft_autohide ) {
							$feedback->aft_autohide = 0;
						}
						break;
					case 'feature':
						$feedback->aft_feature = 1;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 0;
						if ( $feedback->aft_flag && $feedback->aft_autoflag ) {
							$feedback->aft_autoflag = 0;
							$feedback->aft_flag = 0;
						}
						break;
					case 'unfeature':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 0;
						break;
					case 'resolve':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 1;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 0;
						break;
					case 'unresolve':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 0;
						break;
					case 'noaction':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 1;
						$feedback->aft_hide = 0;
						break;
					case 'unnoaction':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 0;
						break;
					case 'hidden':
					case 'hide':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 1;
						break;
					case 'autohide':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 1;
						$feedback->aft_autohide = 1;
						break;
					case 'unhidden':
					case 'unhide':
						$feedback->aft_feature = 0;
						$feedback->aft_resolve = 0;
						$feedback->aft_noaction = 0;
						$feedback->aft_hide = 0;
						$feedback->aft_autohide = 0;
						if ( $feedback->aft_flag && $feedback->aft_autoflag ) {
							$feedback->aft_autoflag = 0;
							$feedback->aft_flag = 0;
						}
						break;
					case 'helpful':
						$feedback->aft_helpful++;
						break;
					case 'unhelpful':
						$feedback->aft_unhelpful++;
						break;
					case 'undo-helpful':
						$feedback->aft_helpful--;
						break;
					case 'undo-unhelpful':
						$feedback->aft_unhelpful--;
						break;
					case 'clear-flags':
						$feedback->aft_autoflag = 0;
						$feedback->aft_flag = 0;
						break;
				}
			}

			$feedback->insert();

			$this->completeCount++;
		}

		return $continue;
	}

	/**
	 * Get the update key name to go in the update log table
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'ArticleFeedbackv5_LegacyToShard';
	}
}

$maintClass = "ArticleFeedbackv5_LegacyToShard";
require_once( RUN_MAINTENANCE_IF_MAIN );
