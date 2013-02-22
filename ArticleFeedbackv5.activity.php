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
	 * The map of flags to permissions.
	 * If an action is not mentioned here, it is not tied to specific permissions
	 * and everyone is able to perform the action.
	 *
	 * @var array
	 */
	public static $actions = array(
		'oversight' => array(
			'permissions' => 'aft-oversighter',
			'sentiment' => 'negative'
		),
		'unoversight' => array(
			'permissions' => 'aft-oversighter',
			'sentiment' => 'positive'
		),
		'decline' => array(
			'permissions' => 'aft-oversighter',
			'sentiment' => 'positive'
		),
		'request' => array(
			'permissions' => 'aft-monitor',
			'sentiment' => 'negative'
		),
		'unrequest' => array(
			'permissions' => 'aft-monitor',
			'sentiment' => 'positive'
		),
		'feature' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'positive'
		),
		'unfeature' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'negative'
		),
		'resolve' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'positive'
		),
		'unresolve' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'negative'
		),
		'noaction' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'neutral'
		),
		'unnoaction' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'neutral'
		),
		'hide' => array(
			'permissions' => 'aft-editor2',
			'sentiment' => 'negative'
		),
		'unhide' => array(
			'permissions' => 'aft-editor2',
			'sentiment' => 'positive'
		),
		'autohide' => array(
			'permissions' => 'aft-editor2',
			'sentiment' => 'negative'
		),
		'archive' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'negative'
		),
		'unarchive' => array(
			'permissions' => 'aft-editor',
			'sentiment' => 'positive'
		),
		'flag' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'negative'
		),
		'unflag' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'positive'
		),
		'autoflag' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'negative'
		),
		'clear-flags' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'positive'
		),
		'helpful' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'positive'
		),
		'undo-helpful' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'neutral'
		),
		'unhelpful' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'negative'
		),
		'undo-unhelpful' => array(
			'permissions' => 'aft-reader',
			'sentiment' => 'neutral'
		)
	);

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
	public static function getList( ArticleFeedbackv5Model $feedback, $user = null, $limit = 25, $continue = null ) {
		if ( !$user ) {
			global $wgUser;
			$user = $wgUser;
		}

		// build where-clause for actions and feedback
		$actions = self::buildWhereActions( $user->getRights() );
		$title = self::buildWhereFeedback( $feedback );

		// nothing to get? return empty resultset
		if ( !$actions || !$title ) {
			return new FakeResultWrapper( array() );
		}

		$where[] = $actions;
		$where['log_title'] = $title;
		$where['log_namespace'] = NS_SPECIAL;
		$where = self::applyContinue( $continue, $where );

		$activity = wfGetDB( DB_SLAVE )->select(
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
	 * Returns a timestamp/id tuple for subsequent request continuing from this record
	 *
	 * @param ResultWrapper $row
	 * @return string
	 */
	public static function getContinue( $row ) {
		$ts = wfTimestamp( TS_MW, $row->log_timestamp );
		return "$ts|{$row->log_id}";
	}

	/**
	 * Get (and cache) the counts of activity (that are within the user's permissions)
	 * that has been posted already
	 *
	 * @param ArticleFeedbackv5Model $feedback
	 * @param User[optional] $user
	 * @return int
	 */
	public static function getActivityCount( ArticleFeedbackv5Model $feedback, User $user = null ) {
		global $wgArticleFeedbackv5Permissions, $wgMemc;
		$total = 0;

		if ( !$user ) {
			global $wgUser;
			$user = $wgUser;
		}

		if ( !$wgUser->isBlocked() ) {
			foreach( $wgArticleFeedbackv5Permissions as $permission ) {
				if ( $user->isAllowed( $permission ) ) {
					// get count for this specific permission level from cache
					$key = wfMemcKey( 'articlefeedbackv5', 'getActivityCount', $permission, $feedback->aft_id );
					$count = $wgMemc->get( $key );

					if ( $count === false ) {
						$count = self::getActivityCountFromDB( $feedback, $permission );
					}

					/*
					 * Save to or extend caching. Set a long expiration (because the
					 * query-alternative is quite expensive) but not forever (once
					 * feedback is dealt with, it won't be accessed again so there's
					 * no point in cluttering memory)
					 */
					$wgMemc->set( $key, $count, 60 * 60 * 24 * 7 );

					$total += $count;
				}
			}
		}

		return $total;
	}

	/**
	 * Because fetching the amount of activity from db is quite expensive, this
	 * method will just increment the data that is in cache already (instead of
	 * purging the cache data to have it re-read from DB, which should be last-resort)
	 *
	 * @param int $feedbackId
	 * @param string $action
	 */
	public static function incrementActivityCount( $feedbackId, $action ) {
		global $wgMemc;

		// get permission level that should be updated
		$permission = self::$actions[$action]['permissions'];

		$key = wfMemcKey( 'articlefeedbackv5', 'getActivityCount', $permission, $feedbackId );
		$count = $wgMemc->get( $key );

		/*
		 * if the data is not (yet) in cache, don't bother fetching it from db yet,
		 * that'll happen in due time, when it's actually requested
		 */
		if ( $count !== false ) {
			$wgMemc->set( $key, $count + 1, 60 * 60 * 24 * 7 );
		}

		// while we're at it, since activity has occured, the editor activity
		// data in cache may be out of date
		$key = wfMemcKey( get_called_class(), 'getLastEditorActivity', $feedbackId );
		$wgMemc->delete( $key );
	}

	/**
	 * Get amount of activity for a certain feedback post for a certain permission level.
	 * This should not be called directly, as it's a relatively expensive call; the result
	 * should be cached (@see self::getActivityCount)
	 *
	 * @param ArticleFeedbackv5Model $feedback
	 * @param string $permission
	 * @return int
	 * @throws MWException
	 */
	private static function getActivityCountFromDB( ArticleFeedbackv5Model $feedback, $permission ) {
		// build where-clause for actions and feedback
		$actions = self::buildWhereActions( array( $permission ) );
		$title = self::buildWhereFeedback( $feedback );

		// nothing to get? return empty resultset
		if ( !$actions || !$title ) {
			return 0;
		}

		$where[] = $actions;
		$where['log_title'] = $title;
		$where['log_namespace'] = NS_SPECIAL;

		return (int) wfGetDB( DB_SLAVE )->selectField(
			'logging',
			'COUNT(log_id)',
			$where,
			__METHOD__
		);
	}

	/**
	 * Gets the last editor activity per feedback entry.
	 *
	 * @param array $entries array of feedback to fetch last log entries for; will be
	 *                       in the form of array( array( 'id' => [id], 'shard' => [shard] ), ... )
	 * @return ResultWrapper db record rows
	 */
	public static function getLastEditorActivity( array $entries ) {
		// build where-clause for all feedback entries
		$titles = array();
		foreach ( $entries as $entry ) {
			$feedback = ArticleFeedbackv5Model::get( $entry['id'], $entry['shard'] );
			if ( !$feedback ) {
				continue;
			}

			$titles[] = self::buildWhereFeedback( $feedback );
		}

		/*
		 * Build where-clause for actions.
		 * We want all "normal" editor tools, not oversight-related tools.
		 */
		$actions = self::buildWhereActions( array( 'aft-editor', 'aft-monitor' ), array( 'request', 'unrequest' ) );

		// nothing to get? return empty resultset
		if ( !$actions || !$titles ) {
			return new FakeResultWrapper( array() );
		}

		$where[] = $actions;
		$where['log_title'] = $titles;
		$where['log_namespace'] = NS_SPECIAL;

		$activity = wfGetDB( DB_SLAVE )->select(
			array( 'logging' ),
			array(
				'log_id' => 'MAX(log_id)',
				/*
				 * The SUBSTRING_INDEX(GROUP_CONCAT()) stuff is a rather ugly hack to not have to
				 * perform a subquery (I could get the MAX(id) in a subquery and then get all the
				 * columns' values of the rows matching the MAX(id)'s)
				 * This solution will return incorrect results in the event that any column's first
				 * value includes a comma, but that should only occur for log_comment, whose value
				 * should not be displayed (it will only be used to check if there is a comment,
				 * so functionality will only break - in a minor way - when the comma is the first
				 * character).
				 */
				'log_action' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_action ORDER BY log_id DESC), ",", 1)',
				'log_timestamp' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_timestamp ORDER BY log_id DESC), ",", 1)',
				'log_user' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_user ORDER BY log_id DESC), ",", 1)',
				'log_user_text' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_user_text ORDER BY log_id DESC), ",", 1)',
				'log_title' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_title ORDER BY log_id DESC), ",", 1)',
				'log_comment' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_comment ORDER BY log_id DESC), ",", 1)',
				'log_params' => 'SUBSTRING_INDEX(GROUP_CONCAT(log_params ORDER BY log_id DESC), ",", 1)',
			),
			$where,
			__METHOD__,
			array(
				'GROUP BY' => 'log_title',
				// Force the page_time index (on _namespace, _title, _timestamp)
				// We don't expect many if any rows for Special:ArticleFeedbackv5/foo that
				// don't match log_type='articlefeedbackv5' , so we can afford to have that
				// clause be unindexed. The alternative is to have the log_type clause be indexed
				// and the namespace/title clauses unindexed, that would be bad.
				'USE INDEX' => 'page_time'
			)
		);

		global $wgMemc;
		foreach ( $activity as $action ) {
			// get feedback id from params
			$params = @unserialize( $action->log_params );
			if ( !isset( $params['feedbackId'] ) ) {
				continue;
			}

			// cache, per feedback entry
			$key = wfMemcKey( get_called_class(), 'getLastEditorActivity', $params['feedbackId'] );
			$wgMemc->set( $key, $action, 60 * 60 );
		}

		return $activity;
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

	/**
	 * Check if a user has sufficient permissions to perform an action
	 *
	 * @param string $action
	 * @param User[optional] $user
	 * @return bool
	 * @throws MWException
	 */
	public static function canPerformAction( $action, User $user = null ) {
		if ( !$user ) {
			global $wgUser;
			$user = $wgUser;
		}

		if ( !isset( self::$actions[$action] ) ) {
			throw new MWException( "Action '$action' does not exist." );
		}

		return $user->isAllowed( self::$actions[$action]['permissions'] ) && !$user->isBlocked();
	}

	/**
	 * Build WHERE conditions for permission-based log actions.
	 *
	 * @param array $permissions The available permissions
	 * @param array[optional] $exclude Actions to exclude
	 * @return string|bool false will be returned in the event no valid WHERE-clause
	 *                     can be built because of actions are permitted
	 */
	protected static function buildWhereActions( $permissions, $exclude = array() ) {
		global $wgLogActionsHandlers;

		$dbr = wfGetDB( DB_SLAVE );

		$actions = array();
		foreach ( self::$actions as $action => $options ) {
			if ( !in_array( $options['permissions'], $permissions) || in_array( $action, $exclude ) ) {
				continue;
			}

			if ( isset( $wgLogActionsHandlers["suppress/$action"] ) ) {
				$type = 'suppress';
			} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$action"] ) ) {
				$type = 'articlefeedbackv5';
			} else {
				continue;
			}
			$actions[] = 'log_type = '.$dbr->addQuotes( $type ).' AND log_action = '.$dbr->addQuotes( $action );
		}

		// if no valid actions were found, return
		if ( !$actions ) {
			return false;
		}

		return '('.implode( ') OR (', $actions ).')';
	}

	/**
	 * Build WHERE conditions for (a) specific feedback entr(y|ies)' log entries.
	 *
	 * @param array ArticleFeedbackv5Model $feedback The feedback to fetch log entries for
	 * @return string|bool false will be returned in the event no valid WHERE-clause
	 *                     can be built because no feedback is found
	 */
	protected static function buildWhereFeedback( ArticleFeedbackv5Model $feedback ) {
		// build title(s) to fetch log entries for
		$feedbackId = $feedback->aft_id;
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			return false;
		}
		$title = $page->getDBKey();

		return "ArticleFeedbackv5/$title/$feedbackId";
	}
}
