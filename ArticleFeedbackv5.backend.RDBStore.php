<?php
/**
 * AFTv5-specific implementation to power DataModel, based on RDBStore.
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
class ArticleFeedbackv5BackendRDBStore extends DataModelBackendRDBStore {
	/*
	 * @todo: will need to extend ::get, ->insert, ->update & ->delete
	 * to be compatible with aft_feedback_blob table; I did not go through
	 * the trouble of doing that myself, since this code has become utterly
	 * useless after RDBStore's abandonment.
	 */

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
		$storeGroup = RDBStoreGroup::singleton();
		$store = $storeGroup->getForTable( $this->table );

		// build where condition
		$where = array();
		$where['rating'] = 1;
		if ( $pageId !== null) {
			$where[$this->shardColumn] = $pageId;
			$partitions = array( $store->getPartition( $this->table, $this->shardColumn, $pageId ) );
		} else {
			$partitions = $store->getAllPartitions( $this->table, $this->shardColumn );
		}

		$count = 0;
		foreach ( $partitions as $partition ) {
			$count += (int) $partition->selectField(
				DB_SLAVE,
				array( 'COUNT('.$this->idColumn.')' ),
				$where,
				__METHOD__,
				array()
			);
		}

		return $count;
	}
}
