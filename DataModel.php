<?php
/**
 * This class represents "a data entry".
 *
 * @todo: this description will need to be updated ;)
 * Note that a sharded database setup is supported and we'll heavily rely
 * on cache, mainly because of the ability to do cross-shard fetching,
 * which will result in multiple/all partitions being being looped for data,
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
	 * @todo: at some point, we'll need to shard over multiple columns
	 *
	 * @var string
	 */
	protected static $shardKey;

	/**
	 * All lists the data can be displayed as
	 *
	 * Key is the filter name, the value is an array containing:
	 * * the conditions an "entry" must abide to to qualify for this list
	 * * the data to be sorted on
	 *
	 * @var array
	 */
	public static $lists = array(
/*
		// sample list that would:
		// * include all entries (there are no conditions)
		// * be sorted by insertion time (no order is defined)
		'all' => array(
			'conditions' => array(),
			'order' => array()
		),
		// sample list that would:
		// * include no entries (condition will never evaluate to true: id won't be < 0)
		// * be sorted by entry id
		'none' => array(
			'conditions' => array( '$this->{self::getIdColumn()} < 0' ),
			'order' => array( '$this->{self::getIdColumn()}' )
		)
*/
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
	 * Strategy:
	 * Limit calls to the sharded data to only be on id & shard key (& index these)
	 * Other WHERE-clauses should be avoided. Instead, this "lists" concept will be used.
	 *
	 * A "list" could basically represent any query with a "special where clause", e.g.
	 * * "show all posts that have been oversighted" could be a list
	 * * "show all anonymous users" could be a list
	 * * ...
	 *
	 * We don't want to do this sort of queries on the source data table, because:
	 * * The data is sharded - cross-shard fetching is horrible
	 * * We would have to add a lot more column indexes to the source data
	 *
	 * Rather than executing these queries on the source data table, the CRUD-methods
	 * will perform these "list requirements" (basically the equivalent of WHERE-clause)
	 * in PHP and update the list entries (add/remove entries or move them around)
	 * These requirements can be defined on a per-list basis, at self::$lists
	 *
	 * These lists will be stored in the database, sharded by list name. This leaves us
	 * vulnerable to scalability issues in the event that one list would grow way out
	 * of proportion. Since the lists only contain id references though, the chance of
	 * this table hitting hardware limits are extremely small.
	 *
	 * These list results are then also saved in cache (in small chunks) so that commonly
	 * accessed lists don't overload the database.
	 *
	 * To keep reducing database connections/queries, we'll be slightly over-fetching
	 * data. Assuming one is requesting the first 25 entries, it is likely that the next
	 * 25 will be requested as well. We'll be fetching more than requested right away
	 * (since that's relatively cheap) and save this larger chunk to cache, which will
	 * enable us to do fewer queries when that data is finally requested.
	 *
	 * Note: one of the drawbacks of this approach is that a list can not be created
	 * "on the fly": we can't just apply a new SQL statement with a new WHERE-
	 * clause (because that's replaced by a PHP conditions). To add a new list when data
	 * already exists, a maintenance script will have to be run to re-populate that list.
	 *
	 * @todo: Currently still struggling to come up with a solution for data with a
	 * variable WHERE-clause (e.g. "show all users with a creation date under 30 days
	 * ago", where the exact value of "30 days ago" changes every second)
	 *
	 * @param string $name The list name (see self::$lists)
	 * @param int $offset The offset to start from (a multiple of self::LIST_LIMIT)
	 * @param string $sort Sort the list ASC or DESC
	 * @return array
	 */
	public static function getList( $name, $offset = 0, $sort = 'ASC' ) {
		global $wgMemc;

		if ( !isset( self::$lists[$name] )) {
			throw new MWException( "List '$name' is no known list" );
		} elseif ( $offset % self::LIST_LIMIT != 0 ) {
			throw new MWException( 'Offset should be a multiple of ' . self::LIST_LIMIT );
		} elseif ( !in_array( $sort, array( 'ASC', 'DESC' ) ) ) {
			throw new MWException( 'Sort should be either ASC or DESC' );
		}

		// internal key to identify this exact list by
		$key = wfMemcKey( __CLASS__, 'getList', $name, $offset, $sort );

		// (try to) fetch list from cache
		$list = $wgMemc->get( $key );
		if ( $list === false ) {
			/*
			 * to reduce the amount of queries/connections to be performed on db,
			 * larger-than-requested chunks will be fetched & cached, waiting
			 * to be re-used at the next offset ;)
			 */
			$batchSize = self::LIST_LIMIT * 4;
			$min = floor( $offset / $batchSize ) * $batchSize; // e.g. for 0-25, 25-50, 50-75 & 75-100: this will be 0

			$list = self::getListFromDB( wfMemcKey( __CLASS__, 'getList', $name ), $min, $batchSize, $sort );

			// save results to cache
			$wgMemc->set( $key, $list, 60 * 60 );
		}

		/*
		 * $list now contains an array of [id] => [shard] entries
		 * we'll now want to fetch the actual feedback data for these entries
		 * some entries might already be cached, don't bother fetching those from db
		 */
		$missing = array();
		foreach ( $list as $id => $shard ) {
			$key = wfMemcKey( __CLASS__, 'get', $name, $shard, $offset );
			if ( $wgMemc->get( $key ) === false ) {

				/*
				 * while not encouraged, it is possible that a list should contain cross-shard
				 * data (e.g. simply a list of all entries) - separate all entries by shard
				 */
				$missing[$shard][] = $id;
			}
		}

		/*
		 * $missing now contains an array of [shard] => [array of ids} entries
		 * now go fetch the missing entries from the database
		 */
		$storeGroup = RDBStoreGroup::singleton();
		$store = $storeGroup->getForTable( self::getTable() );

		foreach ( $missing as $shard => $ids ) {
			/*
			 * @todo: talk about this to Aaron:
			 * I could for example have 2 shard-values here that live on the same server
			 * there currently is no way to know that these 2 values are on the same server,
			 * so I can't combine the queries - maybe we should add a method ->getShardIndex( $key )
			 * to figure out what server this data is living on, so queries can be grouped ;)
			 */
			$partition = $store->getPartition( self::getTable(), self::getShardColumn(), $shard );

			$conds = array(
				self::getIdColumn() => $ids,
				self::getShardColumn() => $shard
			);
			self::loadEntriesFromDB( $partition, $conds );
		}

		/*
		 * at this point, all entries should be in cache: splice the part we
		 * requested and load return those entries
		 */
		$list = array_splice( $list, $offset - $min, self::LIST_LIMIT );
		foreach ( $list as $id => $shard ) {
			$list[$id] = self::loadFromId( $id, $shard );
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
		$list = self::getList( 'all', 0, 'DESC' ); // @todo: order by timestamp desc // @todo: assumes list 'all' exists
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
			$wgMemc->set( $key, $this, 60 * 60 );
		}

		// @todo: code below still needs work and needs to be spun of into its own function ;)

		// insert entry into lists
		foreach ( self::$lists as $list => $properties ) {
			if ( !isset( $properties['conditions'] ) ) {
				$properties['conditions'] = array();
			}
			if ( !isset( $properties['order'] ) ) {
				$properties['order'] = null;
			}

			// check if entry complies to list conditions
			$comply = true;
			foreach ( (array) $properties['conditions'] as $condition ) {
				$comply &= eval( $condition ); // ieuw - eval :o
			}

			if ( $comply ) {
				$key = wfMemcKey( __CLASS__, 'getList', $list ); // @todo: I'm not too fond of the current list naming scheme in db ;)

				// compile order
				$order = '@todo'; // @todo

				// add to list (insert into db)
				$storeGroup = RDBStoreGroup::singleton();
				$store = $storeGroup->getForTable( 'lists' );
				$partition = $store->getPartition( 'lists', 'list_name', $key );
				$partition->insert(
					array(
						'entry_list' => $key,
						'entry_id' => $this->{self::getIdColumn()},
						'entry_shard' => $this->{self::getShardColumn()},
						'entry_order' => $order
					),
					__METHOD
				);

				// @todo: purge list caches
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
			$wgMemc->set( $key, $this, 60 * 60 );
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
	 * Fetch a list from the database (and save to cache)
	 *
	 * @param string $key The internal list identifier
	 * @param int $offset The offset to start fetching entries from
	 * @param int $limit The amount of entries to fetch
	 * @param string $sort Sort the list ASC or DESC
	 * @return array
	 */
	protected static function getListFromDB( $key, $offset, $limit, $sort ) {
		$storeGroup = RDBStoreGroup::singleton();
		$store = $storeGroup->getForTable( 'lists' );
		$partition = $store->getPartition( 'lists', 'list_name', $key );

		// fetch the entry ids for the requested list
		$entries = $partition->select(
			DB_SLAVE,
			array( 'entry_id', 'entry_shard' ),
			array( 'entry_list' => $key ),
			__METHOD__,
			array(
				'LIMIT' => $limit,
				'OFFSET' => $offset,
				'ORDER BY' => "entry_order $sort"
			)
		);

		if ( !$entries ) {
			return array();
		}

		// build [id] => [shard] array for requested part of list
		$list = array();
		foreach ( $entries as $entry ) {
			$list[$entry->entry_id] = $entry->entry_shard;
		}

		return $list;
	}
}
