<?php
/**
 * ArticleFeedbackv5_LegacyToShard class
 *
 * @package    ArticleFeedbackv5
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Move all relevant legacy data stored in aft_article_*
 * tables to the aft_feedback table that will be sharded.
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_LegacyToShard extends Maintenance {
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
	 */
	public function execute() {
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
	}

	/**
	 * Move a batch of AFT entries
	 *
	 * @param $continue int      [optional] the pull the next batch starting at
	 *                           this aft_id
	 */
	public function moveBatch( $continue ) {
		global $wgArticleFeedbackv5HideAbuseThreshold;

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
				'comment' => 'IFNULL(long_answer.aat_response_text, answer.aa_response_text)', // comment
				'af_created' // timestamp
			),
			array(
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
					'INNER JOIN',
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
			$feedback->id = $row->af_id;
			$feedback->page = $row->af_page_id;
			$feedback->page_revision = $row->af_revision_id;
			$feedback->user = $row->af_user_id;
			$feedback->user_text = $row->af_user_ip ? $row->af_user_ip : User::newFromId( $row->af_user_id )->getName();
			$feedback->user_token = $row->af_user_anon_token;
			$feedback->form = $row->af_form_id;
			$feedback->cta = $row->af_cta_id;
			$feedback->link = $row->af_link_id ? $row->af_link_id : 'X';
			$feedback->rating = $row->rating;
			$feedback->comment = $row->comment;
			$feedback->timestamp = $row->af_created;

			// build username from user id
			if ( $feedback->user && !$feedback->user_text ) {
				$feedback->user->text = $feedback->getUser()->getName();
			}

			$logging = $dbr->select(
				array( 'logging' ),
				array( 'log_action' ),
				array(
					'log_type' => array( 'articlefeedbackv5', 'suppress' ),
					'log_action' => array(
						'oversight', 'unoversight', 'decline', 'request', 'unrequest',
						'hidden', 'unhidden', 'flag', 'unflag', 'autoflag', 'autohide',
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
						$feedback->oversight = 1;
						$feedback->request = 0;
						if ( !$feedback->isHidden() ) {
							$feedback->hide = 1;
							$feedback->autohide = 1;
						}
						break;
					case 'unoversight':
						$feedback->oversight = 0;
						$feedback->request = 0;
						if ( $feedback->hide && $feedback->autohide ) {
							$feedback->autohide = 0;
							$feedback->hide = 0;
						}
						break;
					case 'decline':
						$feedback->request = 0;
						$feedback->decline = 1;
						if ( $feedback->hide && $feedback->autohide ) {
							$feedback->hide = 0;
							$feedback->autohide = 0;
						}
						break;
					case 'request':
						$feedback->request = 1;
						if ( !$feedback->isHidden() ) {
							$feedback->hide = 1;
							$feedback->autohide = 1;
						}
						break;
					case 'unrequest':
						$feedback->request = 0;
						if ( $feedback->hide && $feedback->autohide ) {
							$feedback->hide = 0;
							$feedback->autohide = 0;
						}
						break;
					case 'hidden':
					case 'hide':
						$feedback->hide = 1;
						break;
					case 'autohide':
						$feedback->hide = 1;
						$feedback->autohide = 1;
						break;
					case 'unhidden':
					case 'unhide':
						$feedback->hide = 0;
						$feedback->autohide = 0;
						if ( $feedback->flag && $feedback->autoflag ) {
							$feedback->autoflag = 0;
							$feedback->flag = 0;
						}
						break;
					case 'flag':
					case 'autoflag':
						$feedback->{$log->log_action}++;
						if ( $feedback->flag + $feedback->autoflag > $wgArticleFeedbackv5HideAbuseThreshold && !$feedback->isHidden() ) {
							$feedback->hide = 1;
							$feedback->autohide = 1;
						}
						break;
					case 'unflag':
						if ( $feedback->flag <= 0 ) {
							$feedback->autoflag = 0;
						} else {
							$feedback->flag--;
						}
						if ( $feedback->flag + $feedback->autoflag < $wgArticleFeedbackv5HideAbuseThreshold && $feedback->autohide ) {
							$feedback->autohide = 0;
						}
						break;
					case 'feature':
						$feedback->feature = 1;
						if ( $feedback->flag && $feedback->autoflag ) {
							$feedback->autoflag = 0;
							$feedback->flag = 0;
						}
						break;
					case 'unfeature':
						$feedback->feature = 0;
						break;
					case 'resolve':
						$feedback->resolve = 1;
						break;
					case 'unresolve':
						$feedback->resolve = 0;
						break;
					case 'helpful':
						$feedback->helpful++;
						break;
					case 'unhelpful':
						$feedback->unhelpful++;
						break;
					case 'undo-helpful':
						$feedback->helpful--;
						break;
					case 'undo-unhelpful':
						$feedback->unhelpful--;
						break;
					case 'clear-flags':
						$feedback->autoflag = 0;
						$feedback->flag = 0;
						break;
				}
			}

			$feedback->insert();

			$this->completeCount++;
		}

		return $continue;
	}
}

$maintClass = "ArticleFeedbackv5_LegacyToShard";
require_once( RUN_MAINTENANCE_IF_MAIN );
