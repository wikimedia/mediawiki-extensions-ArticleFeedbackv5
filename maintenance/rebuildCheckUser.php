<?php
/**
 * ArticleFeedbackv5_RebuildCheckUser class
 *
 * @package    ArticleFeedbackv5
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Rebuild AFT's CheckUser entries
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_RebuildCheckUser extends Maintenance {

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
		$this->mDescription = 'Rebuild checkuser actiontext & logging usernames (based on checkuser data)';
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		$this->output( "Updating entries\n" );

		$continue = 0;

		while ( $continue !== null ) {
			$continue = $this->refreshBatch( $continue );
			wfWaitForSlaves();

			if ( $continue ) {
				$this->output( "--refreshed to entry #$continue\n" );
			}
		}

		$this->output( "done. Refreshed " . $this->completeCount . " entries.\n" );
	}

	/**
	 * Refreshes a batch of logging usernames
	 *
	 * @param $continue int      [optional] the pull the next batch starting at
	 *                           this log_id
	 */
	public function refreshBatch( $continue ) {
		$dbw = wfGetDB( DB_MASTER );
		$dbr = wfGetDB( DB_SLAVE );

		$rows = $dbr->select(
			array( 'logging', 'cu_changes' ),
			array(
				'log_id',
				'log_type',
				'log_action',
				'log_timestamp',
				'log_user',
				'log_user_text',
				'log_namespace',
				'log_title',
				'log_page',
				'log_comment',
				'log_params',
				'log_deleted',
				'cuc_id',
				'cuc_ip'
			),
			array(
				"log_id > $continue",
				'log_title LIKE "ArticleFeedbackv5/%"',
				'log_namespace' => NS_SPECIAL
			),
			__METHOD__,
			array(
				'LIMIT'    => $this->limit,
				'ORDER BY' => 'log_id',
			),
			array(
				'cu_changes' => array(
					'INNER JOIN', array(
						'log_namespace = cuc_namespace',
						'log_title = cuc_title',
						'log_timestamp = cuc_timestamp',
						'log_user = cuc_user',
						'log_user_text = cuc_user_text'
					)
				)
			)
		);

		$continue = null;

		foreach ( $rows as $row ) {
			$continue = $row->log_id;

			$update = array();

			// fix log entry usernames: anon actions have at times been
			// identified as "Article Feedback V5" rather than as IP
			if ( $row->log_user_text == 'Article Feedback V5'
				&& $row->log_comment != 'Automatic un-hide'
				&& !in_array( $row->log_type, array( 'autohide', 'autoflag' ) ) ) {
				$dbw->update(
					'logging',
					array( 'log_user_text' => $row->cuc_ip ),
					array( 'log_id' => $row->log_id )
				);

				$update['cuc_user_text'] = $row->cuc_ip;
			}

			// fix bad action texts for AFT: the native formatter getPlainActionText
			// did not support AFT entries too well, leaving it packed with
			// escaped html entities, causing a horrible display
			$formatter = LogFormatter::newFromRow( $row );
			if ( $formatter ) {
				$update['cuc_actiontext'] = $formatter->getPlainActionText();
			}

			// update checkuser entry
			$dbw->update(
				'cu_changes',
				$update,
				array( 'cuc_id' => $row->cuc_id )
			);

			$this->completeCount++;
		}

		return $continue;
	}
}

$maintClass = "ArticleFeedbackv5_RebuildCheckUser";
require_once( RUN_MAINTENANCE_IF_MAIN );
