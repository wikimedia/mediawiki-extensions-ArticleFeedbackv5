<?php
/**
 * AFTv5-specific implementation to power DataModel, based on RDBStore.
 *
 * This class will connect to a single database setup with master/slaves
 * architecture.
 *
 * @todo: because RDBStore is abandoned, I did not go through the "trouble"
 * of making this code compatible with aft_feedback_blob table. As far as
 * this code is concerned, it will assume aft_feedback can store the full
 * comment.
 *
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
class ArticleFeedbackv5BackendRDBStore extends DataModelBackendRDBStore {
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
