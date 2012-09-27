<?php
/**
 * ArticleFeedbackv5Fetch class
 *
 * @package    ArticleFeedback
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

/**
 * Handles fetching of feedback
 *
 * @package    ArticleFeedback
 */
class ArticleFeedbackv5Fetch {

	/**
	 * The page ID
	 *
	 * @var int
	 */
	private $pageId;

	/**
	 * The user ID
	 *
	 * @var int
	 */
	private $userId;

	/**
	 * The feedback ID
	 *
	 * @var int
	 */
	private $feedbackId;

	/**
	 * The filter
	 *
	 * @var string
	 */
	private $filter = 'visible';

	/**
	 * The sort method
	 *
	 * @var string
	 */
	private $sort = 'age';

	/**
	 * The sort order
	 *
	 * @var string ('asc' or 'desc')
	 */
	private $sortOrder = 'desc';

	/**
	 * The limit
	 *
	 * @var int
	 */
	private $limit = 25;

	/**
	 * Continue information
	 *
	 * Instead of using offset (which would break if, for example, there was a
	 * flood of new records and we were sorting by date), we use information
	 * about the last row displayed to nail down what should come next.  For
	 * example, in a rating sort, if a hundred comments had the same rating, and
	 * we needed to show the second set of 25, we'd make sure the list was
	 * sorted by rating, then timestamp, then id, and this array would contain
	 * the rating, timestamp, and id of the last record sent.
	 *
	 * @var array
	 */
	private $continue;

	/**
	 * The known sort methods
	 *
	 * @var array
	 */
	public static $knownSorts = array( 'relevance', 'age', 'helpful', 'rating' );

	/**
	 * The known filters
	 *
	 * @var array
	 */
	public static $knownFilters = array( 'id', 'highlight', 'visible',
		'visible-relevant', 'visible-comment', 'visible-helpful',
		'visible-unhelpful', 'visible-abusive', 'visible-featured',
		'visible-unfeatured', 'visible-resolved', 'visible-unresolved',
		'notdeleted-hidden', 'all-hidden', 'notdeleted', 'notdeleted-unhidden',
		'all-unhidden', 'notdeleted-requested', 'all', 'all-requested',
		'notdeleted-unrequested', 'all-unrequested', 'notdeleted-declined',
		'all-declined', 'all-oversighted', 'all-unoversighted' );

	/**
	 * The filters including hidden feedback
	 *
	 * @var array
	 */
	public static $hiddenFilters  = array( 'all-hidden', 'notdeleted-hidden',
		'all-unhidden', 'notdeleted-unhidden', 'all-requested',
		'all-unrequested', 'notdeleted-unrequested',
		'all-declined', 'notdeleted-declined', 'all-oversighted',
		'all-unoversighted', 'notdeleted', 'all' );

	/**
	 * The filters including deleted feedback
	 *
	 * @var array
	 */
	public static $deletedFilters = array( 'all', 'all-unoversighted',
		'all-oversighted', 'all-hidden', 'all-unhidden', 'all-requested',
		'notdeleted-requested', 'all-unrequested', 'all-declined');

	/**
	 * Constructor
	 *
	 * @param string $filter      the filter
	 * @param mixed  $filterValue the filter value (only for filter "id")
	 * @param int    $pageId      the page ID
	 * @param int    $userId      the user ID
	 */
	public function __construct( $filter = null, $filterValue = null, $pageId = null, $userId = null ) {
		if ( $filter ) {
			$this->setFilter( $filter );
		}
		if ( ( $filter == 'id' || $filter == 'highlight' ) && $filterValue ) {
			$this->setFeedbackId( $filterValue );
		}
		if ( $pageId ) {
			$this->setPageId( $pageId );
		}
		if ( $userId ) {
			$this->setUserId( $userId );
		}
		global $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		if ( $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay ) {
			$this->limit = $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		}
	}

	/**
	 * Runs the fetch
	 *
	 * @return stdClass the results, as {
	 *                    showMore => {bool}
	 *                    records  => {array}
	 *                  }
	 */
	public function run() {
		$dbr = wfGetDB( DB_SLAVE );

		$result = new stdClass;
		$result->showMore = false;
		$result->records  = array();

		$direction = strtolower( $this->sortOrder ) == 'asc' ? 'ASC' : 'DESC';
		$continueDirection = ( $direction == 'ASC' ? '>' : '<' );

		$ratingFields  = array( -1 );
		$commentFields = array( -1 );
		// This is in memcache so I don't feel that bad re-fetching it.
		// Needed to join in the comment and rating tables, for filtering
		// and sorting, respectively.
		foreach ( ApiArticleFeedbackv5Utils::getFields() as $field ) {
			if ( in_array( $field['afi_bucket_id'], array( 1, 6 ) ) && $field['afi_name'] == 'comment' ) {
				$commentFields[] = (int) $field['afi_id'];
			}
			if ( in_array( $field['afi_bucket_id'], array( 1, 6 ) ) && $field['afi_name'] == 'found' ) {
				$ratingFields[] = (int) $field['afi_id'];
			}
		}

		// Build ORDER BY clause.
		switch ( $this->sort ) {
			case 'relevance':
				$sortField = 'af_relevance_sort';
				$order       = "af_relevance_sort $direction, af_id $direction";
				if ( $this->continue['af_id'] != '' ) {
					$continueSql = "(af_relevance_sort $continueDirection " . intVal( $this->continue['af_relevance_sort'] )
					 . " OR (af_relevance_sort = " . intVal( $this->continue['af_relevance_sort'] )
					 . " AND af_id $continueDirection " . intval( $this->continue['af_id'] ) . ") )";
				}
				break;

			case 'helpful':
				$sortField   = 'af_net_helpfulness';
				$order       = "af_net_helpfulness $direction, af_id $direction";
				if ( $this->continue['af_id'] != '' ) {
					$continueSql = "(af_net_helpfulness $continueDirection " . intVal( $this->continue['af_net_helpfulness'] )
					 . " OR (af_net_helpfulness = " . intVal( $this->continue['af_net_helpfulness'] )
					 . " AND af_id $continueDirection " . intval( $this->continue['af_id'] ) . ") )";
				}
				break;

			case 'rating':
				# TODO: null ratings don't seem to show up at all. Need to sort that one out.
				$sortField   = 'rating';
				$order       = "yes_no $direction, af_id $direction";
				if ( $this->continue['af_id'] != '' ) {
					$continueSql = "(rating.aa_response_boolean $continueDirection " . intVal( $this->continue['aa_response_boolean'] )
					 . " OR (rating.aa_response_boolean = " . intVal( $this->continue['aa_response_boolean'] )
					 . " AND af_id $continueDirection " . intval( $this->continue['af_id'] ) . ") )";
				}
				break;

			case 'age':
				# Default field, fall through
			default:
				$sortField   = 'af_id';
				$order       = "af_id $direction";
				if ( $this->continue['af_id'] != '' ) {
					$continueSql = "af_id $continueDirection " . intVal( $this->continue['af_id'] );
				}
				break;
		}

		/* Build the basic query parameters */
		$tables = array(
			'aft_article_feedback',
			'rating'  => 'aft_article_answer',
			'comment' => 'aft_article_answer',
		);
		$vars = array(
			'af_id',
			'af_net_helpfulness',
			'af_relevance_sort',
			'rating.aa_response_boolean AS yes_no'
		);
		$conds = $this->getFilterCriteria();
		$options = array(
			'LIMIT'    => ( $this->limit + 1 ),
			'ORDER BY' => $order
		);
		$join_conds = array(
			'rating'  => array(
				'LEFT JOIN',
				array(
					'rating.aa_feedback_id = af_id',
					'rating.aa_field_id' => $ratingFields
				)
			),
			'comment' => array(
				'LEFT JOIN',
				array(
					'comment.aa_feedback_id = af_id',
					'comment.aa_field_id' => $commentFields,
				)
			)
		);

		// Build WHERE clause.
		// Filter applied:
		// PageID:
		if ( $this->pageId ) {
			$conds['af_page_id'] = $this->pageId;
		}
		// UserID:
		if ( $this->userId ) {
			$tables[] = 'page';
			$tables[] = 'watchlist';

			$join_conds['page'] = array(
				'INNER JOIN',
				'page_id = af_page_id'
			);
			$join_conds['watchlist'] = array(
				'INNER JOIN',
				array(
					'wl_user' => $this->userId,
					'wl_namespace = page_namespace',
					'wl_title = page_title'
				)
			);
		}
		// Continue SQL, if any:
		if ( $this->continue !== null && isset( $continueSql ) && $continueSql ) {
			$conds[] = $continueSql;
		}
		// Only show bucket 1 (per Fabrice on 1/25)
		$conds['af_form_id'] = array( 1, 6 );

		// Fetch the feedback IDs we need.
		/* I'd really love to do this in one big query, but MySQL
		   doesn't support LIMIT inside IN() subselects, and since
		   we don't know the number of answers for each feedback
		   record until we fetch them, this is the only way to make
		   sure we get all answers for the exact IDs we want. */
		$id_query = $dbr->select( $tables, $vars, $conds, __METHOD__, $options, $join_conds );

		$ids = array();
		foreach ( $id_query as $id ) {
			$ids[$id->af_id] = $id->af_id;
			// Get the continue values from the last counted item.
			if ( count( $ids ) == $this->limit ) {
				$result->continue = $this->buildContinue($id);
			}
		}
		if ( !count( $ids ) ) {
			return $result;
		}

		// Returned an extra row, meaning there's more to show.
		// Also, pop that extra one off, so we don't render it.
		if ( count( $ids ) > $this->limit ) {
			$result->showMore = true;
			array_pop( $ids );
		}

		// Select rows
		$rows  = $dbr->select(
			array( 'aft_article_feedback',
				'rating' => 'aft_article_answer',
				'answer' => 'aft_article_answer',
				'aft_article_field',
				'aft_article_field_option', 'user', 'page'
			),
			array( 'af_id', 'af_page_id', 'af_form_id', 'af_experiment', 'afi_name', 'afo_name',
				'answer.aa_response_text', 'answer.aa_response_boolean', 'answer.aat_id',
				'answer.aa_response_rating', 'answer.aa_response_option_id',
				'afi_data_type', 'af_created', 'user_name',
				'af_user_id', 'af_user_ip', 'af_is_hidden', 'af_abuse_count',
				'af_helpful_count', 'af_unhelpful_count',
				'af_is_deleted', 'af_oversight_count', 'af_revision_id',
				'af_net_helpfulness', 'af_relevance_score', 'af_revision_id',
				'page_latest', 'page_title', 'page_namespace',
				'rating.aa_response_boolean AS yes_no',
				'af_is_featured', 'af_is_resolved',
				'af_last_status', 'af_last_status_user_id',
				'af_last_status_timestamp', 'af_last_status_notes',
				'af_suppress_count', 'af_activity_count'
			),
			array( 'af_id' => $ids ),
			__METHOD__,
			array(),
			array(
				'rating' => array(
					'LEFT JOIN',
					array(
						'rating.aa_feedback_id = af_id',
						'rating.aa_field_id' => $ratingFields
					)
				),
				'answer' => array(
					'LEFT JOIN',
					'answer.aa_feedback_id = af_id'
				),
				'aft_article_field' => array(
					'LEFT JOIN',
					'afi_id = answer.aa_field_id'
				),
				'aft_article_field_option' => array(
					'LEFT JOIN',
					'answer.aa_response_option_id = afo_option_id'
				),
				'user' => array(
					'LEFT JOIN',
					'user_id = af_user_id'
				),
				'page' => array(
					'JOIN',
					'page_id = af_page_id'
				),
			)
		);

		// our $ids array is the correct order for every id that we're doing
		// so we want to graft the extra data here into the id value
		$fetchedIds = array();
		foreach ( $rows as $row ) {
			$fetchedIds[] = (int) $row->af_id;

			if ( !array_key_exists( $row->af_id, $ids ) ) {
				continue; // something has gone dreadfully wrong actually
			} elseif ( !is_array( $ids[$row->af_id] ) ) {
				$ids[$row->af_id] = array();
				$ids[$row->af_id][0] = $row;
				$ids[$row->af_id][0]->user_name = $row->user_name ? $row->user_name : $row->af_user_ip;
			}
			$ids[$row->af_id][$row->afi_name] = $row;

			// fetch full long content (if any) - only for permalink page
			if ( $this->getFeedbackId() != null && $row->aat_id != null ) {
				$row->aa_response_text = $dbr->selectField(
					array( 'aft_article_answer_text' ),
					array( 'aat_response_text' ),
					array( 'aat_id' => (int) $row->aat_id )
				);
			}
		}

		// doublecheck that we fetched info for all id's from the db; if not,
		// we should remove them from our results array (since it is not enriched
		// with newly fetched data
		$originalIds = array_keys( $ids );
		$fetchedIds = array_unique( $fetchedIds );
		$missingIds = array_diff( $originalIds, $fetchedIds );
		foreach ( $missingIds as $id ) {
			unset( $ids[$id] );
		}

		$result->records = $ids;

		return $result;
	}

	/**
	 * Gets the where clauses to add to the query, by filter
	 *
	 * @return array the where clauses
	 */
	public function getFilterCriteria() {
		global $wgUser, $wgArticleFeedbackv5Cutoff;

		$where = array();

		// Never show hidden or deleted posts unless specifically requested
		// and user has access.
		if ( !in_array( $this->filter, self::$deletedFilters )
		 || !$wgUser->isAllowed( 'aft-oversighter' ) ) {
			$where[] = 'af_is_deleted IS FALSE';
			$where[] = 'af_oversight_count = 0';
		}
		if ( !in_array( $this->filter, self::$hiddenFilters )
		 || !$wgUser->isAllowed( 'aft-monitor' ) ) {
			$where[] = 'af_is_hidden IS FALSE';
		}

		switch ( $this->filter ) {
			// special case - doesn't get any hidden/deleted filtering and is used for permalinks
			case 'id':
				// overwrite any and all where conditions
				$where = array('af_id' => $this->feedbackId);
				break;
			// special case - just get the highlighted post
			case 'highlight':
				// overwrite any and all where conditions
				$where = array('af_id' => $this->feedbackId);
				break;
			// regular filters
			case 'visible-relevant':
				$where[] = '(af_is_featured IS TRUE OR af_has_comment is true OR af_net_helpfulness > 0) AND af_relevance_score > ' . $wgArticleFeedbackv5Cutoff;
				break;
			case 'visible-comment':
				$where[] = 'af_has_comment IS TRUE';
				break;
			case 'visible-helpful':
				$where[] = 'af_net_helpfulness > 0';
				break;
			case 'visible-unhelpful':
				$where[] = 'af_net_helpfulness < 0';
				break;
			case 'visible-abusive':
				$where[] = 'af_abuse_count > 0';
				break;
			case 'visible-featured':
				$where[] = 'af_is_featured IS TRUE';
				break;
			case 'visible-unfeatured':
				$where[] = 'af_is_unfeatured IS TRUE';
				break;
			case 'visible-resolved':
				$where[] = 'af_is_resolved IS TRUE';
				break;
			case 'visible-unresolved':
				$where[] = 'af_is_unresolved IS TRUE';
				break;
			case 'notdeleted-hidden':
			case 'all-hidden':
				$where[] = 'af_is_hidden IS TRUE';
				break;
			case 'notdeleted-unhidden':
			case 'all-unhidden':
				$where[] = 'af_is_unhidden IS TRUE';
				break;
			case 'notdeleted-requested':
			case 'all-requested':
				$where[] = 'af_oversight_count > 0';
				break;
			case 'notdeleted-unrequested':
			case 'all-unrequested':
				$where[] = 'af_is_unrequested IS TRUE';
				break;
			case 'notdeleted-declined':
			case 'all-declined':
				$where[] = 'af_is_declined IS TRUE';
				break;
			case 'all-oversighted':
				$where[] = 'af_is_deleted IS TRUE';
				break;
			case 'all-unoversighted':
				$where[] = 'af_is_undeleted IS TRUE';
				break;
			default:
				break;
		}

		return $where;
	}

	/**
	 * Get the total number of responses, not taking any filters into account
	 *
	 * @return int the count
	 */
	public function overallCount() {
		$dbr   = wfGetDB( DB_SLAVE );
		$where = array( 'afc_filter_name' => 'all' );
		$where['afc_page_id'] = $this->pageId ? $this->pageId : 0;
		$count = $dbr->selectField(
			array( 'aft_article_filter_count' ),
			array( 'afc_filter_count' ),
			$where,
			__METHOD__
		);
		// selectField returns false if there's no row, so make that 0
		return $count ? $count : 0;
	}

	/**
	 * Gets the page ID
	 *
	 * @return int the page ID
	 */
	public function getPageId() {
		return $this->pageId;
	}

	/**
	 * Sets the page ID
	 *
	 * @param  $pageId int the page ID
	 */
	public function setPageId( $pageId ) {
		if ( is_numeric( $pageId ) ) {
			$this->pageId = intval( $pageId );
		}
	}

	/**
	 * Gets the user ID
	 *
	 * @return int the user ID
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * Sets the user ID
	 *
	 * @param  $userId int the user ID
	 */
	public function setUserId( $userId ) {
		if ( is_numeric( $userId ) ) {
			$this->userId = intval( $userId );
		}
	}

	/**
	 * Gets the feedback ID
	 *
	 * @return int the feedback ID
	 */
	public function getFeedbackId() {
		return $this->feedbackId;
	}

	/**
	 * Sets the feedback ID
	 *
	 * @param  $feedbackId int the feedback ID
	 */
	public function setFeedbackId( $feedbackId ) {
		if ( is_numeric( $feedbackId ) ) {
			$this->feedbackId = intval( $feedbackId );
		}
	}

	/**
	 * Gets the filter
	 *
	 * @return string the filter
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * Sets the filter
	 *
	 * @param  $filter string the filter
	 */
	public function setFilter( $filter ) {
		if ( in_array( $filter, self::$knownFilters ) ) {
			$this->filter = $filter;
		}
	}

	/**
	 * Gets the sort method
	 *
	 * @return string the sort method
	 */
	public function getSort() {
		return $this->sort;
	}

	/**
	 * Sets the sort method
	 *
	 * @param  $sort string the sort method
	 */
	public function setSort( $sort ) {
		if ( in_array( $sort, self::$knownSorts ) ) {
			$this->sort = $sort;
		}
	}

	/**
	 * Gets the sort order
	 *
	 * @return string the sort order
	 */
	public function getSortOrder() {
		return $this->sortOrder;
	}

	/**
	 * Sets the sort order
	 *
	 * @param  $sortOrder string the sort order
	 */
	public function setSortOrder( $sortOrder ) {
		if ( strtolower( $sortOrder ) == 'asc' ) {
			$this->sortOrder = 'asc';
		}
		elseif ( strtolower( $sortOrder ) == 'desc' ) {
			$this->sortOrder = 'desc';
		}
	}

	/**
	 * Gets the limit
	 *
	 * @return int the limit
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Sets the limit
	 *
	 * @param  $limit int the limit
	 */
	public function setLimit( $limit ) {
		if ( is_numeric( $limit ) ) {
			$this->limit = intval( $limit );
		}
	}

	/**
	 * Sets the continue information
	 *
	 * @param  $continue   string the continue info, as val1|val2
	 */
	public function setContinue( $continue ) {
		$this->continue = array();
		switch( $this->sort ) {
			case 'relevance':
				list( $c1, $c2 ) = explode( '|', $continue );
				$this->continue['af_relevance_sort'] = $c1;
				$this->continue['af_id'] = $c2;
				break;
			case 'helpful':
				list( $c1, $c2 ) = explode( '|', $continue );
				$this->continue['af_net_helpfulness'] = $c1;
				$this->continue['af_id'] = $c2;
				break;
			case 'rating':
				list( $c1, $c2 ) = explode( '|', $continue );
				$this->continue['aa_response_boolean'] = $c1;
				$this->continue['af_id'] = $c2;
				break;
			case 'age':
			default:
				$this->continue['af_id'] = $continue;
				break;
		}
	}

	/**
	 * Builds the continue information
	 *
	 * @param  $last stdClass the last record
	 * @return string the continue info, as val1|val2
	 */
	public function buildContinue( $record ) {
		switch( $this->sort ) {
			case 'relevance':
				return $record->af_relevance_sort . '|' . $record->af_id;
			case 'helpful':
				return $record->af_net_helpfulness . '|' . $record->af_id;
			case 'rating':
				return $record->aa_response_boolean . '|' . $record->af_id;
			case 'age':
			default:
				return $record->af_id;
		}
	}

}

