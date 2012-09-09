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
		$aft_id,
		$aft_page,
		$aft_page_revision,
		$aft_user,
		$aft_user_text,
		$aft_user_token,
		$aft_form,
		$aft_cta,
		$aft_link,
		$aft_rating,
		$aft_comment,

		// denormalized totals of which real records are in logging table
		$oversight,
		$unoversight,
		$decline,
		$request,
		$unrequest,
		$hidden, // includes autohide
		$unhidden,
		$flag, // includes autoflag
		$unflag, // includes clear-flags
		$feature,
		$unfeature,
		$resolve,
		$unresolve,
		$helpful, // includes undo-helpful
		$unhelpful; // includes undo-unhelpful

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
	protected static $shardKey = 'aft_page';

	/**
	 * Fetch feedback data from database
	 *
	 * @param RDBStoreTablePartition $partition The partition to fetch the data from
	 * @param array $conds The conditions
	 * @return ResultWrapper
	 */
	protected static function loadEntriesFromDB( $partition, $conds, $options = array() ) {
		return $partition->select(
			DB_SLAVE,
			array(
				'aft_id', 'aft_page', 'aft_revision', 'aft_user', 'aft_user_test',
				'aft_form', 'aft_cta', 'aft_link', 'aft_rating', 'aft_comment',
				'aft_timestamp'
			),
			$conds,
			__METHOD__,
			$options
		);
	}

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
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'visible-featured' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'visible-helpful' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'visible-comment' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'visible' => array(
			'permissions' => 'aft-reader',
			'conditions' => array( '' ),
			'order' => array( '' )
		),

		// editor lists
		'visible-unhelpful' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'visible-abusive' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'visible-resolved' => array(
			'permissions' => 'aft-editor',
			'conditions' => array( '' ),
			'order' => array( '' )
		),

		// monitor lists
		'notdeleted-hidden' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'notdeleted-declined' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'notdeleted' => array(
			'permissions' => 'aft-monitor',
			'conditions' => array( '' ),
			'order' => array( '' )
		),

		// oversighter lists
		'notdeleted-requested' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
/*
		'all-hidden' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'all-requested' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'all-declined' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
*/
		'all-oversighted' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array( '' ),
			'order' => array( '' )
		),
		'all' => array(
			'permissions' => 'aft-oversighter',
			'conditions' => array(),
			'order' => array()
		)
/*
		// @todo: this is temp; it's all listsm but conditions need work, stuff will likely stll change, you get the picture ;)
		'visible-relevant' => array( 'permissions' => 'aft-reader', 'conds' => array( 'aft_action_hide > aft_action_unhide' ) ), // @todo: filter is basically the same as "visible" - check what _should_ be different (a min amount of relevance? only order?)
		'visible-featured' => array( 'permissions' => 'aft-reader', 'conds' => array( 'aft_action_hide > aft_action_unhide', 'aft_action_feature > aft_action_unfeature' ) ), // when unfeaturing, keep increment a column "unfeature" or decrement column "feature" - same for most other conds
		'visible-helpful' => array( 'permissions' => 'aft-reader', 'conds' => array( 'aft_action_hide > aft_action_unhide', 'aft_action_helpful > aft_action_unhelpful' ) ), // @todo: helpful > unhelpful, or helpful > 0?
		'visible-comment' => array( 'permissions' => 'aft-reader', 'conds' => array( 'aft_action_hide > aft_action_unhide', 'comment IS NOT NULL' ) ),
		'visible' => array( 'permissions' => 'aft-reader', 'conds' => array( 'aft_action_hide > aft_action_unhide' ) ),

		'visible-unhelpful' => array( 'permissions' => 'aft-editor', 'conds' => array( 'aft_action_hide > aft_action_unhide', 'aft_action_unhelpful > aft_action_helpful' ) ),
		'visible-abusive' => array( 'permissions' => 'aft-editor', 'conds' => array( 'aft_action_hide > aft_action_unhide', 'aft_action_flag > aft_action_unflag' ) ),
		'visible-resolved' => array( 'permissions' => 'aft-editor', 'conds' => array( 'aft_action_hide > aft_action_unhide', 'aft_action_resolve > aft_action_unresolve' ) ),

		'notdeleted-hidden' => array( 'permissions' => 'aft-monitor', 'conds' => array( 'aft_action_oversight  <= aft_action_unoversight', 'aft_action_hide > aft_action_unhide' ) ),
		'notdeleted-declined' => array( 'permissions' => 'aft-monitor', 'conds' => array( 'aft_action_oversight <= aft_action_unoversight', 'aft_action_decline > aft_action_request' ) ),
		'notdeleted' => array( 'permissions' => 'aft-monitor', 'conds' => array( 'aft_action_oversight <= aft_action_unoversight' ) ),

		'notdeleted-requested' => array( 'permissions' => 'aft-oversighter', 'conds' => array( 'aft_action_oversight < aft_action_unoversight', 'aft_action_request > aft_action_unrequest' ) ),
		'all-hidden' => array( 'permissions' => 'aft-oversighter', 'conds' => array( 'aft_action_hide > aft_action_unhide' ) ),
		'all-requested' => array( 'permissions' => 'aft-oversighter', 'conds' => array( 'aft_action_request > aft_action_decline' ) ),
		'all-declined' => array( 'permissions' => 'aft-oversighter', 'conds' => array( 'aft_action_decline > aft_action_request' ) ),
		'all-oversighted' => array( 'permissions' => 'aft-oversighter', 'conds' => array( 'aft_action_oversight > aft_action_unoversight' ) ),
		'all' => array( 'permissions' => 'aft-oversighter', 'conds' => array() )
*/
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
			&& strlen( $this->aft_comment ) > $wgArticleFeedbackv5MaxCommentLength ) {
			throw new MWException( "Comment length exceeds the maximum of '$wgArticleFeedbackv5MaxCommentLength'." );
		}

		return parent::validate();
	}

	/**
	 * Fetch a list of entries
	 *
	 * @param string $name The list name (see self::$lists)
	 * @param int $offset The offset to start from (a multiple of self::LIST_LIMIT)
	 * @param string $sort Sort the list ASC or DESC
	 * @return array
	 */
	public static function getList( $name, $offset = 0, $sort = 'ASC' ) {
		global $wgUser;

		if ( $wgUser->isAllowed( self::$lists['permissions'] ) ) {
			throw new MWException( "List '$name' is not allowed for this user" );
		}

		return parent::getList( $name, $offset, $sort );
	}

	/**
	 * Insert entry into the DB (& cache)
	 *
	 * @return DataModel
	 */
	public function insert() {
		// if no creation timestamp is entered yet, fill it out
		if ( $this->aft_timestamp === null ) {
			// get hold of feedback shard
			$storeGroup = RDBStoreGroup::singleton();
			$store = $storeGroup->getForTable( self::getTable() );
			$partition = $store->getPartition( self::getTable(), self::getShardColumn(), $this->{self::getShardColumn()} );

			$this->aft_timestamp = $partition->getMasterDB()->timestamp( wfTimestampNow() );
		}

		parent::insert();
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
			'afpageid'     => $this->aft_page,
			'maxage'       => 0,
			'smaxage'      => $wgArticleFeedbackv5SMaxage
		) );
		$squidUpdate = new SquidUpdate( array( $uri->toString() ) );
		$squidUpdate->doUpdate();

		// @todo: there's probably more uri's that could use a squid purge when data is updated ;)

		return $this;
	}

	/**
	 * Get user object for this entry
	 *
	 * @return User
	 */
	public function getUser() {
		// @todo: I would actually like a fancy object for anons as well; does User::newFromName( <ip-or-username> ) work fine?
		return User::newFromId( $this->aft_user );
	}
}
