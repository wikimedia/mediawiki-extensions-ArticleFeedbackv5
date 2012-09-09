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
			'permissions' => 'aft_monitor',
			'sentiment' => 'positive'
		),
		'hide' => array(
			'permissions' => 'aft-monitor',
			'sentiment' => 'negative'
		),
		'unhide' => array(
			'permissions' => 'aft-monitor',
			'sentiment' => 'positive'
		),
		'autohide' => array(
			'permissions' => 'aft-monitor',
			'sentiment' => 'negative'
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
	 * @param ArticleFeedbackv5Model $feedback Model of the feedback item whose activity we're fetching
	 * @param User[optional] $user User object who we're fetching activity for (to check permissions)
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
		$permissions = $user->getRights();
		foreach ( self::$actions as $action => $options ) {
			if ( in_array( $options['permissions'], $permissions ) ) {
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
			throw new MWException( 'Invalid continue param. You should pass the value returned by the previous query', 'badcontinue' );
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
		$permission = ArticleFeedbackv5Activity::$actions[$action]['permissions'];

		$key = wfMemcKey( 'articlefeedbackv5', 'getActivityCount', $permission, $feedbackId );
		$count = $wgMemc->get( $key );

		/*
		 * if the data is not (yet) in cache, don't bother fetching it from db yet,
		 * that'll happen in due time, when it's actually requested
		 */
		if ( $count !== false ) {
			$wgMemc->set( $key, $count + 1, 60 * 60 * 24 * 7 );
		}
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
		global $wgLogActionsHandlers;
		$dbr = wfGetDB( DB_SLAVE );

		$feedbackId = $feedback->aft_id;
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			throw new MWException( 'Page for feedback does not exist', 'invalidfeedbackid' );
		}
		$title = $page->getDBKey();

		// get action-specific where-clause for requested permission level
		$actions = array();
		foreach( self::$actions as $action => $options ) {
			if ( $options['permissions'] == $permission ) {
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

		if ( !$actions ) {
			return 0;
		}

		$where[] = '('.implode( ') OR (', $actions ).')';
		$where['log_namespace'] = NS_SPECIAL;
		$where['log_title'] = "ArticleFeedbackv5/$title/$feedbackId";

		return (int) $dbr->selectField(
			'logging',
			'COUNT(log_id)',
			$where,
			__METHOD__
		);
	}
}
