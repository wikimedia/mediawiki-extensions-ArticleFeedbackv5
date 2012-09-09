<?php

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . '/maintenance/Maintenance.php'
	: dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * This will purge all DataModel caches.
 *
 * @ingroup Maintenance
 */
class DataModelPurgeCache extends Maintenance {
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
		$this->addOption( 'model', 'Classname of the model to purge caches for', true, true );
		$this->mDescription = 'Purge all DataModel caches.';
	}

	/**
	 * @return string
	 */
	public function getClass() {
		return $this->getOption( 'model' );
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		$class = $this->getClass();
		$shards = array( null );

		$this->output( "Purging $class caches.\n" );

		// get all entries from DB
		$rows = $class::getBackend()->get( null, null );

		foreach ( $rows as $i => $row ) {
			if ( !in_array( $row->{$class::getShardColumn()}, $shards ) ) {
				$shards[] = $row->{$class::getShardColumn()};
			}

			// clear cached entries
			$entry = $class::loadFromRow( $row );
			$entry->uncache();

			$this->completeCount++;

			wfWaitForSlaves();

			if ( $i % 50 == 0 ) {
				$this->output( "--purged caches to entry #".$entry->{$class::getIdColumn()}."\n" );
			}
		}

		foreach ( $class::$lists as $list => $properties ) {
			foreach ( $shards as $shard ) {
				// clear lists
				$key = wfMemcKey( get_called_class(), 'getListValidity', $list, $shard );
				$class::getCache()->delete( $key );

				// clear counts
				$key = wfMemcKey( $class, 'getCount', $list, $shard );
				$class::getCache()->delete( $key );
			}

			$this->output( "--purged caches for list #".$list."\n" );
		}

		$this->output( "Done. Purged caches for $this->completeCount $class entries.\n" );
	}
}

$maintClass = "DataModelPurgeCache";
require_once( RUN_MAINTENANCE_IF_MAIN );
