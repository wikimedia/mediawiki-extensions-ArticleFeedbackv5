<?php
	/**
	 * ArticleFeedbackv5_LoggingUpdate class
	 *
	 * @package    ArticleFeedbackv5
	 * @author     Matthias Mullie <mmullie@wikimedia.org>
	 * @version    $Id$
	 */

	require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

	/**
	 * Refresh the filter counts
	 *
	 * @package    ArticleFeedbackv5
	 */
class ArticleFeedbackv5_LoggingUpdate extends Maintenance {

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
		$this->mDescription = 'Rebuild existing logging.log_params data to form a serialized array with feedback id & page id';
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		$this->output( "Updating log entries\n" );

		$continue = 0;
		$dbw = wfGetDB( DB_MASTER );

		while ( $continue !== null ) {
			$continue = $this->refreshBatch( $dbw, $continue );
			wfWaitForSlaves();

			if ( $continue ) {
				$this->output( "--refreshed to entry #$continue\n" );
			}
		}

		$this->output( "done. Refreshed " . $this->completeCount . " log entries.\n" );
	}

	/**
	 * Refreshes a batch of logging entries
	 *
	 * @param $dbw      Database master database connection
	 * @param $continue int      [optional] the pull the next batch starting at
	 *                           this log_id
	 */
	public function refreshBatch( $dbw, $continue ) {
		// fetch the log IDs we need.
		$id_query = $dbw->select(
			array( 'logging' ),
			array( 'log_id' ),
			array(
				"log_id > $continue",
				'log_title LIKE "ArticleFeedbackv5/%"'
			),
			__METHOD__,
			array(
				'LIMIT'    => ( $this->limit + 1 ),
				'ORDER BY' => 'log_id',
			)
		);

		// Pull the continue for the next set
		$ids = array();
		$continue = null;
		foreach ( $id_query as $id ) {
			$ids[$id->log_id] = $id->log_id;
			// Get the continue values from the last counted item.
			if ( count( $ids ) == $this->limit ) {
				$continue = $id->log_id;
			}
		}
		if ( !count( $ids ) ) {
			return null;
		}
		if ( count( $ids ) > $this->limit ) {
			array_pop( $ids );
		}

		// select rows
		$rows  = $dbw->select(
			array( 'logging', 'page' ),
			array(
				'log_id',
				'feedback_id' => 'SUBSTRING_INDEX(log_title, "/", -1)',
				'page_id'
			),
			array( 'log_id' => $ids ),
			__METHOD__,
			array(),
			array(
				'page' => array(
					'INNER JOIN', array(
						'page_namespace = 0', // this maintenance only supports NS_MAIN
						'page_title = SUBSTRING_INDEX(REPLACE(log_title, "ArticleFeedbackv5/", ""), "/", 1)'
					)
				),
			)
		);

		foreach ( $rows as $row ) {
			// build params
			$params = array(
				'feedbackId' => $row->feedback_id,
				'pageId' => $row->page_id
			);

			// update log entry
			$dbw->update(
				'logging',
				array( 'log_params' => serialize( $params ) ),
				array( 'log_id' => $row->log_id )
			);

			$this->completeCount++;
		}

		return $continue;
	}
}

$maintClass = "ArticleFeedbackv5_LoggingUpdate";
require_once( RUN_MAINTENANCE_IF_MAIN );
