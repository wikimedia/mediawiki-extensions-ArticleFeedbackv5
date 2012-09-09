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
		$unoversight = 0,
		$decline = 0,
		$request = 0,
		$unrequest = 0,
		$hide = 0, // includes autohide
		$unhide = 0,
		$flag = 0, // includes autoflag
		$unflag = 0, // includes clear-flags
		$feature = 0,
		$unfeature = 0,
		$resolve = 0,
		$unresolve = 0,
		$helpful = 0, // includes undo-helpful
		$unhelpful = 0, // includes undo-unhelpful

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
	 * All lists the data can be displayed as
	 *
	 * Key is the filter name, the value is an array of
	 * * the conditions an "entry" must abide to to qualify for this list
	 * * the column to sort on
	 *
	 * @var array
	 */
	public static $lists = array(
		// reader lists
		'visible-relevant' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""' ), // @todo: also needs: '$this->relevance_score > $wgArticleFeedbackv5Cutoff'
			'order' => array( '$this->relevance_score' )
		),
		'visible-featured' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""', '$this->feature > $this->unfeature' ),
			'order' => array( '' )
		),
		'visible-helpful' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""', '$this->helpful > $this->unhelpful' ),
			'order' => array( '' )
		),
		'visible' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""' ),
			'order' => array( '$this->timestamp' )
		),

		// editor lists
		'visible-unhelpful' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""', '$this->helpful < $this->unhelpful' ),
			'order' => array( '' )
		),
		'visible-abusive' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""', '$this->flag > $this->unflag' ),
			'order' => array( '' )
		),
		'visible-resolved' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""', '$this->resolve > $this->unresolve' ),
			'order' => array( '' )
		),
		'visible-unresolved' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '$this->hide <= $this->unhide', '$this->comment != ""', '$this->resolve <= $this->unresolve' ),
			'order' => array( '$this->timestamp' )
		),

		// monitor lists
		'notdeleted-hidden' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '$this->oversight <= $this->unoversight', '$this->comment != ""', '$this->hide > $this->unhide' ),
			'order' => array( '' )
		),
		'notdeleted-declined' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '$this->oversight <= $this->unoversight', '$this->comment != ""', '$this->decline > 0' ),
			'order' => array( '' )
		),
		'notdeleted' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '$this->oversight <= $this->unoversight', '$this->comment != ""' ),
			'order' => array( '' )
		),

		// oversighter lists
		'notdeleted-requested' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '$this->oversight <= $this->unoversight', '$this->comment != ""', '$this->request > $this->unrequest' ),
			'order' => array( '' )
		),
		'all-oversighted' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '$this->oversight > $this->unoversight', '$this->comment != ""' ),
			'order' => array( '' )
		),
		'all' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '$this->comment != ""' ),
			'order' => array()
		)
	);

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
	 * @param string[optional] $sort Sort the list ASC or DESC
	 * @return array
	 */
	public static function getList( $name, $shard = null, $offset = 0, $sort = 'ASC' ) {
		global $wgUser;

		if ( $wgUser->isAllowed( self::$lists['permissions'] ) ) {
			throw new MWException( "List '$name' is not allowed for this user" );
		}

		return parent::getList( $name, $shard, $offset, $sort );
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

		return parent::insert();
	}

	/**
	 * Update entry in the DB (& cache)
	 *
	 * @return DataModel
	 */
	public function update() {
		$this->relevance_score = $this->getRelevanceScore();

		return parent::update();
	}

	/**
	 * Purge relevant Squid cache when updating data
	 *
	 * @return DataModel
	 */
	public function purgeSquidCache() {
		global $wgArticleFeedbackv5SMaxage;

		// purge squid cache for ratings api
		$uri = new Uri( wfScript( 'api' ) );
		$uri->extendQuery( array(
			'action'       => 'query',
			'format'       => 'json',
			'list'         => 'articlefeedbackv5-view-ratings',
			'afpageid'     => $this->page,
			'maxage'       => 0,
			'smaxage'      => $wgArticleFeedbackv5SMaxage
		) );
		$squidUpdate = new SquidUpdate( array( $uri->toString() ) );
		$squidUpdate->doUpdate();

		// @todo: there's probably more uri's that could use a squid purge when data is updated ;)

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
	 * Get actions performed on feedback
	 *
	 * @return array
	 */
	public function getActions() {
		// @todo
	}
}
