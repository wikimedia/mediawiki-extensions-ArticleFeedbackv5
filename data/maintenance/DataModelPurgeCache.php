<?php

require_once ( getenv( 'MW_INSTALL_PATH' ) !== false
	? getenv( 'MW_INSTALL_PATH' ) . '/maintenance/Maintenance.php'
	: dirname( __FILE__ ) . '/../../../../maintenance/Maintenance.php' );

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
	protected $completeCount = 0;

	/**
	 * Array of shard ids
	 *
	 * @var array
	 */
	protected $shards = array( null );

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

		$this->output( "Purging $class caches.\n" );

		// get all entries from DB
		$rows = $class::getBackend()->get( null, null );

		foreach ( $rows as $i => $row ) {
			if ( !in_array( $row->{$class::getShardColumn()}, $this->shards ) ) {
				$this->shards[] = $row->{$class::getShardColumn()};
			}

			$object = $class::loadFromRow( $row );

			$this->purgeObject( $object );

			$this->completeCount++;

			if ( $i % 50 == 0 ) {
				$this->output( "--purged caches to entry #".$object->{$class::getIdColumn()}."\n" );
				wfWaitForSlaves();
			}
		}

		foreach ( $this->shards as $i => $shard ) {
			$this->purgeShard( $shard );

			if ( $i % 50 == 0 ) {
				$this->output( "--purged caches to shard #$shard\n" );
				wfWaitForSlaves();
			}
		}

		$this->output( "Done. Purged caches for $this->completeCount $class entries.\n" );
	}

	/**
	 * Per-object cache removal
	 *
	 * @param DataModel $object The object
	 */
	public function purgeObject( DataModel $object ) {
		$object->uncache();
	}

	/**
	 * Per-shard cache removal
	 *
	 * @param mixed $shard The shard column's value
	 */
	public function purgeShard( $shard ) {
		$class = $this->getClass();

		foreach ( $class::$lists as $list => $properties ) {
			// clear lists
			$class::uncacheList( $list, $shard );

			// clear counts
			$key = wfMemcKey( $class, 'getCount', $list, $shard );
			$class::getCache()->delete( $key );
		}
	}
}

// allow extension-specific override before including this file
if ( !isset( $maintClassOverride ) || !$maintClassOverride ) {
	$maintClass = "DataModelPurgeCache";
	require_once( RUN_MAINTENANCE_IF_MAIN );
}
