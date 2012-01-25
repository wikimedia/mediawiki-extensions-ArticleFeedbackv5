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

	public function fetchFeedback( $pageId,
	 $filter = 'visible', $filterValue = null, $sort = 'age', $sortOrder = 'desc', $limit = 25, $continue = null ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$ids   = array();
		$rows  = array();
		$rv    = array();
		$where = $this->getFilterCriteria( $filter, $filterValue );

		$direction         = strtolower( $sortOrder ) == 'asc' ? 'ASC' : 'DESC';
		$continueDirection = ( $direction == 'ASC' ? '>' : '<' );
		$order;
		$continueSql;
		$sortField;

		switch( $sort ) {
			case 'helpful':
				$sortField   = 'net_helpfulness';
				// Can't use aliases in mysql where clauses.
				$continueSql = "CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) $continueDirection";
				break;
			case 'rating':
# disable because it's broken
#				$sortField = 'rating';
#				break;
			case 'age':
				# Default field, fall through	
			default:
				$sortField   = 'af_id';
				$continueSql = "$sortField $continueDirection";
				break;
		}	
		$order       = "$sortField $direction";

		$where['af_page_id'] = $pageId;

		# This join is needed for the comment filter.
		$where[] = 'af_id = aa_feedback_id';

		if( $continue !== null ) {
			$where[] = "$continueSql $continue";
		}

		/* I'd really love to do this in one big query, but MySQL
		   doesn't support LIMIT inside IN() subselects, and since
		   we don't know the number of answers for each feedback
		   record until we fetch them, this is the only way to make
		   sure we get all answers for the exact IDs we want. */
		$id_query = $dbr->select(
			array(
				'aft_article_feedback', 
				'aft_article_answer'
			),
			array(
				'DISTINCT af_id', 
				'CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) AS net_helpfulness'
			),
			$where, 
			__METHOD__,
			array(
				'LIMIT'    => $limit,
				'ORDER BY' => $order
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
			array( 'aft_article_feedback', 'aft_article_answer',
				'aft_article_field', 'aft_article_field_option',
				'user', 'page'
			),
			array( 'af_id', 'af_bucket_id', 'afi_name', 'afo_name',
				'aa_response_text', 'aa_response_boolean',
				'aa_response_rating', 'aa_response_option_id',
				'afi_data_type', 'af_created', 'user_name',
				'af_user_ip', 'af_hide_count', 'af_abuse_count',
				'af_helpful_count', 'af_unhelpful_count', 
				'af_delete_count', 'af_needs_oversight',
				'(SELECT COUNT(*) FROM revision WHERE rev_id > af_revision_id AND rev_page = '.( integer ) $pageId.') AS age', 
				'CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED) AS net_helpfulness',
				'page_latest', 'af_revision_id', 'page_title'
			),
			array( 'af_id' => $ids ),
			__METHOD__,
			array( 'ORDER BY' => $order ),
			array(
				'page'                     => array(
					'JOIN', 'page_id = af_page_id'
				),
				'user'                     => array(
					'LEFT JOIN', 'user_id = af_user_id'
				),
				'aft_article_field'        => array(
					'LEFT JOIN', 'afi_id = aa_field_id'
				),
				'aft_article_answer'       => array(
					'LEFT JOIN', 'af_id = aa_feedback_id'
				),
				'aft_article_field_option' => array(
					'LEFT JOIN',
					'aa_response_option_id = afo_option_id'
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
		if(
			( $filter == 'invisible' 
			 && !$wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) )
			|| 
			( $filter == 'deleted' 
			 && !$wgUser->isAllowed( 'aftv5-see-deleted-feedback' ) )
		) {
			$filter = null;
		}

		// Don't let non-allowed users see these.
		if( !$wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) ) {
			$where['af_hide_count'] = 0;
		}

		// Don't let non-allowed users see these.
		if( !$wgUser->isAllowed( 'aftv5-see-deleted-feedback' ) ) {
			$where['af_delete_count'] = 0;
		}

		switch( $filter ) {
			case 'needsoversight':
				$where[] = 'af_needs_oversight IS TRUE';
				break;
			case 'id':
				# Used for permalinks.
				$where['af_id'] = $filterValue;
				break;
			case 'all':
				# relies on the above to handler filtering,
				# because 'all' doesn't mean thhe same thing
				# at all access levels.
				# oversight, real 'all' - no filtering done
				# non-oversight 'all' - no deleted feedback
				# non-moderator 'all' - no hidden/deleted
				break;
			case 'visible':
				# For oversights/sysops to see what normal
				# people see.
				$where['af_delete_count'] = 0;
				$where['af_hide_count']   = 0;
				break;
			case 'invisible':
				$where[] = 'af_hide_count > 0';
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
				$where[] = 'aa_response_text IS NOT NULL';
				break;
			case 'deleted':
				$where[] = 'af_delete_count > 0';
				break;
			default:
				break;
		}
		return $where;
	}

	protected function renderFeedback( $record ) {
		global $wgArticlePath, $wgUser;
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

		// These two are the same for now, but may now always be,
		// so set them each separately.
		$can_flag   = !$wgUser->isBlocked();
		$can_vote   = !$wgUser->isBlocked();
		$can_hide   = $wgUser->isAllowed( 'aftv5-hide-feedback' );
		$can_delete = $wgUser->isAllowed( 'aftv5-delete-feedback' );

		$details = Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-comment-details'
		) )
		. Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-comment-details-date'
		) ) 
		.Html::element( 'a', array(
			'href' => "#id=$id"
		), date( 'M j, Y H:i', strtotime($record[0]->af_created) ) )
		. Html::closeElement( 'div' )
# Remove for now, pending feedback.
#		. Html::openElement( 'div', array(
#			'class' => 'articleFeedbackv5-comment-details-permalink'
#		) )
#		.Html::element( 'a', array(
#			'href' => "#id=$id"
#		), wfMessage( 'articlefeedbackv5-comment-link' ) )
#		. Html::closeElement( 'div' )

		. Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-comment-details-updates'
		) ) 
		. Linker::link(
			Title::newFromText( $record[0]->page_title ),
			wfMessage( 'articlefeedbackv5-updates-since',  $record[0]->age ), 
			array(),
			array(
				'action' => 'historysubmit',
				'diff'   => $record[0]->page_latest,
				'oldid'  => $record[0]->af_revision_id
			)
		)
		. Html::closeElement( 'div' )
		. Html::closeElement( 'div' );
;

		$footer_links = Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-vote-wrapper'
		) )
		. Html::openElement( 'p', array( 'class' => 'articleFeedbackv5-comment-foot' ) );

		if( $can_vote ) {
			$footer_links .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-helpful-caption'
			), wfMessage( 'articlefeedbackv5-form-helpful-label', ( $record[0]->af_helpful_count + $record[0]->af_unhelpful_count ) ) )
			. Html::element( 'a', array(
				'id'    => "articleFeedbackv5-helpful-link-$id",
				'class' => 'articleFeedbackv5-helpful-link'
			), wfMessage( 'articlefeedbackv5-form-helpful-yes-label', $record[0]->af_helpful_count )->text() )
			.Html::element( 'a', array(
				'id'    => "articleFeedbackv5-unhelpful-link-$id",
				'class' => 'articleFeedbackv5-unhelpful-link'
			), wfMessage( 'articlefeedbackv5-form-helpful-no-label', $record[0]->af_unhelpful_count )->text() );
		}
		$footer_links .= Html::element( 'span', array(
			'class' => 'articleFeedbackv5-helpful-votes',
			'id'    => "articleFeedbackv5-helpful-votes-$id"
		), wfMessage( 'articlefeedbackv5-form-helpful-votes', ( $record[0]->af_helpful_count + $record[0]->af_unhelpful_count ), $record[0]->af_helpful_count, $record[0]->af_unhelpful_count ) );
		if( $can_flag ) {
			$footer_links .= Html::element( 'a', array(
				'id'    => "articleFeedbackv5-abuse-link-$id",
				'class' => 'articleFeedbackv5-abuse-link'
			), wfMessage( 'articlefeedbackv5-form-abuse', $record[0]->af_abuse_count )->text() );
		}
		$footer_links .= Html::closeElement( 'p' )
		. Html::closeelement( 'div' );

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
		if( $can_hide || $can_delete ) { 
			$tools = Html::openElement( 'div', array( 
				'class' => 'articleFeedbackv5-feedback-tools',
				'id'    => 'articleFeedbackv5-feedback-tools-'.$id
			) )
			. Html::element( 'h3', array(
				'id' => 'articleFeedbackv5-feedback-tools-header-'.$id
			), wfMessage( 'articlefeedbackv5-form-tools-label' )->text() )
			. Html::openElement( 'ul', array(
				'id' => 'articleFeedbackv5-feedback-tools-list-'.$id
			) );


			if( $can_hide ) { 
				$link = 'hide';
				if( $record[0]->af_hide_count > 0 ) {
					# unhide
					$link = 'unhide';
					$tools .= Html::element( 'li', array(), wfMessage( 'articlefeedbackv5-hidden' ) );
				}
				$tools .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-$link-link-$id",
					'class' => "articleFeedbackv5-$link-link"
				), wfMessage( "articlefeedbackv5-form-$link", $record[0]->af_hide_count )->text() ) );
			}

			if( $can_delete ) {
				# delete
				$link = 'delete';
				if( $record[0]->af_delete_count > 0 ) {
					$link = 'undelete';
				}
				$tools .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-$link-link-$id",
					'class' => "articleFeedbackv5-$link-link"
					), wfMessage( "articlefeedbackv5-form-$link", $record[0]->af_delete_count )->text() ) );
			}

			$link = null;
			if( $record[0]->af_needs_oversight ) {
				if( $can_delete ) {
					$link = 'unoversight';
				} else {
					$link = 'oversighted';
				}
			} elseif( $can_hide ) { 
				# flag for oversight
				$link = 'oversight';
			}

			if( $link ) {
				$tools .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-$link-link-$id",
					'class' => "articleFeedbackv5-$link-link"
				), wfMessage( "articlefeedbackv5-form-$link", $record[0]->af_delete_count )->text() ) );
			}

			$tools .= Html::closeElement( 'ul' )
			. Html::closeElement( 'div' );
		}

		return Html::openElement( 'div', array( 'class' => 'articleFeedbackv5-feedback' ) )
		. Html::openElement( 'div', array(
			'class' => "articleFeedbackv5-comment-wrap"
		) )
		. $content
		. $footer_links
		. Html::closeElement( 'div' )
		. $details
		. $tools
		. Html::closeElement( 'div' );
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
		. $this->renderComment( $record['comment']->aa_response_text );
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
		. $this->renderComment( $record['comment']->aa_response_text );
	}

	private function renderBucket3( $record ) {
		$name   = htmlspecialchars( $record[0]->user_name );
		$rating = htmlspecialchars( $record['rating']->aa_response_rating );
		$class  = $record['rating']->aa_response_rating >= 3 ? 'positive' : 'negative';

		return $this->feedbackHead( 'articlefeedbackv5-form3-header', $class, $record[0], $record['rating']->aa_response_rating )
		. $this->renderComment( $record['comment']->aa_response_text );
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

		return $head.$body;
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

	private function renderComment( $text ) {
		if( strlen( $text ) <= 500 ) { 
			$rv = "<blockquote>"
			. htmlspecialchars( $text )
			. '</blockquote>';
		} else {
			$rv = "<blockquote>"
			. htmlspecialchars( $text )
			. '</blockquote>';
		}
		return $rv;
	}

	private function feedbackHead( $message, $class, $record, $extra = '' ) {
		$gender = ''; #?
		$name   = htmlspecialchars( $record->user_name );
		$link   = $record->af_user_id ? "User:$name" : "Special:Contributions/$name";

		return Html::openElement( 'h3', array( 
			'class' => $class
		) )
                . Linker::link( Title::newFromText( $link ), $name )
		. Html::element( 'span', array( 'class' => 'icon' ) )
		. Html::element( 'span', 
			array( 'class' => 'result' ), 
			wfMessage( $message, $gender, $extra )->escaped() 
		)
		. Html::closeElement( 'h3' );
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
