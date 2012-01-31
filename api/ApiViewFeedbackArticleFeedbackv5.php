<?php
/**
 * ApiViewFeedbackArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 */

/**
 * This class pulls the individual ratings/comments for the feedback page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiViewFeedbackArticleFeedbackv5 extends ApiQueryBase {
	private $continue;

	/**
	 * Constructor
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'afvf' );
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		$params   = $this->extractRequestParams();
		$result   = $this->getResult();
		$pageId   = $params['pageid'];
		$html     = '';
		$length   = 0;
		$count    = $this->fetchFeedbackCount( $params['pageid'] );
		$feedback = $this->fetchFeedback(
			$params['pageid'],
			$params['filter'],
			$params['filtervalue'],
			$params['sort'],
			$params['sortdirection'],
			$params['limit'],
			( $params['continue'] !== 'null' ? $params['continue'] : null )
		);

		foreach ( $feedback as $record ) {
			$html .= $this->renderFeedback( $record );
			$length++;
		}

		$continue = $this->continue;

		$result->addValue( $this->getModuleName(), 'length', $length );
		$result->addValue( $this->getModuleName(), 'count', $count );
		$result->addValue( $this->getModuleName(), 'feedback', $html );
		if ( $continue ) {
			$result->addValue( $this->getModuleName(), 'continue', $continue );
		}
	}

	public function fetchFeedbackCount( $pageId ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$count = $dbr->selectField(
			array( 'aft_article_filter_count' ),
			array( 'afc_filter_count' ),
			array(
				'afc_page_id'     => $pageId,
				'afc_filter_name' => 'all'
			),
			__METHOD__
		);
		// selectField returns false if there's no row, so make that 0
		return $count ? $count : 0;
	}

	public function fetchFeedback( $pageId, $filter = 'visible',
	 $filterValue = null, $sort = 'age', $sortOrder = 'desc',
	 $limit = 25, $continue = null ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$ids   = array();
		$rows  = array();
		$rv    = array();

		$direction         = strtolower( $sortOrder ) == 'asc' ? 'ASC' : 'DESC';
		$continueDirection = ( $direction == 'ASC' ? '>' : '<' );
		$order;
		$continueSql;
		$sortField;

		$ratingField  = 0;
		$commentField = 0;
		// This is in memcache so I don't feel that bad re-fetching it.
		// Needed to join in the comment and rating tables, for filtering
		// and sorting, respectively.
		foreach( ApiArticleFeedbackv5Utils::getFields() as $field ) {
			if( $field['afi_bucket_id'] == 1 && $field['afi_name'] == 'comment' ) {
				$commentField = $field['afi_id'];
			}
			if( $field['afi_bucket_id'] == 1 && $field['afi_name'] == 'found' ) {
				$ratingField = $field['afi_id'];
			}
		}

		// Build ORDER BY clause.
		switch( $sort ) {
			case 'helpful':
				$sortField   = 'net_helpfulness';
				// Can't use aliases in mysql where clauses.
				$continueSql = "CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) $continueDirection";
				break;
			case 'rating':
				$sortField = 'rating';
				break;
			case 'age':
				# Default field, fall through
			default:
				$sortField   = 'af_id';
				$continueSql = "$sortField $continueDirection";
				break;
		}
		$order = "$sortField $direction";

		// Build WHERE clause.
		// Filter applied , if any:
		$where = $this->getFilterCriteria( $filter, $filterValue );
		// PageID:
		$where['af_page_id'] = $pageId;
		// Continue value, if any:
		if ( $continue !== null ) {
			$where[] = $continueSql.' '.intVal( $continue );
		}
		// Only show bucket 1 (per Fabrice on 1/25)
		$where['af_bucket_id'] = 1;

		// Fetch the feedback IDs we need.
		/* I'd really love to do this in one big query, but MySQL
		   doesn't support LIMIT inside IN() subselects, and since
		   we don't know the number of answers for each feedback
		   record until we fetch them, this is the only way to make
		   sure we get all answers for the exact IDs we want. */
		$id_query = $dbr->select(
			array(
				'aft_article_feedback',
				'rating'  => 'aft_article_answer',
				'comment' => 'aft_article_answer',
			),
			array(
				'af_id',
				'CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) AS net_helpfulness',
				'rating.aa_response_boolean AS rating'
			),
			$where,
			__METHOD__,
			array(
				'LIMIT'    => $limit,
				'ORDER BY' => $order
			),
			array(
				'rating'  => array(
					'LEFT JOIN',
					'rating.aa_feedback_id = af_id AND rating.aa_field_id = '.intval( $ratingField )
				),
				'comment' => array(
					'LEFT JOIN',
					'comment.aa_feedback_id = af_id AND comment.aa_field_id = '.intval( $commentField )
				)
			)
		);
		foreach ( $id_query as $id ) {
			$ids[] = $id->af_id;
			$this->continue = $id->$sortField;
		}

		if ( !count( $ids ) ) {
			return array();
		}

		$rows  = $dbr->select(
			array( 'aft_article_feedback',
				'rating' => 'aft_article_answer',
				'answer' => 'aft_article_answer',
				'aft_article_field',
				'aft_article_field_option', 'user', 'page'
			),
			array( 'af_id', 'af_bucket_id', 'afi_name', 'afo_name',
				'answer.aa_response_text', 'answer.aa_response_boolean',
				'answer.aa_response_rating', 'answer.aa_response_option_id',
				'afi_data_type', 'af_created', 'user_name',
				'af_user_ip', 'af_is_hidden', 'af_abuse_count',
				'af_helpful_count', 'af_unhelpful_count',
				'af_is_deleted', 'af_needs_oversight',
				'(SELECT COUNT(*) FROM ' . $dbr->tableName( 'revision' ) . ' WHERE rev_id > af_revision_id AND rev_page = ' . ( integer ) $pageId . ') AS age',
				'CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) AS net_helpfulness',
				'page_latest', 'af_revision_id', 'page_title',
				'rating.aa_response_boolean AS rating'
			),
			array( 'af_id' => $ids ),
			__METHOD__,
			array( 'ORDER BY' => $order ),
			array(
				'rating' => array(
					'LEFT JOIN',
					'rating.aa_feedback_id = af_id AND rating.aa_field_id = ' . intval( $ratingField )
				),
				'answer' => array(
					'LEFT JOIN', 'af_id = answer.aa_feedback_id'
				),
				'aft_article_field' => array(
					'LEFT JOIN', 'afi_id = answer.aa_field_id'
				),
				'aft_article_field_option' => array(
					'LEFT JOIN',
					'answer.aa_response_option_id = afo_option_id'
				),
				'user' => array(
					'LEFT JOIN', 'user_id = af_user_id'
				),
				'page' => array(
					'JOIN', 'page_id = af_page_id'
				)
			)
		);

		foreach ( $rows as $row ) {
			if ( !array_key_exists( $row->af_id, $rv ) ) {
				$rv[$row->af_id]    = array();
				$rv[$row->af_id][0] = $row;
				$rv[$row->af_id][0]->user_name = $row->user_name ? $row->user_name : $row->af_user_ip;
			}
			$rv[$row->af_id][$row->afi_name] = $row;
		}

		return $rv;
	}

	private function getFilterCriteria( $filter, $filterValue = null ) {
		global $wgUser;
		$where = array();

		// Permissions check: these filters are for admins only.
		if (
			( $filter == 'invisible'
			 && !$wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) )
			||
			( $filter == 'deleted'
			 && !$wgUser->isAllowed( 'aftv5-see-deleted-feedback' ) )
		) {
			$filter = null;
		}

		// Don't let non-allowed users see these.
		if ( !$wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) ) {
			$where['af_is_hidden'] = 0;
		}

		// Don't let non-allowed users see these.
		if ( !$wgUser->isAllowed( 'aftv5-see-deleted-feedback' ) ) {
			$where['af_is_deleted'] = 0;
		}

		switch ( $filter ) {
			case 'needsoversight':
				$where[] = 'af_needs_oversight IS TRUE';
				break;
			case 'id':
				# Used for permalinks.
				$where['af_id'] = $filterValue;
				break;
			case 'visible':
				$where['af_is_deleted'] = 0;
				$where['af_is_hidden']  = 0;
				break;
			case 'invisible':
				$where[] = 'af_is_hidden > 0';
 				break;
			case 'abusive':
				$where[] = 'af_abuse_count > 0';
				break;
			case 'helpful':
				$where[] = 'CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) > 0';
				break;
			case 'unhelpful':
				$where[] = 'CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) < 0';
				break;
			case 'comment':
				$where[] = 'comment.aa_response_text IS NOT NULL';
				break;
			case 'deleted':
				$where[] = 'af_is_deleted > 0';
				break;
			default:
				break;
		}
		return $where;
	}

	protected function renderFeedback( $record ) {
		global $wgUser;
		$id = $record[0]->af_id;

		switch( $record[0]->af_bucket_id ) {
			case 1: $content .= $this->renderBucket1( $record ); break;
			case 2: $content .= $this->renderBucket2( $record ); break;
			case 3: $content .= $this->renderBucket3( $record ); break;
			case 4: $content .= $this->renderBucket4( $record ); break;
			case 5: $content .= $this->renderBucket5( $record ); break;
			case 6: $content .= $this->renderBucket6( $record ); break;
			default: $content .= $this->renderNoBucket( $record ); break;
		}

		// These two are the same for now, but may not always be,
		// so set them each separately.
		$can_flag   = !$wgUser->isBlocked();
		$can_vote   = !$wgUser->isBlocked();
		$can_hide   = $wgUser->isAllowed( 'aftv5-hide-feedback' );
		$can_delete = $wgUser->isAllowed( 'aftv5-delete-feedback' );

		$details = Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-comment-details-updates'
		) );
		if( $record[0]->age > 0 ) {
			$details .=  Linker::link(
				Title::newFromText( $record[0]->page_title ),
				wfMessage( 'articlefeedbackv5-updates-since',  $record[0]->age )->escaped(),
				array(),
				array(
					'action' => 'historysubmit',
					'diff'   => $record[0]->page_latest,
					'oldid'  => $record[0]->af_revision_id
				)
			);
		}
		$details .= Html::closeElement( 'div' );

		$footer_links = Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-vote-wrapper'
		) )
		. Html::openElement( 'div', array( 'class' => 'articleFeedbackv5-comment-foot' ) );

		if ( $can_vote ) {
			$footer_links .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-helpful-caption'
			), wfMessage( 'articlefeedbackv5-form-helpful-label', ( $record[0]->af_helpful_count + $record[0]->af_unhelpful_count ) ) )
			. Html::element( 'a', array(
				'id'    => "articleFeedbackv5-helpful-link-$id",
				'class' => 'articleFeedbackv5-helpful-link'
			), wfMessage( 'articlefeedbackv5-form-helpful-yes-label', $record[0]->af_helpful_count )->text() )
			. Html::element( 'a', array(
				'id'    => "articleFeedbackv5-unhelpful-link-$id",
				'class' => 'articleFeedbackv5-unhelpful-link'
			), wfMessage( 'articlefeedbackv5-form-helpful-no-label', $record[0]->af_unhelpful_count )->text() );
		}
		$footer_links .= Html::element( 'span', array(
			'class' => 'articleFeedbackv5-helpful-votes',
			'id'    => "articleFeedbackv5-helpful-votes-$id"
		), wfMessage( 'articlefeedbackv5-form-helpful-votes', $record[0]->af_helpful_count, $record[0]->af_unhelpful_count ) );
		$footer_links .= Html::closeElement( 'div' );
		if ( $can_flag ) {
			$aclass = 'articleFeedbackv5-abuse-link';
			global $wgArticleFeedbackv5AbusiveThreshold;
			if ( $record[0]->af_abuse_count >= $wgArticleFeedbackv5AbusiveThreshold ) {
				$aclass .= ' abusive';
			}
			// Count masked if user cannot hide comments (as per Fabrice)
			$msg = $can_hide ? 'articlefeedbackv5-form-abuse' : 'articlefeedbackv5-form-abuse-masked';
			$footer_links .= Html::element( 'a', array(
				'id'    => "articleFeedbackv5-abuse-link-$id",
				'class' => $aclass,
				'rel'   => $record[0]->af_abuse_count
			), wfMessage( $msg, $record[0]->af_abuse_count )->text() );
		}
		$footer_links .= $details . Html::closeElement( 'div' );

		/*$footer_links .= Html::element( 'span', array(
			'class' => 'articleFeedbackv5-helpful-votes'
		), wfMessage( 'articlefeedbackv5-form-helpful-votes', ( $record[0]->af_helpful_count + $record[0]->af_unhelpful_count ), $record[0]->af_helpful_count, $record[0]->af_unhelpful_count ) )
		. ( $can_flag ? Html::rawElement( 'div', array(
			'class' => 'articleFeedbackv5-abuse-link-wrap'
		), Html::element( 'a', array(
			'id'    => "articleFeedbackv5-abuse-link-$id",
			'class' => 'articleFeedbackv5-abuse-link'
		), wfMessage( 'articlefeedbackv5-form-abuse', $record[0]->af_abuse_count )->text() ) ) : '' )
		. Html::closeElement( 'div' );*/

		// Don't render the toolbox if they can't do anything with it.
		$tools = null;
		if ( $can_hide || $can_delete ) {
			$tools = Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-feedback-tools',
				'id'    => 'articleFeedbackv5-feedback-tools-' . $id
			) )
			. Html::element( 'h3', array(
				'id' => 'articleFeedbackv5-feedback-tools-header-' . $id
			), wfMessage( 'articlefeedbackv5-form-tools-label' )->text() )
			. Html::openElement( 'ul', array(
				'id' => 'articleFeedbackv5-feedback-tools-list-' . $id
			) );

			if ( $can_hide ) {
				if ( $record[0]->af_is_hidden > 0 ) {
					$msg = 'unhide';
				} else {
					$msg = 'hide';
				}
				$tools .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-hide-link-$id",
					'class' => "articleFeedbackv5-hide-link"
				), wfMessage( "articlefeedbackv5-form-" . $msg )->text() ) );
			}

			if ( $can_delete ) {
				if ( $record[0]->af_is_deleted > 0 ) {
					$msg = 'undelete';
				} else {
					$msg = 'delete';
				}
				$tools .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-delete-link-$id",
					'class' => "articleFeedbackv5-delete-link"
				), wfMessage( "articlefeedbackv5-form-" . $msg )->text() ) );
			}

/*
			$link = null;
			if ( $record[0]->af_needs_oversight ) {
				if ( $can_delete ) {
					$link = 'unoversight';
				} else {
					$link = 'oversighted';
				}
			} elseif ( $can_hide ) {
				# flag for oversight
				$link = 'oversight';
			}

			if ( $link ) {
				$tools .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-$link-link-$id",
					'class' => "articleFeedbackv5-$link-link"
				), wfMessage( "articlefeedbackv5-form-$link", $record[0]->af_is_deleted )->text() ) );
			}
*/

			$tools .= Html::closeElement( 'ul' )
			. Html::closeElement( 'div' );
		}

		$topClass = 'articleFeedbackv5-feedback';
		if ( $record[0]->af_is_hidden ) {
			$topClass .= ' articleFeedbackv5-feedback-hidden';
		}
		if ( $record[0]->af_is_deleted ) {
			$topClass .= ' articleFeedbackv5-feedback-deleted';
		}
		return Html::openElement( 'div', array(
			'class' => $topClass,
			'rel'   => $id
		) )
		. Html::openElement( 'div', array(
			'class' => "articleFeedbackv5-comment-wrap"
		) )
		. $content
		. $footer_links
		. Html::closeElement( 'div' )
		//. $details
		. $tools
		. Html::closeElement( 'div' );
	}

	private function renderPermalinkTimestamp( $record ) {
	    global $wgLang;
	    $id    = $record->af_id;
		$title = $record->page_title;

	    // Taken from the Moodbar extension.
	    $now       = wfTimestamp( TS_UNIX );
	    $timestamp = wfTimestamp( TS_UNIX, $record->af_created );
	    $date	   = '';

	    // Relative dates for 48 hours, normal timestamps later.
	    if ( $timestamp > ( $now - ( 86400 * 2 ) ) ) {
		    $time = $wgLang->formatTimePeriod(
			    ( $now - $timestamp ), 'avoidseconds'
		    );
		    $date = wfMessage( 'articleFeedbackv5-comment-ago', $time )->escaped();
	    } elseif( $timestamp ) {
		    $date = $wgLang->timeanddate($record->af_created  );
	    }

	    // format the element
	    return Html::openElement( 'span', array(
		    'class' => 'articleFeedbackv5-comment-details-date'
	    ) )
		. Linker::link(
			Title::newFromText(
				"Special:ArticleFeedbackv5/$title/$id"
			),
			$date
		)
	    . Html::closeElement( 'span' );
	}

	private function renderBucket1( $record ) {
		if ( $record['found']->aa_response_boolean ) {
			$msg   = 'articlefeedbackv5-form1-header-found';
			$class = 'positive';
		} else {
			$msg   = 'articlefeedbackv5-form1-header-not-found';
			$class = 'negative';
		}

		return $this->feedbackHead( $msg, $class, $record[0] )
		. $this->renderComment( $record['comment']->aa_response_text, $record[0]->af_id );
	}


	private function renderBucket2( $record ) {
		$type  = htmlspecialchars( $record['tag']->afo_name );
		$class = $type == 'problem' ? 'negative' : 'positive';
		// Document for grepping. Uses any of the messages:
		// * articlefeedbackv5-form2-header-praise
		// * articlefeedbackv5-form2-header-problem
		// * articlefeedbackv5-form2-header-question
		// * articlefeedbackv5-form2-header-suggestion
		return $this->feedbackHead( "articlefeedbackv5-form2-header-$type", $class, $record[0], $type )
		. $this->renderComment( $record['comment']->aa_response_text, $record[0]->af_id );
	}

	private function renderBucket3( $record ) {
		$name   = htmlspecialchars( $record[0]->user_name );
		$rating = htmlspecialchars( $record['rating']->aa_response_rating );
		$class  = $record['rating']->aa_response_rating >= 3 ? 'positive' : 'negative';

		return $this->feedbackHead( 'articlefeedbackv5-form3-header', $class, $record[0], $record['rating']->aa_response_rating )
		. $this->renderComment( $record['comment']->aa_response_text, $record[0]->af_id );
	}

	private function renderBucket4( $record ) {
		return $this->feedbackHead(
			'articlefeedbackv5-form4-header',
			'positive',
			$record[0]
		);
	}

	private function renderBucket5( $record ) {
		$name  = htmlspecialchars( $record[0]->user_name );
		$body  = '<ul>';
		$total = 0;
		$count = 0;
		foreach ( $record as $key => $answer ) {
			if ( $answer->afi_data_type == 'rating' && $key != '0' ) {
				$body .= "<li>"
				. htmlspecialchars( $answer->afi_name  )
				. ': '
				. htmlspecialchars( $answer->aa_response_rating )
				. "</li>";
				$total += $answer->aa_response_rating;
				$count++;
			}
		}
		$body .= "</ul>";

		$class = $total / $count >= 3 ? 'positive' : 'negative';

		$head = $this->feedbackHead(
			'articlefeedbackv5-form5-header',
			$class,
			$record[0]
		);

		return $head . $body;
	}

	private function renderBucket0( $record ) {
		# Future-proof this for when the bucket ID changes to 0.
		return $this->renderBucket6( $record );
	}

	private function renderNoBucket( $record ) {
		return $this->feedbackHead(
			'articlefeedbackv5-form-invalid',
			'negative',
			$record[0]
		);
	}

	private function renderBucket6( $record ) {
		return $this->feedbackHead(
			'articlefeedbackv5-form-not-shown',
			'positive',
			$record[0]
		);
	}

	private function renderComment( $text, $feedbackId ) {
		global $wgLang;

		$short = $wgLang->truncate( $text, 500 );

		$rv = Html::openElement( 'blockquote' )
		. Html::element( 'span',
			array(
				'class' => 'articleFeedbackv5-comment-short',
				'id'    => "articleFeedbackv5-comment-short-$feedbackId"
			),
			htmlspecialchars( $short )
		);

		// If the short string is the same size as the
		// original, no truncation happened, so no
		// controls are needed.
		if ( $short != $text ) {
			// Show the short text, with the 'show more' control.
			$rv .= Html::element( 'span',
				array(
					'class' => 'articleFeedbackv5-comment-full',
					'id'    => "articleFeedbackv5-comment-full-$feedbackId"
				),
				htmlspecialchars( $text )
			)
			. Html::element( 'a', array(
				'class' => 'articleFeedbackv5-comment-toggle',
				'id'    => "articleFeedbackv5-comment-toggle-$feedbackId"
			), wfMessage( 'articlefeedbackv5-comment-more' )->escaped() );
		}

		$rv .= Html::closeElement( 'blockquote' );

		return $rv;
	}

	private function feedbackHead( $message, $class, $record, $extra = '' ) {
		$name   = htmlspecialchars( $record->user_name );
		$link   = !$record->af_user_ip ? "User:$name" : "Special:Contributions/$name";
		$gender = $name;

		return Html::openElement( 'h3', array( 'class' => $class) )
		. Html::element( 'span', array( 'class' => 'icon' ) )
		. Linker::link( Title::newFromText( $link ), $name )
		. Html::element( 'span',
			array( 'class' => 'result' ),
			wfMessage( $message, $gender, $extra )->text()
		)
		. Html::closeElement( 'h3' )
		. $this->renderPermalinkTimestamp( $record );
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'pageid'        => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'sort'          => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'age', 'helpful', 'rating' )
			),
			'sortdirection' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'desc', 'asc' )
			),
			'filter'        => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'all', 'invisible', 'visible', 'comment', 'id', 'helpful', 'unhelpful', 'abusive', 'deleted', 'needsoversight' )
			),
			'filtervalue'   => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			),
			'limit'         => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'continue'      => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			),
		);
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		return array(
			'pageid'      => 'Page ID to get feedback ratings for',
			'sort'        => 'Key to sort records by',
			'filter'      => 'What filtering to apply to list',
			'filtervalue' => 'Optional param to pass to filter',
			'limit'       => 'Number of records to show',
			'continue'    => 'Offset from which to continue',
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'List article feedback for a specified page'
		);
	}

	/**
	 * Gets any possible errors
	 *
	 * @return array the errors
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	public function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedbackv5-view-feedback&afpageid=1',
		);
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id: ApiViewRatingsArticleFeedbackv5.php 103439 2011-11-17 03:19:01Z rsterbin $';
	}
}
