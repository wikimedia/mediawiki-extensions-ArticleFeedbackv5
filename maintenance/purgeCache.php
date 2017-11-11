<?php

/**
 * DataModelPurgeClass is a good starting point to purge all caches;
 * extend that class and add some more caches to be clear here.
 */
$maintClassOverride = true;
require_once __DIR__ . '/../data/maintenance/DataModelPurgeCache.php';

/**
 * This will purge all ArticleFeedbackv5 caches.
 *
 * @ingroup Maintenance
 */
class ArticleFeedbackv5_PurgeCache extends DataModelPurgeCache {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->requireExtension( 'ArticleFeedbackv5' );

		$this->deleteOption( 'model' );
		$this->mDescription = 'Purge all ArticleFeedbackv5 caches.';
	}

	/**
	 * @return string
	 */
	public function getClass() {
		return 'ArticleFeedbackv5Model';
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		parent::execute();

		// clear max user id
		global $wgMemc;
		$wgMemc->delete( wfMemcKey( 'articlefeedbackv5', 'maxUserId' ) );
	}

	/**
	 * Per-object cache removal
	 *
	 * @param ArticleFeedbackv5Model $object The object
	 */
	public function purgeObject( DataModel $object ) {
		parent::purgeObject( $object );

		global $wgMemc;

		// feedback activity count per permission
		global $wgArticleFeedbackv5Permissions;
		foreach ( $wgArticleFeedbackv5Permissions as $permission ) {
			$key = wfMemcKey( 'articlefeedbackv5', 'getActivityCount', $permission, $object->aft_id );
			$wgMemc->delete( $key );
		}

		// feedback last editor activity
		$key = wfMemcKey( 'ArticleFeedbackv5Activity', 'getLastEditorActivity', $object->aft_id );
		$wgMemc->delete( $key );
	}

	/**
	 * Per-shard cache removal
	 *
	 * @param mixed $shard The shard column's value
	 */
	public function purgeShard( $shard ) {
		parent::purgeShard( $shard );

		$class = $this->getClass();

		// clear page found percentage
		$key = wfMemcKey( $class, 'getCountFound', $shard );
		$class::getCache()->delete( $key );
	}
}

$maintClass = 'ArticleFeedbackv5_PurgeCache';
require_once RUN_MAINTENANCE_IF_MAIN;
