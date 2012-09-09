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
