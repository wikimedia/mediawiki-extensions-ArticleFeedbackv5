<?php
/**
 * This class represents a feedback entry, which can be backed by
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
			'conditions' => array( '$this->hide <= 0', '$this->comment != ""', '$this->relevance_score > -5' ), // -5 here is $wgArticleFeedbackv5Cutoff
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
	 * Get the backend object that'll store the data for real.
	 *
	 * @return DataModelBackend
	 */
	public static function getBackend() {
		if ( self::$backend === null ) {
			global $wgArticleFeedbackv5BackendClass;
			self::$backend = new $wgArticleFeedbackv5BackendClass( static::getTable(), static::getIdColumn(), static::getShardColumn() );
		}

		return self::$backend;
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
			$found = static::getBackend()->getCountFound( $pageId );
			$wgMemc->set( $key, $found );
		}

		return $found;
	}

	/**
	 * Get watchlist list, based on user ID rather than page id ($shard)
	 *
	 * @param string $name The list name (see static::$lists)
	 * @param User[optional] $user The user who'se watchlisted feedback to fetch
	 * @param int[optional] $offset The offset to start from (a multiple of static::LIST_LIMIT)
	 * @param string[optional] $sort Sort to apply to list
	 * @param string[optional] $order Sort the list ASC or DESC
	 * @return array
	 */
	public static function getWatchlistList( $name, User $user = null, $offset = 0, $sort = 'relevance', $order = 'ASC' ) {
		if ( !$user ) {
			global $wgUser;
			$user = $wgUser;
		}

		/*
		 * Get array of page ids
		 *
		 * Note: even though the watchlist stuff is not up to par with other lists
		 * (because we can't properly shard and cache it), all code should still scale
		 * though.
		 * This line however does not scale: in theory, it's possible that the amount
		 * of article data grows too large to fit the machine's memory. This will most
		 * likely never happen though - and we just have to wish it won't, since there
		 * is not other way: both the feedback entries and the lists are sharded, so we
		 * can't perform a joined query ;)
		 */
		$articles = wfGetDB( DB_SLAVE )->select(
			array( 'watchlist', 'page' ),
			array( 'page_id' ),
			array( 'wl_user' => $user->getId() ),
			__METHOD__,
			array(),
			array(
				'page' => array(
					'INNER JOIN',
					array(
						'page_namespace = wl_namespace',
						'page_title = wl_title'
					)
				)
			)
		);

		$shards = array();
		foreach( $articles as $article ) {
			$shards[] = $article->page_id;
		}

		return static::getList( $name, $shards, $offset, $sort, $order );
	}

	/**
	 * This is a workaround for watchlist-stuff. People's watchlist can change
	 * all the time and it makes not sense to cache the results for a watchlist.
	 * Instead of attempting to fetch feedback on someone's watchlisted pages
	 * ($shard will be an array of page id's in this case) from cache, get it
	 * straight from the DB - we've got an index on this table, and all filter
	 * results are on 1 partition, so this is quite an inexpensive query.
	 * The more expensive part is that the feedback itself for these different
	 * pages might/will be sharded over multiple partitions. Given that a watchlist
	 * is different per user, and it may change any given time, there is no way
	 * we can distribute the feedback over partitions in a way that is optimized
	 * for watchlists. Let's hope that most of that data will be in cache, or
	 * it might take a bit longer to present the result, because we'll be
	 * querying multiple partitions.
	 *
	 * @param string $name The list name (see static::$lists)
	 * @param array $shard Get only data for certain shard values
	 * @param int[optional] $offset The offset to start from (a multiple of static::LIST_LIMIT)
	 * @param string[optional] $sort Sort to apply to list
	 * @param string[optional] $order Sort the list ASC or DESC
	 * @return array
	 */
	protected static function getListArray( $name, array $shard, $offset = 0, $sort = 'relevance', $order = 'ASC' ) {
		global $wgMemc;

		// fetch data from db
		$rows = static::getBackend()->getList( $name, $shard, $offset, static::LIST_LIMIT, $sort, $order );

		// build [id] => [shard] array for requested part of list
		$list = array();
		if ( $rows ) {
			foreach ( $rows as $row ) {
				$list[$row->id] = $row->shard;
			}
		}

		/*
		 * $list now contains an array of [id] => [shard] entries
		 * we'll now want to fetch the actual feedback data for these entries
		 * some entries might already be cached, don't bother fetching those from db
		 */
		$missing = array();
		foreach ( $list as $id => $shard ) {
			$keyGet = wfMemcKey( get_called_class(), 'get', $id, $shard );
			if ( $wgMemc->get( $keyGet ) === false ) {
				/*
				 * while not encouraged, it is possible that a list should contain cross-shard
				 * data (e.g. simply a list of all entries) - separate all entries by shard
				 */
				$missing[$id] = $shard;
			}
		}

		/*
		 * Fetch the missing entries from the database all at once and
		 * cache them right away
		 */
		$rows = static::getBackend()->get( array_keys( $missing ), array_values( $missing ) );

		// we don't really care for the returned row but just want them cached
		foreach ( $rows as $row ) {
			$entry = static::loadFromRow( $row );
			$entry->cache();
		}

		foreach ( $list as $id => $shard ) {
			$list[$id] = static::get( $id, $shard );
		}

		return $list;
	}

	/**
	 * Validate the entry's data
	 *
	 * @return DataModel
	 */
	public function validate() {
		global $wgArticleFeedbackv5MaxCommentLength;

		$page = Title::newFromID( $this->page );
		if ( $page === null ) {
			throw new MWException( "Invalid page id '$this->page'." );
		}

		$revision = Revision::newFromId( $this->page_revision );
		if ( $revision === null ) {
			throw new MWException( "Invalid revision id '$this->page_revision'." );
		}

		if ( $this->user != 0 && $this->getUser() === false ) {
			throw new MWException( "Invalid user id '$this->user' or name '$this->user_text'." );
		}

		global $wgArticleFeedbackv5DisplayBuckets;
		if ( !in_array( $this->form, array_keys( $wgArticleFeedbackv5DisplayBuckets['buckets'] ) ) ) {
			throw new MWException( "Invalid form id '$this->form'." );
		}

		global $wgArticleFeedbackv5CTABuckets;
		if ( !in_array( $this->cta, array_keys( $wgArticleFeedbackv5CTABuckets['buckets'] ) ) ) {
			throw new MWException( "Invalid cta id '$this->cta'." );
		}

		global $wgArticleFeedbackv5LinkBuckets;
		if ( !in_array( $this->link, array_keys( $wgArticleFeedbackv5LinkBuckets['buckets'] ) ) ) {
			throw new MWException( "Invalid form id '$this->link'." );
		}

		if ( !in_array( $this->rating, array( 0, 1 ) ) ) {
			throw new MWException( "Invalid rating '$this->rating'." );
		}

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

		// watchlist workaround
		if ( is_array( $shard ) ) {
			return static::getListArray( $name, $shard, $offset, $sort, $order );
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
			$this->timestamp = wfTimestampNow();
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
}
