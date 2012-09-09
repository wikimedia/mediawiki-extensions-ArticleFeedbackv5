<?php
/**
 * AFTv5-specific implementation to power DataModel, allowing AFTv5 to be
 * put on a separate cluster.
 *
 * This class will connect to a single database setup with master/slaves
 * architecture.
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
class ArticleFeedbackv5BackendLBFactory extends DataModelBackendLBFactory {
	/**
	 * Override getDB so that AFT's data can be on a separate cluster.
	 *
	 * @param $db Integer: index of the connection to get. May be DB_MASTER for the
	 *            master (for write queries), DB_SLAVE for potentially lagged read
	 *            queries, or an integer >= 0 for a particular server.
	 * @param $groups Mixed: query groups. An array of group names that this query
	 *                belongs to. May contain a single string if the query is only
	 *                in one group.
	 * @param $wiki String: the wiki ID, or false for the current wiki
	 */
	public function getDB( $db, $groups = array(), $wiki = false ) {
		global $wgArticleFeedbackv5Cluster;

		// connect to external, aft-specific, cluster
		if ( $wgArticleFeedbackv5Cluster ) {
			return wfGetLBFactory()->getExternalLB( $wgArticleFeedbackv5Cluster )->getConnection( $db, $groups, $wiki );
		}

		// plain old wfGetDB
		return parent::getDB( $db, $groups, $wiki );
	}

	/**
	 * Query to fetch entries from DB.
	 *
	 * @param mixed $id The id(s) to fetch, either a single id or an array of them
	 * @param mixed $shard The corresponding shard value(s)
	 * @return ResultWrapper
	 */
	public function get( $id = null, $shard = null ) {
		$ids = null;
		if ( $id != null ) {
			$ids = (array) $id;
		}

		$ids = array_map( $this->standardizeId, $ids );

		return parent::get( $ids, $shard );
	}

	/**
	 * Update entry.
	 *
	 * @param DataModel $entry
	 * @return int
	 */
	public function update( DataModel $entry ) {
		/*
		 * The clone will make sure it's no longer the same object referenced
		 * inside DataModel.
		 */
		$entry = clone( $entry );
		$entry->{$this->idColumn} = $this->standardizeId( $entry->{$this->idColumn} );

		return parent::update( $entry );
	}

	/**
	 * Delete entry.
	 *
	 * @param DataModel $entry
	 * @return int
	 */
	public function delete( DataModel $entry ) {
		/*
		 * The clone will make sure it's no longer the same object referenced
		 * inside DataModel.
		 */
		$entry = clone( $entry );
		$entry->{$this->idColumn} = $this->standardizeId( $entry->{$this->idColumn} );

		return parent::delete( $entry );
	}

	/**
	 * Evaluate an entry to possible conditions.
	 *
	 * Before updating data, DataModel will want to re-evaluate en entry to
	 * all possible conditions, to know which caches need to be purged/updated.
	 *
	 * @param DataModel $entry
	 * @return ResultWrapper
	 */
	public function evaluateConditions( DataModel $entry ) {
		/*
		 * The clone will make sure it's no longer the same object referenced
		 * inside DataModel.
		 */
		$entry = clone( $entry );
		$entry->{$this->idColumn} = $this->standardizeId( $entry->{$this->idColumn} );

		return parent::evaluateConditions( $entry );
	}

	/**
	 * For a given list name, this will fetch the list's conditions.
	 *
	 * @param string $name The list name (see <datamodel>::$lists)
	 * @return string
	 */
	public function getConditions( $name ) {
		$class = $this->datamodel;

		$conditions = array();
		if ( isset( $class::$lists[$name]['conditions'] ) ) {
			$conditions = $class::$lists[$name]['conditions'];
		}

		if ( empty( $conditions ) ) {
			$conditions = array();
		}

		return $conditions;
	}

	/**
	 * Get the amount of people who marked "yes" to the question if they
	 * found what the were looking for.
	 *
	 * This is quite an expensive function, whose result should be cached.
	 *
	 * @param int[optional] The page id
	 * @return int
	 */
	public function getCountFound( $pageId = null ) {
		// build where condition
		$where = array();
		$where['aft_rating'] = 1;
		if ( $pageId !== null) {
			$where['aft_page'] = $pageId;
		}

		return (int) $this->getDB( DB_SLAVE )->selectField(
			$this->table,
			array( 'COUNT(*)' ),
			$where,
			__METHOD__,
			array()
		);
	}

	/**
	 * ID is saved as binary(32), but all older id values (of feedback
	 * prior to major code refactor, that have been migrated) will
	 * remain unchanged, which will result in MySQL padding them to 32
	 * length with null-bytes. Make sure out values are re-padded to
	 * 32 characters before looking for them in the database.
	 *
	 * @param string $id The id
	 * @return string
	 */
	protected function standardizeId( $id ) {
		return str_pad( $id, 32, chr( 0 ) );
	}
}
