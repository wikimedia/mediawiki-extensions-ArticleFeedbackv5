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
	 * This overwrites the default parent::get method to add a join to
	 * the table that'll hold the blobs for longer texts.
	 *
	 * @param mixed $id The id(s) to fetch
	 * @param mixed $shard The corresponding shard value(s)
	 * @return ResultWrapper
	 */
	public function get( $id = null, $shard = null ) {
		// even if only 1 element (int|string), cast to array for consistent usage
		$id = !$id ? null : (array) $id;
		$shard = !$shard ? null : (array) $shard;

		// query conditions
		$conds = array();
		if ( $id ) {
			$conds[$this->idColumn] = array_unique( $id );
		}
		if ( $shard ) {
			$conds[$this->shardColumn] = array_unique( $shard );
		}

		return $this->getDB( DB_SLAVE )->select(
			array( $this->table, 'aft_feedback_blob' ),
			array(
				'*',
				'aft_id' => 'TRIM(TRAILING CHAR(0x00) FROM aft_id)', // legacy non 32 char entries will be padded to 32 chars
				'aft_comment' => 'IFNULL(aftb_comment, aft_comment)' // get from blob table if long entry exists
			),
			$conds,
			__METHOD__,
			array(),
			array(
				'aft_feedback_blob' => array(
					'LEFT JOIN',
					array(
						"aftb_id = $this->idColumn"
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

		if ( strlen( $entry->aft_comment ) > 255 ) {
			// insert full comment into large table
			$dbw->insert(
				'aft_feedback_blob',
				array(
					'aftb_id' => $entry->aft_id,
					'aftb_comment' => $entry->aft_comment,
				),
				__METHOD__
			);

			// truncate comment for regular aft table
			global $wgLang;
			$data['aft_comment'] = $wgLang->truncate( $entry->aft_comment, 255 );
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

		if ( strlen( $entry->aft_comment ) > 255 ) {
			// insert full comment into large table
			$dbw->replace(
				'aft_feedback_blob',
				array( 'aftb_id' ),
				array(
					'aftb_id' => $entry->aft_id,
					'aftb_comment' => $entry->aft_comment,
				),
				__METHOD__
			);

			// truncate comment for regular aft table
			global $wgLang;
			$data['aft_comment'] = $wgLang->truncate( $entry->aft_comment, 255 );

		// make sure no previous entry stays behind
		} else {
			$dbw->delete(
				'aft_feedback_blob',
				array(
					'aftb_id' => $entry->aft_id,
				),
				__METHOD__
			);
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
			array( 'aftb_id' => $entry->aft_id ),
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
	 * Fetch a list.
	 *
	 * @param string $name The list name (see <datamodel>::$lists)
	 * @param mixed $shard Get only data for a certain shard value
	 * @param int $offset The offset to start fetching entries from
	 * @param int $limit The amount of entries to fetch
	 * @param string $sort Sort to apply to list
	 * @param string $order Sort the list ASC or DESC
	 * @return ResultWrapper
	 */
	public function getList( $name, $shard = null, $offset = null, $limit, $sort = null, $order ) {
		$dbr = $this->getDB( DB_SLAVE );

		$tables = array();
		$vars = array();
		$conds = array();
		$options = array();
		$join_conds = array();

		$tables[] = $this->table;

		$vars[] = '*';
		$vars['aft_id'] = 'TRIM(TRAILING CHAR(0x00) FROM aft_id)'; // legacy non 32 char entries will be padded to 32 chars

		/*
		 * We're not really sharding now, so we can take the easy way;
		 * when really sharding & attempting to fetch cross-shard, multiple
		 * servers will have to be queried & the results combined ;)
		 */
		if ( $shard ) {
			$conds[$this->shardColumn] = $shard;
		}

		// "where"
		$conditions = $this->getConditions( $name );
		$conds += $conditions;

		// "order by"
		$sort = $this->getSort( $sort );
		$options['ORDER BY'] = array();
		if ( $sort ) {
			$options['ORDER BY'][] = "$sort $order";
		}
		$options['ORDER BY'][] = "$this->idColumn $order";

		// "offset"-alternative
		$vars['offset_value'] = $sort ? "CONCAT_WS('|', $sort, TRIM(TRAILING CHAR(0x00) FROM $this->idColumn))" : "TRIM(TRAILING CHAR(0x00) FROM $this->idColumn)";
		$options['LIMIT'] = $limit;
		list( $sortOffset, $idOffset ) = $this->getOffset( $offset );
		if ( $sortOffset ) {
			$direction = $order == 'ASC' ? '>' : '<';
			$sortOffset = $dbr->addQuotes( $sortOffset );
			$idOffset = $dbr->addQuotes( $idOffset );
			if ( $sort && $sortOffset ) {
				// sort offset defined; add to conditions
				$conds[] = "
					($sort $direction $sortOffset) OR
					($sort = $sortOffset AND $this->idColumn $direction= $idOffset)";
			} elseif ( !$sort && $idOffset ) {
				$conds[] = "$this->idColumn $direction= $idOffset";
			}
		}

		// aft_feedback_blob join for full comments
		$tables[] = 'aft_feedback_blob';
		$vars['aft_comment'] = 'IFNULL(aftb_comment, aft_comment)';
		$join_conds['aft_feedback_blob'] =
			array(
				'LEFT JOIN',
				array(
					"aftb_id = $this->idColumn"
				)
			);

		return $dbr->select(
			$tables,
			$vars,
			$conds,
			__METHOD__,
			$options,
			$join_conds
		);
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
