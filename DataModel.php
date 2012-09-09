<?php
/**
 * This class represents "a data entry".
 *
 * Note that a sharded database setup is supported and we'll heavily rely
 * on cache, mainly because of the ability to do cross-shard fetching,
 * which will result in multiple/all partitionsbeing being looped for data,
 * which can & will get slow eventually. That in mind, we should take extra
 * care of thoughtful caching: not only should we scale well, we must maintain
 * high performance as well.
 *
 * Also note that I throw a lot of errors: I like to be verbose in what's wrong
 * rather than returning booleans or nulls when things break. It's equally easy
 * to try/catch for exceptions than it is to if/else return values, and the
 * exceptions provide a developer a more detailed explanation of what's wrong.
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

// @todo: use CAS to prevent race, build BagOStuff interface for it

abstract class DataModel { // @todo: interface?
	/**
	 * MUST BE EDITED BY EXTENDING CLASS
	 */

	/**
	 * To be implemented by extending class: query to fetch 1 entry from DB,
	 * with using id & shard
	 *
	 * @param RDBStoreTablePartition $partition The partition to fetch the data from
	 * @param array $conds The conditions
	 * @return ResultWrapper
	 */
	abstract protected static function loadEntriesFromDB( $partition, $conds );

	/**
	 * Database table to hold the data
	 *
	 * @var string
	 */
	protected static $table;

	/**
	 * Name of column to act as unique id
	 *
	 * @var string
	 */
	protected static $idColumn;

	/**
	 * Name of column to shard data over
	 *
	 * @var string
	 */
	protected static $shardKey;

	/**
	 * All lists the data can be displayed as
	 *
	 * Key is the filter name, the value is an array of conditions an "entry"
	 * must abide to to qualify for this list
	 *
	 * @var array
	 */
	public static $lists = array(
		'all' => array()
		// @todo: sample data?
	);


	/**
	 * CAN BE EDITED BY EXTENDING CLASS
	 */

	/**
	 * Pagination limit: how many entries should be fetched at once for lists
	 *
	 * @var int
	 */
	const LIST_LIMIT = 25;

	/**
	 * Validate the entry's data
	 *
	 * @return DataModel
	 */
	public function validate() {
		return $this;
	}

	/**
	 * Populate object's properties with database (ResultWrapper) data
	 *
	 * @param ResultWrapper $row The db row
	 * @return DataModel
	 */
	protected function toObject( $row ) {
		foreach ( get_object_vars( $row ) as $column => $row ) {
			$this->$column = $row;
		}

		return $this;
	}

	/**
	 * Get array-representation of this object, ready for use by
	 * DB wrapper
	 *
	 * @return array
	 */
	protected function toArray() {
		return get_object_vars( $this );
	}

	/**
	 * Purge relevant Squid cache when updating data
	 *
	 * @return DataModel
	 */
	public function purgeSquidCache() {
		return $this;
	}


	/**
	 * PUBLICLY INTERESTING METHODS
	 */

	/**
	 * Fetch a data entry by its id & shard key
	 *
	 * @param int $id The id of the entry to fetch
	 * @param int $shard The shard key value
	 * @return DataModel
	 */
	public static function loadFromId( $id, $shard ) {
		global $wgMemc;

		$key = wfMemcKey( __CLASS__, 'get', $id, $shard );
		$entry = $wgMemc->get( $key );

		// when not found in cache, load data from DB
		if ( $entry === false ) {
			// conditions for fetching 1 single row:
			// WHERE [id-col] = [id-val] AND [shard-col] = [shard-val]
			$conds = array( self::getIdColumn() => $id, self::getShardColumn() => $shard );

			$storeGroup = RDBStoreGroup::singleton();
			$store = $storeGroup->getForTable( self::getTable() );
			$partition = $store->getPartition( self::getTable(), $entry->{self::getShardColumn()}, $shard );

			$row = array_shift( self::loadEntriesFromDB( $partition, $conds ) );
			$entry = self::loadFromRow( $row );
		}

		return $entry;
	}

	/**
	 * Fetch a list of entries
	 *
	 * @param string $name The list name (see self::$lists)
	 * @param mixed $shard The value of the shard key to fetch data for (null for all entries)
	 * @param int $offset The offset to start from (a multiple of self::LIST_LIMIT)
	 * @return array
	 */
	public static function getList( $name, $shard, $offset = 0 ) {
		// @todo: incorporate order

		if ( !isset( self::$lists[$name] )) {
			throw new MWException( "List '$name' is no known list" );
		} elseif ( $offset % self::LIST_LIMIT != 0 ) {
			throw new MWException( 'Offset should be a multiple of ' . self::LIST_LIMIT );
		}

		/*
		 * Once requested from DB, lists are cached (and shouldn't expire,
		 * but be constantly kept up-to-date on every change) - first try
		 * to hit the cache for the requested list
		 */
		$list = self::getListFromCache( $name, $shard, $offset );

		// If no cache hits were found, fetch the data from the database
		if ( $list === false ) {
			$list = self::getListFromDB( $name, $shard, $offset );
		}

		return $list;
	}

	/**
	 * General save function - will figure out if insert/update should be performed
	 * based on the respective absence/presence of the unique id.
	 *
	 * @return DataModel
	 */
	public function save() {
		if ( $this->{self::getIdColumn()} === null ) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}

	/**
	 * Insert entry into the DB (& cache)
	 *
	 * @return DataModel
	 */
	public function insert() {
		if ( $this->{self::getIdColumn()} !== null ) {
			throw new MWException( 'Entry has unique id (' . $this->{self::getIdColumn()} . ') already - did you intend to update rather than insert?' );
		}

		// get hold of entry shard
		$storeGroup = RDBStoreGroup::singleton();
		$store = $storeGroup->getForTable( self::getTable() );
		$partition = $store->getPartition( self::getTable(), self::getShardColumn(), $this->{self::getShardColumn()} );

		/*
		 * Since entries are sharded, we can't just use the database engine's
		 * auto-increment to generate a unique id. Fetch the last entry and
		 * increment its id to form the id of the new entry.
		 */
		$list = self::getList( 'all', 0 ); // @todo: order by timestamp desc // @todo: assumes list 'all' exists
		if ( count( $list ) > 0 ) {
			$last = array_shift( $list );
			$this->{self::getIdColumn()} = $last->{self::getIdColumn()} + 1;
		} else {
			$this->{self::getIdColumn()} = 1;
		}

		// validate properties before saving them
		$this->validate();

		// insert data
		$result = $partition->insert(
			$this->toArray(),
			__METHOD__
		);

		// if successfully inserted, cache entry
		if ( $result ) {
			global $wgMemc;

			$key = wfMemcKey( __CLASS__, 'get', $this->{self::getIdColumn()}, $this->{self::getShardColumn()} );
			$wgMemc->set( $key, $this );
		}

		/*
		 * update list caches: we want to reduce db queries as much as possible,
		 * especially for lists, so live-update what's in cache already
		 */
		foreach ( self::$lists as $list => $conditions ) {
			if ( $this->matchListConditions( $list ) ) {
				// @todo: should also update lists cache
				// it'll be quite hard to live-update in the middle of some list
			}
		}

		// purge existing cache
		$this->purgeSquidCache();

		return $this;
	}

	/**
	 * Update entry in the DB (& cache)
	 *
	 * @return DataModel
	 */
	public function update() {
		if ( $this->{self::getIdColumn()} !== null ) {
			throw new MWException( "Entry has no unique id yet - did you intend to insert rather than update?" );
		}

		// get hold of entry shard
		$storeGroup = RDBStoreGroup::singleton();
		$store = $storeGroup->getForTable( self::getTable() );
		$partition = $store->getPartition( self::getTable(), self::getShardColumn(), $this->{self::getShardColumn()} );

		// validate properties before saving them
		$this->validate();

		// update data
		$result = $partition->update(
			$this->toArray(),
			array(
				self::getIdColumn() => $this->{self::getIdColumn()},
				self::getShardColumn() => $this->{self::getShardColumn()}
			),
			__METHOD__
		);

		// if successfully inserted, update entry cache
		if ( $result ) {
			global $wgMemc;

			$key = wfMemcKey( __CLASS__, 'get', $this->{self::getIdColumn()}, $this->{self::getShardColumn()} );
			$wgMemc->set( $key, $this );
		}

		// BRAINDUMP
		// @todo
		/*
		 * So, after inserting/updating entries, the list-caches are basically
		 * outdated. To keep the list caches in sync with the newly added/
		 * edited entry, we could:
		 * * live-update cache chunks
		 *   pro: no queries required
		 *   con: complex browsing & updating list chuncks
		 * * cache full lists, not chunks
		 *   pro: easy handling, no queries
		 *   con: will eventually not scale, too much to fit in memory
		 * * purge cache
		 *   pro: easy handling, will scale
		 *   con: lot of writes = lot of (intense) queries
		 * * add rollup tables
		 *   pro: relatively fast (denormalized) queries
		 *   con: rollup table will not scale, can't shard
		 */

		/*
		 * update list caches: we want to reduce db queries as much as possible,
		 * especially for lists, so live-update what's in cache already
		 */
		foreach ( self::$lists as $list => $conditions ) {
			if ( $this->matchListConditions( $list ) ) {
				// @todo: should also update lists cache
				// it'll be quite hard to live-update in the middle of some list
			}
		}

		// purge existing cache
		$this->purgeSquidCache();

		return $this;
	}

	/**
	 * Get name of table to hold the data
	 *
	 * @return string
	 */
	public static function getTable() {
		if ( !self::$table ) {
			throw new MWException( 'No table name has been set in class ' . __CLASS__ );
		}

		return self::$table;
	}

	/**
	 * Get name of column to act as unique id
	 *
	 * @return string
	 */
	public static function getIdColumn() {
		if ( !self::$idColumn ) {
			throw new MWException( 'No id column has been set in class ' . __CLASS__ );
		} elseif ( !property_exists( __CLASS__, self::$idColumn ) ) {
			throw new MWException( 'Id column does not exist in object representation in class ' . __CLASS__ );
		}

		return self::$idColumn;
	}

	/**
	 * Get name of column to shard data over
	 *
	 * @return string
	 */
	public static function getShardColumn () {
		if ( !self::$shardColumn ) {
			throw new MWException( 'No shard column has been set in class ' . __CLASS__ );
		} elseif ( !property_exists( __CLASS__, self::$shardColumn ) ) {
			throw new MWException( 'Shard column does not exist in object representation in class ' . __CLASS__ );
		}

		return self::$shardColumn;
	}


	/**
	 * INTERNALS
	 */

	/**
	 * This method will determine whether or not an entry matches a certain list
	 *
	 * @param string $list
	 * @return bool
	 */
	function matchListConditions( $list ) {
		$match = true;

		foreach ( self::$lists[$list] as $condition ) {
			$match &= eval( $condition );
		}

		return $match;
	}

	/**
	 * Build an entry from it's DB data
	 *
	 * @param ResultWrapper $row
	 * @return DataModel
	 */
	protected static function loadFromRow( $row ) {
		global $wgMemc;

		// map db data to object
		$entry = new __CLASS__();
		$entry->toObject( $row );

		$entry->validate();

		// cache object
		$key = wfMemcKey( __CLASS__, 'get', $entry->{self::getIdColumn()}, $entry->{self::getShardColumn()} );
		$wgMemc->set( $key, $entry, 60 * 60 );

		return $entry;
	}

	/**
	 * Fetch a list from the database
	 *
	 * @param string $name The list name (see self::$lists)
	 * @param mixed $shard The value of the shard key to fetch data for (null for all entries)
	 * @param int $offset The offset to start from (a multiple of self::LIST_LIMIT)
	 * @return array
	 */
	protected static function getListFromDB( $name, $shard, $offset = 0 ) {
		global $wgMemc;

		// build conditions for requested list
		$conds = self::$lists[$name]['conds'];

		/*
		 * to reduce the amount of queries/connections to be performed on db,
		 * larger-than-requested chunks will be fetched & cached, waiting
		 * to be re-used at the next offset ;)
		 */
		$batchSize = self::LIST_LIMIT * 4;
		$min = floor( $offset / $batchSize ) * $batchSize; // e.g. for 0-25, 25-50, 50-75 & 75-100: this will be 0
		$max = ceil( $offset / $batchSize ) * $batchSize; // e.g. for 0-25, 25-50, 50-75 & 75-100: this will be 100
		$options = array(
			'LIMIT' => $max
		);
		// @todo: offset - how?
		// @todo: to make offset work, it should be bound to a value that can be used in a where-clause
		// (like timestamp when sorting by order, relevance score when sorting by relevance, ...)

		if ( !$shard ) {
			$rows = array();

			/*
			 * Basically, attempting to fetch entries _without_ specifying the
			 * shard key value is quite, erm, problematic, since that'll have
			 * you looping all sharded servers.
			 * To counter the effect of this possibly ever-growing amount of db
			 * requests, all data should be thoughtfully cached.
			 */
			$storeGroup = RDBStoreGroup::singleton();
			$store = $storeGroup->getForTable( self::getTable() );
			$partitions = $store->getAllPartitions( self::getTable(), self::getShardColumn() );
			foreach ( $partitions as $partition ) {
				$partitionRows = self::loadEntriesFromDB( $partition, $conds, $options );
				$rows = array_merge( $rows, $partitionRows );

				/*
				 * note: I could do the sort & splice only once, outside the loop
				 * while that would be a wee bit faster, it would't scale well:
				 * every added server would make $rows grow by self::LIST_LIMIT * 4
				 * entries, clogging memory during this loop
				 */
				usort( $rows, array( self, 'sort' ) ); // @todo: this will also depend on order - not yet sure what approach to take
				$rows = array_splice( $rows, $min, $max );
			}
		} else {
			/*
			 * This one's easy, just grab the data from the server designated
			 * to store entries with the specified shard value.
			 */
			$conds[self::getShardColumn()] = $shard;

			$storeGroup = RDBStoreGroup::singleton();
			$store = $storeGroup->getForTable( self::getTable() );
			$partition = $store->getPartition( self::getTable(), self::getShardColumn(), $shard );

			$rows = self::loadEntriesFromDB( $partition, $conds, $options );
		}

		if ( !$rows ) {
			return array();
		}

		// build list results
		$list = array();
		foreach ( $rows as $row ) {
			$list[$row->{self::getIdColumn()}] = self::loadFromRow( $row );
		}

		// cache list results (array of id => page) in batches of 25
		$cache = array();
		$i = 0;
		foreach ( $list as $id => $entry ) {
			$cache[$id] = $entry->{self::getShardColumn()};

			if ( ++$i % self::LIST_LIMIT == 0 ) {
				$key = wfMemcKey( __CLASS__, 'getList', $name, $shard, $min + $i - self::LIST_LIMIT );
				$wgMemc->set( $key, $cache, 0 ); // cache does not expire

				$cache = array();
			}
		}

		return array_splice( $list, $offset, self::LIST_LIMIT );
	}

	/**
	 * (Try to) fetch a list straight from cache
	 *
	 * @param string $name The list name (see self::$lists)
	 * @param mixed $shard The value of the shard key to fetch data for (null for all entries)
	 * @param int $offset The offset to start from (a multiple of self::LIST_LIMIT)
	 * @return array|bool
	 */
	protected static function getListFromCache( $name, $shard, $offset = 0 ) {
		global $wgMemc;

		// fetch entry ids from cache
		$key = wfMemcKey( __CLASS__, 'getList', $name, $shard, $offset ); // @todo: will need 'sort' in here somewhere
		$entryIds = $wgMemc->get( $key );

		if ( $entryIds === false ) {
			return false;
		}

		// check for entries that are no longer in cache so we can query for them all at once!
		// @todo: this does not yet take into account the central feedback page (= all-shard)
		$missing = array();
		foreach ( $entryIds as $id => $shard ) {
			$key = wfMemcKey( __CLASS__, 'get', $name, $shard, $offset );
			if ( $wgMemc->get( $key ) === false ) {
				$missing[] = $id;
			}
		}
		if ( $missing ) {
			$conds = array(
				self::getIdColumn() => array_keys( $missing ),
				self::getShardColumn() => $shard
			);
			self::loadEntriesFromDB( /* @todo:partition */, $conds );
		}

		// build array of objects
		$entries = array();
		foreach ( $entryIds as $id => $shard ) {
			$entries[$id] = self::loadFromId( $id, $shard );
		}

		return $entries;
	}
}
