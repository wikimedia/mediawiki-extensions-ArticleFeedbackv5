<?php
/**
 * ArticleFeedbackv5_ArchiveFeedback class
 *
 * @package    ArticleFeedbackv5
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . '/maintenance/Maintenance.php'
	: dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Mark old feedback that is not particularly interesting as archived.
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_ArchiveFeedback extends Maintenance {
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
		$this->mDescription = 'Mark old feedback that is not particularly interesting as archived.';
	}

	/**
	 * Even though (theoretically) we could use "aft_archive_date >= NOW()" condition
	 * on wiki's with no cache configured, I deliberately did not make that possible.
	 * The problem when using cache is that that the result of that condition changes
	 * outside of our control. As seconds go by and more and more articles are to be
	 * considered archived, no code is executed, so we can't purge/update caches to
	 * reflect the changes.
	 * We'll be using "aft_archive = 1" to determine if feedback is or is not archived.
	 * This script will run periodically and will evaluate all entries to
	 * "aft_archive_date >= NOW()", and set "aft_archive = 1" for those. That way,
	 * it works similar to any other action plus caches will update nicely.
	 */
	public function execute() {
		$this->output( "Marking old feedback as archived.\n" );

		$backend = ArticleFeedbackv5Model::getBackend();
		while ( true ) {
			$break = true;

			$list = $backend->getList( 'archive_scheduled', null, 0, $this->limit, 'age', 'ASC' );

			foreach ( $list as $row ) {
				$feedback = ArticleFeedbackv5Model::loadFromRow( $row );

				$timestamp = wfTimestamp( TS_UNIX, $feedback->aft_timestamp );
				$archiveDate = wfTimestamp( TS_UNIX, $feedback->aft_archive_date );
				$days = round( ( $archiveDate - $timestamp )  / ( 60 * 60 * 24 ) );
				$note = wfMessage( 'articlefeedbackv5-activity-note-archive', $days )->escaped();

				$flagger = new ArticleFeedbackv5Flagging( null, $feedback->aft_id, $feedback->aft_page );
				$flagger->run( 'archive', $note, false, 'job' );

				$this->completeCount++;

				$break = false;
			}

			if ( $break ) {
				break;
			}

			wfWaitForSlaves();
			$this->output( "--moved to entry #$feedback->aft_id\n" );
		}

		$this->output( "Done. Marked " . $this->completeCount . " entries as archived.\n" );

		global $wgArticleFeedbackAutoArchiveEnabled;
		if ( !$wgArticleFeedbackAutoArchiveEnabled ) {
			$this->output( 'IMPORTANT! The "archived" filter is currently not displayed. To enable, set $wgArticleFeedbackAutoArchiveEnabled = true.'."\n" );
		}
	}
}

$maintClass = "ArticleFeedbackv5_ArchiveFeedback";
require_once( RUN_MAINTENANCE_IF_MAIN );
