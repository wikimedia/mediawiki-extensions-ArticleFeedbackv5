<?php
/**
 * ArticleFeedbackv5_RefreshLastActivity class
 *
 * @package    ArticleFeedbackv5
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Refresh the last-activity columns for all feedback rows
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_RefreshLastActivity extends Maintenance {

	/**
	 * Map of log actions to statuses
	 *
	 * @var array
	 */
	public static $logActionToStatus = array(
		'autoflag' => 'autoflag',
		'autohide' => 'autohide',
		'decline' => 'declined',
		'feature' => 'featured',
		'flag' => 'autoflag',
		'hidden' => 'hidden',
		'oversight' => 'deleted',
		'request' => 'request',
		'resolve' => 'resolved',
		'unfeature' => 'unfeatured',
		'unflag' => 'autounflag',
		'unhidden' => 'unhidden',
		'unoversight' => 'undeleted',
		'unrequest' => 'unrequest',
		'unresolve' => 'unresolved',
	);

	/**
	 * Batch size
	 *
	 * @var int
	 */
	private $limit = 50;

	/**
	 * The number of feedback posts completed
	 *
	 * @var int
	 */
	private $completeCount = 0;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->mDescription = 'Refresh the last-activity columns for all feedback rows';
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		$this->output( "Refreshing last feedback activity...\n" );
		$continue = 0;
		$dbw = wfGetDB( DB_MASTER );

		while ( $continue !== null ) {
			$continue = $this->refreshBatch( $dbw, $continue );
			wfWaitForSlaves();
			if ( $continue ) {
				$this->output( "--refreshed to post #$continue\n" );
			}
		}

		$this->output( "done. Refreshed " . $this->completeCount . " feedback posts.\n" );
	}

	/**
	 * Refreshes a batch of feedback posts
	 *
	 * @param $dbw      Database master database connection
	 * @param $continue int      [optional] the pull the next batch starting at
	 *                           this feedback id
	 */
	public function refreshBatch( $dbw, $continue ) {

		// Fetch the feedback IDs we need.
		$where = array();
		if ( $continue ) {
			$where[] = "af_id > $continue";
		}
		$id_query = $dbw->select(
			array( 'aft_article_feedback' ),
			array( 'af_id' ),
			$where,
			__METHOD__,
			array(
				'LIMIT'    => ( $this->limit + 1 ),
				'ORDER BY' => 'af_id',
			)
		);

		// Pull the continue for the next set
		$ids = array();
		$continue = null;
		foreach ( $id_query as $id ) {
			$ids[$id->af_id] = $id->af_id;
			// Get the continue values from the last counted item.
			if ( count( $ids ) == $this->limit ) {
				$continue = $id->af_id;
			}
		}
		if ( !count( $ids ) ) {
			return null;
		}
		if ( count( $ids ) > $this->limit ) {
			array_pop( $ids );
		}

		// Select rows
		$rows  = $dbw->select(
			array( 'aft_article_feedback', 'page', 'logging' ),
			array( 'af_id', 'log_id', 'log_type', 'log_action', 'log_timestamp', 'log_user', 'log_user_text', 'log_page', 'log_comment' ),
			array( 'af_id' => $ids ),
			__METHOD__,
			array(
				'ORDER BY' => 'af_id, log_id',
			),
			array(
				'page' => array(
					'JOIN', 'page_id = af_page_id'
				),
				'logging' => array(
					'LEFT JOIN',
					array (
						0 => "(log_type = 'articlefeedbackv5')
							OR (log_type = 'suppress' AND
							(log_action = 'oversight' OR
							 log_action = 'unoversight' OR
							 log_action = 'decline' OR
							 log_action = 'request' OR
							 log_action = 'unrequest'))",
						'log_namespace' => NS_SPECIAL,
						"log_title = CONCAT('ArticleFeedbackv5/', page_title, '/', af_id)"
					)
				)
			)
		);

		// Figure out what to update
		$updates  = array();
		foreach ( $rows as $row ) {
			if ( !isset ( $updates[$row->af_id] ) ) {
				$updates[$row->af_id] = array(
					'normal_count' => 0,
					'suppress_count' => 0,
				);
			}
			$updates[$row->af_id]['log_action']    = $row->log_action;
			$updates[$row->af_id]['log_user']      = $row->log_user;
			$updates[$row->af_id]['log_timestamp'] = $row->log_timestamp;
			$updates[$row->af_id]['log_comment']   = $row->log_comment;
			if ( $row->log_type == 'suppress' ) {
				$updates[$row->af_id]['suppress_count']++;
			} else {
				$updates[$row->af_id]['normal_count']++;
			}
		}

		// Update last status for each one
		$batchCount = 0;
		foreach ( $updates as $af_id => $row ) {
			if ( !isset( self::$logActionToStatus[$row['log_action']] ) ) {
				$dbw->update(
					'aft_article_feedback',
					array(
						'af_last_status'           => null,
						'af_last_status_user_id'   => null,
						'af_last_status_timestamp' => null,
						'af_last_status_notes'     => null,
						'af_activity_count'        => null,
					),
					array(
						'af_id' => $af_id,
					),
					__METHOD__
				);
			} else {
				$dbw->update(
					'aft_article_feedback',
					array(
						'af_last_status'           => self::$logActionToStatus[$row['log_action']],
						'af_last_status_user_id'   => $row['log_user'],
						'af_last_status_timestamp' => $row['log_timestamp'],
						'af_last_status_notes'     => $row['log_comment'],
						'af_activity_count'        => $row['normal_count'],
						'af_suppress_count'        => $row['suppress_count'],
					),
					array(
						'af_id' => $af_id,
					),
					__METHOD__
				);
			}
			++$batchCount;
		}
		$this->completeCount += $batchCount;

		return $continue;
	}

}

$maintClass = "ArticleFeedbackv5_RefreshLastActivity";
require_once( RUN_MAINTENANCE_IF_MAIN );

