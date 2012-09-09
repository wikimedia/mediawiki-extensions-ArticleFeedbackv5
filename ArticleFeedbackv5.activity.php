<?php
/**
 * This class provides some functionality to easily get feedback's activity.
 * Because this data is less often requested & because we're dealing with
 * the default MW logging table (which we can't "just change"), this is a
 * completely different approach than ArticleFeedbackv5Model:
 * - no datamodel/sharded data
 * - few cache
 * - no "lists"
 * - queries, queries, queries
 * - ...
 *
 * @package    ArticleFeedback
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
class ArticleFeedbackv5Activity {
	/**
	 * Gets the last $limit of activity rows taken from the log table,
	 * starting from point $continue, sorted by time - latest first
	 *
	 * @param ArticleFeedbackv5Model $feedback identifier for the feedback item we are fetching activity for
	 * @param User[optional] $feedback identifier for the feedback item we are fetching activity for
	 * @param int[optional] $limit total limit number
	 * @param string[optional] $continue used for offsets
	 * @return array db record rows
	 */
	public static function getList( $feedback, $user = null, $limit = 25, $continue = null ) {
		global $wgLogActionsHandlers;
		$dbr = wfGetDB( DB_SLAVE );

		$feedbackId = $feedback->aft_id;
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			throw new MWException( 'Page for feedback does not exist', 'invalidfeedbackid' );
		}
		$title = $page->getDBKey();

		if ( !$user ) {
			global $wgUser;
			$user = $wgUser;
		}

		// can only see activity for actions that you have permissions to perform
		$actions = array();
		foreach( ArticleFeedbackv5Model::$actions as $action => $options ) {
			if ( ArticleFeedbackv5Model::canPerformAction( $action, $user ) ) {
				if ( isset( $wgLogActionsHandlers["suppress/$action"] ) ) {
					$type = 'suppress';
				} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$action"] ) ) {
					$type = 'articlefeedbackv5';
				} else {
					continue;
				}
				$actions[] = 'log_type = '.$dbr->addQuotes( $type ).' AND log_action = '.$dbr->addQuotes( $action );
			}
		}

		// nothing to get? return empty resultset
		if ( !$actions ) {
			return new FakeResultWrapper( array() );
		}

		$where[] = '('.implode( ') OR (', $actions ).')';
		$where['log_namespace'] = NS_SPECIAL;
		$where['log_title'] = "ArticleFeedbackv5/$title/$feedbackId";
		$where = self::applyContinue( $continue, $where );

		$activity = $dbr->select(
			array( 'logging' ),
			array(
				'log_id',
				'log_action',
				'log_timestamp',
				'log_user',
				'log_user_text',
				'log_title',
				'log_comment'
			),
			$where,
			__METHOD__,
			array(
				'LIMIT' => $limit + 1,
				'ORDER BY' => 'log_timestamp DESC',
				// Force the page_time index (on _namespace, _title, _timestamp)
				// We don't expect many if any rows for Special:ArticleFeedbackv5/foo that
				// don't match log_type='articlefeedbackv5' , so we can afford to have that
				// clause be unindexed. The alternative is to have the log_type clause be indexed
				// and the namespace/title clauses unindexed, that would be bad.
				'USE INDEX' => 'page_time'
			)
		);

		return $activity;
	}

	/**
	 * Creates a timestamp/id tuple for continue
	 *
	 * @param ResultWrapper $row
	 * @return string
	 */
	public static function getContinue( $row ) {
		$ts = wfTimestamp( TS_MW, $row->log_timestamp );
		return "$ts|{$row->log_id}";
	}

	/**
	 * Gets timestamp and id pair for continue
	 *
	 * @param string $continue
	 * @param array $where
	 * @return array
	 */
	protected static function applyContinue( $continue, $where ) {
		if ( !$continue ) {
			return $where;
		}

		$values = explode( '|', $continue, 3 );
		if ( count( $values ) !== 2 ) {
			throw new MWException( 'Invalid continue param. You should pass the original value returned by the previous query', 'badcontinue' );
		}

		$db = wfGetDB( DB_SLAVE );
		$ts = $db->addQuotes( $db->timestamp( $values[0] ) );
		$id = intval( $values[1] );
		$where[] = '(log_id = ' . $id . ' AND log_timestamp <= ' . $ts . ') OR log_timestamp < ' . $ts;

		return $where;
	}
}
