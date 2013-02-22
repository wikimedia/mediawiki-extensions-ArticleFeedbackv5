<?php
/**
 * ArticleFeedbackv5_SetArchiveDate class
 *
 * @package    ArticleFeedbackv5
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . '/maintenance/Maintenance.php'
	: dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Set an archive date for feedback posts, before archive was introduced.
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_SetArchiveDate extends LoggedUpdateMaintenance {
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
		$this->mDescription = 'Fix archive dates of pre-auto-archive feedback.';
	}

	/**
	 * Installing archive.sql will create the schema changes necessary for auto-archive to work.
	 * Old feedback, however, has no archive date set. The SQL we ran to install the schema has
	 * added dates to the to-archive feedback already, but these dates are not 100% correct.
	 * This script will fix these dates.
	 *
	 * @return bool
	 */
	protected function doDBUpdates() {
		$this->output( "Fixing archive dates.\n" );

		/**
		 * Temporarily create a bogus filter that is more of an aid to use the model's
		 * built-in functions to query for stuff that has not yet been archived but is due.
		 */
		$now = wfTimestampNow();
		ArticleFeedbackv5Model::$lists['archive_scheduled'] = array(
			'permissions' => 'aft-noone',
			'conditions' => array( 'aft_archive = 0', "aft_archive_date <= '$now'" ),
		);

		$next = null;

		$backend = ArticleFeedbackv5Model::getBackend();
		while ( true ) {
			$break = true;

			$list = $backend->getList( 'archive_scheduled', null, $next, $this->limit, 'age', 'ASC' );

			foreach ( $list as $row ) {
				$feedback = ArticleFeedbackv5Model::loadFromRow( $row );

				$offset = ( isset( $row->offset_value ) ? $row->offset_value . '|' : '' ) . $feedback->aft_id;

				// there's 1 item overlap (the offset is where a next search starts from) - ignore the one we just processed
				if ( $next === $offset ) {
					continue;
				}

				// clear out the archive date & the correct one will be set
				$feedback->aft_archive_date = null;
				$feedback->update();

				$this->completeCount++;
				$break = false;
			}

			wfWaitForSlaves();
			$this->output( "--updated to entry #$feedback->aft_id\n" );
			$next = $offset;

			if ( $break ) {
				break;
			}
		}

		$this->output( "Done. Fixed " . $this->completeCount . " entries' archive dates.\n" );

		global $wgArticleFeedbackAutoArchiveEnabled;
		if ( !$wgArticleFeedbackAutoArchiveEnabled ) {
			$this->output( 'IMPORTANT! The "archived" filter is currently not displayed. To enable, set $wgArticleFeedbackAutoArchiveEnabled = true.'."\n" );
		}

		return true;
	}

	/**
	 * Get the update key name to go in the update log table
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return 'ArticleFeedbackv5_SetArchiveDate';
	}
}

$maintClass = "ArticleFeedbackv5_SetArchiveDate";
require_once( RUN_MAINTENANCE_IF_MAIN );
