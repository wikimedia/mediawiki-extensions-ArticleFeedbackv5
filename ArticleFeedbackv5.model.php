<?php
/**
 * This class represents a feedback entry, which is backed by
 * a sharded database setup and heavy cache usage.
 *
 * @package    ArticleFeedback
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
class ArticleFeedbackv5Model extends DataModel {
	/**
	 * These are the exact columns a feedback entry consists of in the DB
	 *
	 * @var int|string
	 */
	public
		// regular AFT data
		$id,
		$page,
		$page_revision,
		$user,
		$user_text,
		$user_token,
		$form,
		$cta,
		$link,
		$rating,
		$comment,
		$timestamp,

		// denormalized totals of which real records are in logging table
		$oversight = 0,
		$decline = 0,
		$request = 0,
		$hide = 0,
		$autohide = 0,
		$flag = 0,
		$autoflag = 0,
		$feature = 0,
		$resolve = 0,
		$helpful = 0,
		$unhelpful = 0,

		$relevance_score = 0;

	/**
	 * Database table to hold the data
	 *
	 * @var string
	 */
	protected static $table = 'aft_feedback';

	/**
	 * Name of column to act as unique id
	 *
	 * @var string
	 */
	protected static $idColumn = 'id';

	/**
	 * Name of column to shard data over
	 *
	 * @var string
	 */
	protected static $shardColumn = 'page';

	/**
	 * Pagination limit: how many entries should be fetched at once for lists
	 *
	 * @var int
	 */
	const LIST_LIMIT = 50;

	/**
	 * All lists the data can be displayed as
	 *
	 * Key is the filter name, the value is an array of
	 * * the conditions an "entry" must abide to to qualify for this list
	 * * the column to sort on
	 *
	 * @var array
	 */
	public static $lists = array(
		// no-one should see this list, we'll use it to keep count of all articles ;)
		'*' => array(
			'permissions' => 'noone',
			'conditions' => array(),
			'sort' => array()
		),

		// reader lists
		'visible-relevant' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""' ), // @todo: also needs: '$this->relevance_score > $wgArticleFeedbackv5Cutoff'
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'visible-featured' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->feature > 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'visible-helpful' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->helpful > $this->unhelpful' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'visible' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),

		// editor lists
		'visible-unhelpful' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->helpful < $this->unhelpful' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'visible-abusive' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->flag > 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'visible-resolved' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->resolve > 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'visible-unresolved' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->resolve <= 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),

		// monitor lists
		'notdeleted-hidden' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '$this->oversight <= 0', '$this->comment != ""', '$this->hide > 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'notdeleted-declined' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '$this->oversight <= 0', '$this->comment != ""', '$this->decline > 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'notdeleted' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '$this->oversight <= 0', '$this->comment != ""' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),

		// oversighter lists
		'notdeleted-requested' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '$this->oversight <= 0', '$this->comment != ""', '$this->request > 0' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'all-oversighted' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '$this->oversight > 0', '$this->comment != ""' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		),
		'all' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '$this->comment != ""' ),
			'sort' => array( 'relevance' => '$this->relevance_score', 'age' => '$this->timestamp', 'helpful' => '$this->helpful - $this->unhelpful' )
		)
	);

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
	 * Validate the entry's data
	 *
	 * @return DataModel
	 */
	public function validate() {
		global $wgArticleFeedbackv5MaxCommentLength;

		// @todo: validate the object's properties, throw exceptions for whatever is wrong

		if ( $wgArticleFeedbackv5MaxCommentLength > 0
			&& strlen( $this->comment ) > $wgArticleFeedbackv5MaxCommentLength ) {
			throw new MWException( "Comment length exceeds the maximum of '$wgArticleFeedbackv5MaxCommentLength'." );
		}

		return parent::validate();
	}

	/**
	 * Fetch a list of entries
	 *
	 * @param string $name The list name (see static::$lists)
	 * @param mixed[optional] $shard Get only data for a certain shard value
	 * @param int[optional] $offset The offset to start from (a multiple of static::LIST_LIMIT)
	 * @param string[optional] $sort Sort to apply to list
	 * @param string[optional] $order Sort the list ASC or DESC
	 * @return array
	 */
	public static function getList( $name, $shard = null, $offset = 0, $sort = 'relevance', $order = 'ASC' ) {
		global $wgUser;

		if ( isset( self::$lists[$name] ) && !$wgUser->isAllowed( self::$lists[$name]['permissions'] ) ) {
			throw new MWException( "List '$name' is not allowed for this user" );
		}

		return parent::getList( $name, $shard, $offset, $sort, $order );
	}

	/**
	 * Insert entry into the DB (& cache)
	 *
	 * @return DataModel
	 */
	public function insert() {
		// if no creation timestamp is entered yet, fill it out
		if ( $this->timestamp === null ) {
			$storeGroup = RDBStoreGroup::singleton();
			$store = $storeGroup->getForTable( self::getTable() );
			$partition = $store->getPartition( self::getTable(), self::getShardColumn(), $this->{self::getShardColumn()} );

			$this->timestamp = $partition->getMasterDB()->timestamp( wfTimestampNow() );
		}

		$this->relevance_score = $this->getRelevanceScore();
		$this->updateCountFound();

		return parent::insert();
	}

	/**
	 * Update entry in the DB (& cache)
	 *
	 * @return DataModel
	 */
	public function update() {
		$this->relevance_score = $this->getRelevanceScore();
		$this->updateCountFound();

		return parent::update();
	}

	/**
	 * Purge relevant Squid cache when updating data
	 *
	 * @return DataModel
	 */
	public function purgeSquidCache() {
		global $wgArticleFeedbackv5SMaxage;

		// @todo: this api has been removed; see what else there is left & implement that, or remove this method
/*
		$uri = wfAppendQuery(
			wfScript( 'api' ),
			array(
				'action'       => 'query',
				'format'       => 'json',
				'list'         => 'articlefeedbackv5-view-ratings',
				'afpageid'     => $this->page,
				'maxage'       => 0,
				'smaxage'      => $wgArticleFeedbackv5SMaxage
			)
		);
		$squidUpdate = new SquidUpdate( array( $uri ) );
		$squidUpdate->doUpdate();
*/
		return $this;
	}

	/**
	 * Calculate the relevance score based on the actions performed
	 *
	 * @return int
	 */
	public function getRelevanceScore() {
		global $wgArticleFeedbackv5RelevanceScoring;

		$total = 0;

		foreach ( $wgArticleFeedbackv5RelevanceScoring as $action => $score ) {
			if ( isset( $this->$action ) ) {
				$total += $score * $this->$action;
			}
		}

		return $total;
	}

	/**
	 * @return string
	 */
	public function getExperiment() {
		return $this->form . $this->link;
	}

	/**
	 * Get user object for this entry
	 *
	 * @return User
	 */
	public function getUser() {
		if ( $this->user ) {
			return User::newFromId( $this->user );
		}

		return User::newFromName( $this->user_text );
	}

	/**
	 * ACTIONS-RELATED
	 */

	/**
	 * @return bool
	 */
	public function isHidden() {
		return (bool) $this->hide;
	}

	/**
	 * @return bool
	 */
	public function isOversighted() {
		return (bool) $this->oversight;
	}

	/**
	 * @return bool
	 */
	public function isFeatured() {
		return (bool) $this->feature;
	}

	/**
	 * @return bool
	 */
	public function isResolved() {
		return (bool) $this->resolve;
	}

	/**
	 * @return bool
	 */
	public function isDeclined() {
		return (bool) $this->decline;
	}

	/**
	 * @return bool
	 */
	public function isRequested() {
		return $this->request && !$this->isDeclined();
	}

	/**
	 * @return bool
	 */
	public function isFlagged() {
		return $this->flag + $this->autoflag > 0;
	}

	/**
	 * Update the amount of people who marked "yes" to the question if they
	 * found what the were looking for
	 */
	public function updateCountFound() {
		$oldRating = 0;
		$old = static::get( $this->{static::getIdColumn()}, $this->{static::getShardColumn()} );
		if ( $old ) {
			$oldRating = $old->rating;
		}
		$difference = (int) $this->rating - (int) $oldRating;

		global $wgMemc;
		$class = get_called_class();
		foreach ( array( $this->{self::getShardColumn()}, null ) as $shard ) {
			/**
			 * Callback method, updating the cached counts
			 *
			 * @param BagOStuff $cache
			 * @param string $key
			 * @param int $existingValue
			 * @use mixed $shard The shard value
			 * @use int $difference The difference to apply to current count
			 * @use string $class The called class
			 * @return int
			 */
			$callback = function( BagOStuff $cache, $key, $existingValue ) use ( $shard, $difference, $class ) {
				// if cache is stale, get it from DB
				if ( $existingValue === false ) {
					return $class::getCountFound( $shard );
				}

				// if count is in cache already, update it right away, avoiding any more DB reads
				return $existingValue + $difference;
			};

			$key = wfMemcKey( get_called_class(), 'getCountFound', $shard );
			$wgMemc->merge( $key, $callback );
		}
	}

	/**
	/**
	 * Get the amount of people who marked "yes" to the question if they
	 * found what the were looking for
	 *
	 * @param int[optional] The page id
	 * @return int
	 */
	public static function getCountFound( $pageId = null ) {
		global $wgMemc;

		$key = wfMemcKey( get_called_class(), 'getCountFound', $pageId );
		$found = $wgMemc->get( $key );

		if ( $found === false ) {
			$found = self::getCountFoundFromDB( $pageId );
			$wgMemc->set( $key, $found );
		}

		return $found;
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
	protected static function getCountFoundFromDB( $pageId = null ) {
		$storeGroup = RDBStoreGroup::singleton();
		$store = $storeGroup->getForTable( static::getTable() );

		// build where condition
		$where = array();
		$where['rating'] = 1;
		if ( $pageId !== null) {
			$where[static::getShardColumn()] = $pageId;
			$partitions = array( $store->getPartition( static::getTable(), static::getShardColumn(), $pageId ) );
		} else {
			$partitions = $store->getAllPartitions( static::getTable(), static::getShardColumn() );
		}

		$count = 0;
		foreach ( $partitions as $partition ) {
			$count += (int) $partition->selectField(
				DB_SLAVE,
				array( 'COUNT('.static::getIdColumn().')' ),
				$where,
				__METHOD__,
				array()
			);
		}

		return $count;
	}
}
