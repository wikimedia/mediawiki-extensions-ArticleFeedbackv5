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
		$aft_id,
		$aft_page,
		$aft_page_revision,
		$aft_user,
		$aft_user_text,
		$aft_user_token,
		$aft_claimed_user,
		$aft_form,
		$aft_cta,
		$aft_link,
		$aft_rating,
		$aft_comment,
		$aft_timestamp,

		// will hold the date after which an entry may be archived
		$aft_archive_date,

		// will hold info if discussion about the feedback has been started on user or article talk page
		$aft_discuss,

		// denormalized status indicators for actions of which real records are in logging table
		$aft_oversight = 0,
		$aft_decline = 0,
		$aft_request = 0,
		$aft_hide = 0,
		$aft_autohide = 0,
		$aft_flag = 0,
		$aft_autoflag = 0,
		$aft_feature = 0,
		$aft_resolve = 0,
		$aft_noaction = 0,
		$aft_inappropriate = 0,
		$aft_archive = 0,
		$aft_helpful = 0,
		$aft_unhelpful = 0,

		// even more denormalized stuff, allowing easy DB-indexing sort columns
		$aft_has_comment,
		$aft_net_helpful = 0,
		$aft_relevance_score = 0;

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
	protected static $idColumn = 'aft_id';

	/**
	 * Name of column to shard data over
	 *
	 * @var string
	 */
	protected static $shardColumn = 'aft_page';

	/**
	 * Pagination limit: how many entries should be fetched at once for lists
	 *
	 * @var int
	 */
	const LIST_LIMIT = 50;

	/**
	 * All lists the data can be displayed as
	 *
	 * @var array
	 */
	public static $lists = array(
		// no-one should see this list, we'll use it to keep count of all articles ;)
		'*' => array(
			'permissions' => 'aft-noone',
			'conditions' => array(),
		),

		// reader lists
		'featured' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_resolve = 0', 'aft_noaction = 0', 'aft_inappropriate = 0', 'aft_net_helpful > 0 OR aft_feature = 1' ),
		),
		'unreviewed' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_feature = 0', 'aft_resolve = 0', 'aft_noaction = 0', 'aft_inappropriate = 0' ),
		),

		// editor lists
		'helpful' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_net_helpful > 0' ),
		),
		'unhelpful' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_net_helpful < 0' ),
		),
		'flagged' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_flag > 0' ),
		),
		'useful' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_feature = 1' ),
		),
		'resolved' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_resolve = 1' ),
		),
		'noaction' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_noaction = 1' ),
		),
		'inappropriate' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 0', 'aft_hide = 0', 'aft_inappropriate = 1' ),
		),
		'archived' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_archive = 1' ),
		),
		'allcomment' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_hide = 0' ),
		),

		// monitor lists
		'hidden' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_hide = 1' ),
		),
		'requested' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_request = 1' ),
		),
		'declined' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 0', 'aft_request = 1', 'aft_decline = 1' ),
		),

		// oversighter lists
		'oversighted' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( 'aft_has_comment = 1', 'aft_oversight = 1' ),
		),
		'all' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array(),
		)
	);

	/**
	 * Available sorts to order the data
	 *
	 * Key is the sort name, the value is the condition for the ORDER BY clause.
	 *
	 * When creating indexes on the database, create a compound index for each of
	 * the sort-columns, along with the id column.
	 *
	 * @var array
	 */
	public static $sorts = array(
		'relevance' => 'aft_relevance_score',
		'age' => 'aft_timestamp',
		'helpful' => 'aft_net_helpful'
	);

	/**
	 * Get the backend object that'll store the data for real.
	 *
	 * @return DataModelBackend
	 */
	public static function getBackend() {
		if ( self::$backend === null ) {
			global $wgArticleFeedbackv5BackendClass;
			self::$backend = new $wgArticleFeedbackv5BackendClass( get_called_class(), static::getTable(), static::getIdColumn(), static::getShardColumn() );
		}

		return self::$backend;
	}

	/**
	 * Update the amount of people who marked "yes" to the question if they
	 * found what the were looking for
	 */
	public function updateCountFound() {
		$oldRating = 0;
		if ( $this->{static::getIdColumn()} ) {
			$old = static::get( $this->{static::getIdColumn()}, $this->{static::getShardColumn()} );
			if ( $old ) {
				$oldRating = $old->aft_rating;
			}
		}
		$difference = (int) $this->aft_rating - (int) $oldRating;

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
				// if nothing is cached, leave be; cache will rebuild when it's requested
				if ( $existingValue === false ) {
					return false;
				}

				// if count is in cache already, update it right away, avoiding any more DB reads
				return $existingValue + $difference;
			};

			$key = wfMemcKey( get_called_class(), 'getCountFound', $shard );
			static::getCache()->merge( $key, $callback );
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
		$key = wfMemcKey( get_called_class(), 'getCountFound', $pageId );
		$found = static::getCache()->get( $key );

		if ( $found === false ) {
			$found = static::getBackend()->getCountFound( $pageId );
			static::getCache()->set( $key, $found );
		}

		return $found;
	}

	/**
	 * Get a user's AFT-contributions to add to the My Contributions special page
	 *
	 * @param ContribsPager $pager object hooked into
	 * @param string $offset index offset, inclusive
	 * @param int $limit exact query limit
	 * @param bool $descending query direction, false for ascending, true for descending
	 * @param array $userIds array of user_ids whose data is to be selected
	 * @return ResultWrapper
	 */
	public static function getContributionsData( $pager, $offset, $limit, $descending, $userIds = array() ) {
		return static::getBackend()->getContributionsData( $pager, $offset, $limit, $descending, $userIds );
	}

	/**
	 * Get watchlist list, based on user ID rather than page id ($shard)
	 *
	 * @param string $name The list name (see static::$lists)
	 * @param User[optional] $user The user who'se watchlisted feedback to fetch
	 * @param int[optional] $offset The offset to start from
	 * @param string[optional] $sort Sort to apply to list
	 * @param string[optional] $order Sort the list ASC or DESC
	 * @return DataModelList
	 */
	public static function getWatchlistList( $name, User $user = null, $offset = null, $sort = 'relevance', $order = 'ASC' ) {
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
	 * @param int[optional] $offset The offset to start from
	 * @param string[optional] $sort Sort to apply to list
	 * @param string[optional] $order Sort the list ASC or DESC
	 * @return DataModelList
	 */
	protected static function getListArray( $name, array $shard, $offset = null, $sort = 'relevance', $order = 'ASC' ) {
		// fetch data from db
		$rows = static::getBackend()->getList( $name, $shard, $offset, static::LIST_LIMIT, $sort, $order );

		$entries = array();
		foreach ( $rows as $row ) {
			// pre-cache entries
			$entry = static::loadFromRow( $row );
			$entry->cache();

			// build list of id's
			$entries[] = array(
				'id' => $entry->{static::getIdColumn()},
				'shard' => $entry->{static::getShardColumn()},
				'offset' => ( isset( $row->offset_value ) ? $row->offset_value . '|' : '' ) . $entry->{static::getIdColumn()}
			);
		}

		$list = new DataModelList( $entries, get_called_class() );

		return $list;
	}

	/**
	 * Validate the entry's data
	 *
	 * @return DataModel
	 */
	public function validate() {
		// when running unittests, ignore this
		if ( defined( 'MW_PHPUNIT_TEST' ) && MW_PHPUNIT_TEST ) {
			return $this;
		}

		global $wgArticleFeedbackv5MaxCommentLength;

		if ( !$this->getArticle() ) {
			throw new MWException( "Invalid page id '$this->aft_page'." );
		}

		if ( !$this->getRevision() ) {
			throw new MWException( "Invalid revision id '$this->aft_page_revision'." );
		}

		if ( $this->aft_user != 0 && !$this->getUser() ) {
			throw new MWException( "Invalid user id '$this->aft_user' or name '$this->aft_user_text'." );
		}

		global $wgArticleFeedbackv5DisplayBuckets;
		if ( !in_array( $this->aft_form, array_keys( $wgArticleFeedbackv5DisplayBuckets['buckets'] ) ) ) {
			throw new MWException( "Invalid form id '$this->aft_form'." );
		}

		global $wgArticleFeedbackv5CTABuckets;
		if ( !in_array( $this->aft_cta, array_keys( $wgArticleFeedbackv5CTABuckets['buckets'] ) ) ) {
			throw new MWException( "Invalid cta id '$this->aft_cta'." );
		}

		global $wgArticleFeedbackv5LinkBuckets;
		if ( !in_array( $this->aft_link, array_keys( $wgArticleFeedbackv5LinkBuckets['buckets'] ) ) ) {
			throw new MWException( "Invalid link id '$this->aft_link'." );
		}

		if ( !in_array( $this->aft_rating, array( 0, 1 ) ) ) {
			throw new MWException( "Invalid rating '$this->aft_rating'." );
		}

		if ( $wgArticleFeedbackv5MaxCommentLength > 0
			&& strlen( $this->aft_comment ) > $wgArticleFeedbackv5MaxCommentLength ) {
			throw new MWException( "Comment length exceeds the maximum of '$wgArticleFeedbackv5MaxCommentLength'." );
		}

		if ( $this->aft_discuss && !in_array( $this->aft_discuss, array( 'talk', 'user' ) ) ) {
			throw new MWException( "Invalid discuss type '$this->aft_discuss'." );
		}

		return parent::validate();
	}

	/**
	 * Fetch a list of entries
	 *
	 * @param string $name The list name (see static::$lists)
	 * @param mixed[optional] $shard Get only data for a certain shard value
	 * @param int[optional] $offset The offset to start from
	 * @param string[optional] $sort Sort to apply to list
	 * @param string[optional] $order Sort the list ASC or DESC
	 * @return DataModelList
	 */
	public static function getList( $name, $shard = null, $offset = null, $sort = 'relevance', $order = 'ASC' ) {
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
	 * @throw MWException
	 */
	public function insert() {
		// if no creation timestamp is entered yet, fill it out
		if ( $this->aft_timestamp === null ) {
			$this->aft_timestamp = wfTimestampNow();
		}

		$this->aft_net_helpful = $this->aft_helpful - $this->aft_unhelpful;
		$this->aft_relevance_score = $this->getRelevanceScore();
		$this->aft_has_comment = (bool) $this->aft_comment;
		$this->aft_archive_date = $this->getArchiveDate();
		$this->updateCountFound();

		return parent::insert();
	}

	/**
	 * Update entry in the DB (& cache)
	 *
	 * @return DataModel
	 * @throw MWException
	 */
	public function update() {
		$this->aft_net_helpful = $this->aft_helpful - $this->aft_unhelpful;
		$this->aft_relevance_score = $this->getRelevanceScore();
		$this->aft_has_comment = (bool) $this->aft_comment;
		$this->aft_archive_date = $this->getArchiveDate();
		$this->updateCountFound();

		return parent::update();
	}

	/**
	 * Populate object's properties with database (ResultWrapper) data.
	 *
	 * Assume that object properties & db columns are an exact match;
	 * if not, the extending class can extend this method.
	 *
	 * @param stdClass $row The db row
	 * @return DataModel
	 */
	public function toObject( stdClass $row ) {
		parent::toObject( $row );

		/*
		 * ID is saved as binary(32), but all older id values will remain
		 * unchanged, which will result in MySQL padding them to 32 length
		 * with null-bytes. We obviously want to strip these.
		 */
		$this->{static::getIdColumn()} = trim( $this->{static::getIdColumn()}, chr( 0 ) );

		return $this;
	}

	/**
	 * Will fetch a couple of items (from DB) and cache them.
	 *
	 * Fetching & caching as much as (useful) entries as possible will result
	 * in more efficient (fewer) queries to the backend.
	 *
	 * @param array $entries Array of items to be preloaded, in [id] => [shard] format
	 */
	public static function preload( array $entries ) {
		parent::preload( $entries );

		// when running unittests, ignore this
		if ( defined( 'MW_PHPUNIT_TEST' ) && MW_PHPUNIT_TEST ) {
			return;
		}

		/*
		 * Only editors will have the detailed toolbox, so only for editors,
		 * we'll need to know the details of the last editor activity.
		 * Readers will almost never need these details (apart from when
		 * visiting the permalink of a hidden post, in which case the mask
		 * will display details of when the post was hidden), so abstain
		 * from preloading this data.
		 */
		global $wgUser;
		if ( $wgUser->isAllowed( 'aft-editor' ) ) {
			// load editor activity for all requested entries
			ArticleFeedbackv5Activity::getLastEditorActivity( $entries );
		}
	}

	/**
	 * Get a list's conditions.
	 *
	 * @param string $name
	 * @return array
	 * @throws MWException
	 */
	public static function getListConditions( $name ) {
		if ( !isset( static::$lists[$name] ) ) {
			throw new MWException( "List '$name' is no known list" );
		}

		return isset( static::$lists[$name]['conditions'] ) ? static::$lists[$name]['conditions'] : array();
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
			if ( isset( $this->{"aft_$action"} ) ) {
				$total += $score * $this->{"aft_$action"};
			}
		}

		return $total;
	}

	/**
	 * Returns the archive date (if any)
	 *
	 * If there's a positive relevance score, the article can be
	 * considered useful and should not be auto-archived.
	 * Only set archive date if none is set already; otherwise
	 * leave the old date be
	 *
	 * @return mixed string with timestamp or null if no archive date
	 */
	public function getArchiveDate() {
		if ( $this->isFeatured() || $this->isResolved() || $this->isNonActionable() || $this->isHidden() ) {
			return null;
		} elseif ( !$this->aft_archive_date ) {
			global $wgArticleFeedbackAutoArchiveTtl;
			$wgArticleFeedbackAutoArchiveTtl = (array) $wgArticleFeedbackAutoArchiveTtl;
			$ttl = '+5 years';

			// ttl is set per x amount of unreviewed comments
			$count = static::getCount( 'unreviewed', $this->aft_page );

			ksort( $wgArticleFeedbackAutoArchiveTtl );
			foreach ( $wgArticleFeedbackAutoArchiveTtl as $amount => $time ) {
				if ( $amount <= $count ) {
					$ttl = $time;
				} else {
					break;
				}
			}

			$ttl = strtotime( $ttl ) - time();

			// convert creation timestamp to unix
			$creation = wfTimestamp( TS_UNIX, $this->aft_timestamp );

			// add ttl to creation timestamp and return in mediawiki timestamp format
			return wfTimestamp( TS_MW, $creation + $ttl );
		} else {
			return $this->aft_archive_date;
		}
	}

	/**
	 * @return string
	 */
	public function getExperiment() {
		return $this->aft_form . $this->aft_link;
	}

	/**
	 * Get article object for this entry
	 *
	 * @return Article|null Article object or null if invalid page
	 */
	public function getArticle() {
		return Article::newFromID( $this->aft_page );
	}

	/**
	 * Get revision object for this entry
	 *
	 * @return Revision|null Revision object or null if invalid revision
	 */
	public function getRevision() {
		return Revision::newFromId( $this->aft_page_revision );
	}

	/**
	 * Get user object for this entry
	 *
	 * @return User|bool User object or false if invalid user
	 */
	public function getUser() {
		if ( $this->aft_user ) {
			return User::newFromId( $this->aft_user );
		} else {
			return User::newFromName( $this->aft_user_text );
		}
	}

	/**
	 * ACTIONS-RELATED
	 */

	/**
	 * Get an entry's last editor activity
	 *
	 * @return stdClass|null
	 */
	public function getLastEditorActivity() {
		$activity = false;

		$activities = ArticleFeedbackv5Activity::getLastEditorActivity( array( array( 'id' => $this->{static::getIdColumn()}, 'shard' => $this->{static::getShardColumn()} ) ) );
		foreach ( $activities as $activity ) {
			break;
		}

		return $activity;
	}

	/**
	 * @return bool
	 */
	public function isFlagged() {
		return $this->aft_flag + $this->aft_autoflag > 0;
	}

	/**
	 * @return bool
	 */
	public function isFeatured() {
		return (bool) $this->aft_feature;
	}

	/**
	 * @return bool
	 */
	public function isResolved() {
		return (bool) $this->aft_resolve;
	}

	/**
	 * @return bool
	 */
	public function isNonActionable() {
		return (bool) $this->aft_noaction;
	}

	/**
	 * @return bool
	 */
	public function isInappropriate() {
		return (bool) $this->aft_inappropriate;
	}

	/**
	 * @return bool
	 */
	public function isArchived() {
		return (bool) $this->aft_archive;
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return (bool) $this->aft_hide;
	}

	/**
	 * @return bool
	 */
	public function isRequested() {
		return $this->aft_request && !$this->isDeclined();
	}

	/**
	 * @return bool
	 */
	public function isDeclined() {
		return (bool) $this->aft_decline;
	}

	/**
	 * @return bool
	 */
	public function isOversighted() {
		return (bool) $this->aft_oversight;
	}
}
