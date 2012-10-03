<?php
/**
 * ArticleFeedbackv5_RebuildRelevance class
 *
 * @package    ArticleFeedbackv5
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Rebuild relevance scores
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_RebuildRelevance extends Maintenance {

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
		$this->mDescription = 'Rebuild relevance scores';
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		$this->output( "Updating relevance scores\n" );

		$continue = 0;

		while ( $continue !== null ) {
			$continue = $this->refreshBatch( $continue );
			wfWaitForSlaves();

			if ( $continue ) {
				$this->output( "--refreshed to entry #$continue\n" );
			}
		}

		$this->output( "done. Refreshed " . $this->completeCount . " relevance scores.\n" );
	}

	/**
	 * Refreshes a batch of logging entries
	 *
	 * @param $continue int      [optional] the pull the next batch starting at
	 *                           this log_id
	 */
	public function refreshBatch( $continue ) {
		global $wgArticleFeedbackv5RelevanceScoring;

		$dbw = wfGetDB( DB_MASTER );
		$dbr = wfGetDB( DB_SLAVE );

		$rows = $dbr->select(
			array( 'aft_article_feedback' ),
			array(
				'af_id',
				'af_abuse_count',
				'af_helpful_count',
				'af_unhelpful_count',
				'af_oversight_count',
				'af_is_deleted',
				'af_is_hidden',
				'af_is_declined',
				'af_is_featured',
				'af_is_resolved',
				'af_is_autohide',
			),
			array(
				"af_id > $continue"
			),
			__METHOD__,
			array(
				'LIMIT'    => $this->limit,
				'ORDER BY' => 'af_id',
			)
		);

		$continue = null;

		foreach ( $rows as $row ) {
			$continue = $row->af_id;

			// calculate relevance score
			$score = 0;
			$score += $wgArticleFeedbackv5RelevanceScoring['flag'] * $row->af_abuse_count;
			$score += $wgArticleFeedbackv5RelevanceScoring['helpful'] * $row->af_helpful_count;
			$score += $wgArticleFeedbackv5RelevanceScoring['unhelpful'] * $row->af_unhelpful_count;
			$score += $wgArticleFeedbackv5RelevanceScoring['request'] * $row->af_oversight_count;
			$score += $wgArticleFeedbackv5RelevanceScoring['oversight'] * $row->af_is_deleted;
			$score += $wgArticleFeedbackv5RelevanceScoring['hide'] * $row->af_is_hidden;
			$score += $wgArticleFeedbackv5RelevanceScoring['autohide'] * $row->af_is_autohide;
			$score += $wgArticleFeedbackv5RelevanceScoring['decline'] * $row->af_is_declined;
			$score += $wgArticleFeedbackv5RelevanceScoring['feature'] * $row->af_is_featured;
			$score += $wgArticleFeedbackv5RelevanceScoring['resolve'] * $row->af_is_resolved;

			// update log entry
			$dbw->update(
				'aft_article_feedback',
				array( 'af_relevance_score' => $score, 'af_relevance_sort' => -$score ),
				array( 'af_id' => $row->af_id )
			);

			$this->completeCount++;
		}

		return $continue;
	}
}

$maintClass = "ArticleFeedbackv5_RebuildRelevance";
require_once( RUN_MAINTENANCE_IF_MAIN );
