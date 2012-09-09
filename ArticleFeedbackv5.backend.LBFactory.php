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
	 * Alternative for wfGetDB, since AFT's data can be put on separate cluster.
	 *
	 * @param $db Integer: index of the connection to get. May be DB_MASTER for the
	 *            master (for write queries), DB_SLAVE for potentially lagged read
	 *            queries, or an integer >= 0 for a particular server.
	 * @param $groups Mixed: query groups. An array of group names that this query
	 *                belongs to. May contain a single string if the query is only
	 *                in one group.
	 * @param $wiki String: the wiki ID, or false for the current wiki
	 */
	protected function getDB( $db, $groups = array(), $wiki = false ) {
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
	 * This overwrites the default parent::get method to add a join to
	 * the table that'll hold the blobs for longer texts.
	 *
	 * @param mixed $id The id(s) to fetch
	 * @param mixed $shard The corresponding shard value(s)
	 * @return ResultWrapper
	 */
	public function get( $id = null, $shard = null ) {
		$dbr = $this->getDB( DB_SLAVE );
		$tableName = $dbr->tableName( $this->table );

		// even if only 1 element (int|string), cast to array for consistent usage
		$id = !$id ? null : (array) $id;
		$shard = !$shard ? null : (array) $shard;

		// query conditions
		$conds = array();
		if ( $id ) {
			$conds["$tableName.$this->idColumn"] = $id;
		}
		if ( $shard ) {
			$conds["$tableName.$this->shardColumn"] = $shard;
		}

		return $this->getDB( DB_SLAVE )->select(
			array( $this->table, 'aft_feedback_blob' ),
			array( '*', "IFNULL(aft_feedback_blob.comment, $tableName.comment) AS comment", "$tableName.id AS id" ),
			$conds,
			__METHOD__,
			array(),
			array(
				'aft_feedback_blob' => array(
					'LEFT JOIN',
					array(
						"aft_feedback_blob.id = $tableName.id"
					)
				)
			)
		);
	}

	/**
	 * Insert entry.
	 *
	 * This overwrites the default parent::insert method to insert long
	 * comments into a separate aft_feedback_blob table
	 *
	 * @param DataModel $entry
	 * @return int
	 */
	public function insert( DataModel $entry ) {
		$dbw = $this->getDB( DB_MASTER );

		$data = $entry->toArray();

		if ( strlen( $entry->comment ) > 255 ) {
			// insert full comment into large table
			$dbw->insert(
				'aft_feedback_blob',
				array(
					'id' => $entry->id,
					'comment' => $entry->comment,
				),
				__METHOD__
			);

			// truncate comment for regular aft table
			global $wgLang;
			$data['comment'] = $wgLang->truncate( $entry->comment, 255 );
		}

		return $dbw->insert(
			$this->table,
			$data,
			__METHOD__
		);
	}

	/**
	 * Update entry.
	 *
	 * This overwrites the default parent::update method to insert long
	 * comments into a separate aft_feedback_blob table
	 *
	 * @param DataModel $entry
	 * @return int
	 */
	public function update( DataModel $entry ) {
		$dbw = $this->getDB( DB_MASTER );

		$data = $entry->toArray();
		unset( $data[$this->shardColumn] );

		if ( strlen( $entry->comment ) > 255 ) {
			// insert full comment into large table
			$dbw->replace(
				'aft_feedback_blob',
				array( 'id' ),
				array(
					'id' => $entry->id,
					'comment' => $entry->comment,
				),
				__METHOD__
			);

			// truncate comment for regular aft table
			global $wgLang;
			$data['comment'] = $wgLang->truncate( $entry->comment, 255 );
		}

		return $dbw->update(
			$this->table,
			$data,
			array(
				$this->idColumn => $entry->{$this->idColumn},
				$this->shardColumn => $entry->{$this->shardColumn}
			),
			__METHOD__
		);
	}

	/**
	 * Delete entry.
	 *
	 * This overwrites the default parent::delete method to delete long
	 * comments from the separate aft_feedback_blob table
	 *
	 * @param DataModel $entry
	 * @return int
	 */
	public function delete( DataModel $entry ) {
		$dbw = $this->getDB( DB_MASTER );

		// delete full comment (if any)
		$dbw->delete(
			'aft_feedback_blob',
			array( 'id' => $entry->id ),
			__METHOD__
		);

		return $dbw->delete(
			$this->table,
			array(
				$this->idColumn => $entry->{$this->idColumn},
				$this->shardColumn => $entry->{$this->shardColumn}
			),
			__METHOD__
		);
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
		$where['rating'] = 1;
		if ( $pageId !== null) {
			$where[$this->shardColumn] = $pageId;
		}

		return (int) $this->getDB( DB_SLAVE )->selectField(
			$this->table,
			array( 'COUNT('.$this->idColumn.')' ),
			$where,
			__METHOD__,
			array()
		);
	}
}
