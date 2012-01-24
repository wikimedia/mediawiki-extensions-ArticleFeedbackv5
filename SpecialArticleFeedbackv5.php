<?php
/**
 * SpecialArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Special
 * @author     Greg Chiasson <gchiasson@omniti.com>
 * @version    $Id$
 */

/**
 * This is the Special page the shows the feedback dashboard
 *
 * @package    ArticleFeedback
 * @subpackage Special
 */
class SpecialArticleFeedbackv5 extends SpecialPage {
	private $filters = array( 
		'comment',
		'visible'
	);
	private $sorts = array( 
		'age', 
		'helpful', 
		'rating'
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wgUser;
		parent::__construct( 'ArticleFeedbackv5' );

		if( $wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) ) {
			$this->filters[] = 'invisible';
			$this->filters[] = 'all';
		}

		if( $wgUser->isAllowed( 'aftv5-see-deleted-feedback' ) ) {
			$this->filters[] = 'deleted';
		}
	}

	/**
	 * Executes the special page
	 *
	 * @param $param string the parameter passed in the url
	 */
	public function execute( $param ) {
		global $wgArticleFeedbackv5DashboardCategory;
		$out   = $this->getOutput();
		$title = Title::newFromText( $param );

		// Page does not exist.
		if( !$title->exists() ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}

		$pageId = $title->getArticleID();
		$dbr    = wfGetDB( DB_SLAVE );
		$t      = $dbr->select( 
			'categorylinks', 
			'cl_from', 
			array( 
				'cl_from' => $pageId,
				'cl_to'   => $wgArticleFeedbackv5DashboardCategory 
			),
			__METHOD__, 
			array( 'LIMIT' => 1 ) 
		);

		// Page exists, but feedback is disabled.
		if( $dbr->numRows( $t ) == 0 ) {
			$out->addWikiMsg( 'articlefeedbackv5-page-disabled' );
			return;
		}

		// Success!
		$ratings = $this->fetchOverallRating( $pageId );
		$found   = isset( $ratings['found'] ) ? $ratings['found'] : null;
		$rating  = isset( $ratings['rating'] ) ? $ratings['rating'] : null;

		$out->setPagetitle(
			$this->msg( 'articlefeedbackv5-special-pagetitle', $title )->escaped()
		);

		if ( !$pageId ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}

		# TODO: Fix links.
		$out->addHTML(
			Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-header-wrap' )
			)
			. Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-header-links' )
			)
			. Linker::link(
				Title::newFromText( $param ),
				$this->msg( 'articlefeedbackv5-go-to-article' )->escaped()
			)
			. ' | ' .
			Linker::link(
				Title::newFromText( $param ),
				$this->msg( 'articlefeedbackv5-discussion-page' )->escaped()
			)
			. ' | ' .
			Linker::link(
				Title::newFromText( $param ),
				$this->msg( 'articlefeedbackv5-whats-this' )->escaped()
			)
			. Html::closeElement( 'div' )
			. Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-showing-count-wrap' )
			)
			. $this->msg(
				'articlefeedbackv5-special-showing',
				Html::element( 'span', array( 'id' => 'articleFeedbackv5-feedback-count-total' ), '0' )
			)
			. Html::closeElement( 'div' )
		);

		if ( $found ) {
			$out->addHtml(
				Html::openElement(
					'div',
					array( 'id' => 'articleFeedbackv5-percent-found-wrap' )
				)
				. $this->msg( 'articlefeedbackv5-percent-found', $found ) # Can't escape this, need the <span> tag to parse.
				. Html::closeElement( 'div' )
			);
		}
		
		$out->addHtml(
			Html::element(
				'a',
				array(
					'href'  => '#',
					'id'    => 'articleFeedbackv5-special-add-feedback',
				),
				$this->msg( 'articlefeedbackv5-special-add-feedback' )->text()
       	                )
			. Html::element( 'div', array( 'class' => 'float-clear' ) )
			. Html::closeElement( 'div' )
		);

#		if ( $rating ) {
#			$out->addWikiMsg( 'articlefeedbackv5-overall-rating', $rating );
#		}


		$out->addJsConfigVars( 'afPageId', $pageId );
		$out->addModules( 'jquery.articleFeedbackv5.special' );

		$sortLabels = array();
		foreach ( $this->sorts as $sort ) {
			$sortLabels[] = Html::element( 'img',
				array(
					'id'    => 'articleFeedbackv5-sort-arrow-' . $sort,
					'class' => 'articleFeedbackv5-sort-arrow'
				), '' )
				. Html::element(
				'a',
				array(
					'href'  => '#',
					'id'    => 'articleFeedbackv5-special-sort-' . $sort,
					'class' => 'articleFeedbackv5-sort-link'
				),
				$this->msg( 'articlefeedbackv5-special-sort-' . $sort )->text()
			);
		}

		$opts   = array();
		$counts = $this->getFilterCounts( $pageId );
		foreach( $this->filters as $filter ) {
			$count = isset($counts[$filter]) ? $counts[$filter] : 0;
			$key = $this->msg( 'articlefeedbackv5-special-filter-'.$filter, $count )->escaped();
			$opts[ (string) $key ] = $filter;
		}

		$filterSelect = new XmlSelect( false, 'articleFeedbackv5-filter-select' );
		$filterSelect->addOptions( $opts );

		$out->addHTML(
			Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-sort-filter-controls' )
			)
			. Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-sort' )
			)
			. Html::openElement(
				'span',
				array( 'class' => 'articleFeedbackv5-sort-label' )
			)
			. $this->msg( 'articlefeedbackv5-special-sort-label-before' )->escaped()
			. Html::closeElement( 'span' )
			. implode( $this->msg( 'pipe-separator' )->escaped(), $sortLabels )

			. $this->msg( 'articlefeedbackv5-special-sort-label-after' )->escaped()
			. Html::closeElement( 'div' )
			. Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-filter' )
			)
			. Html::openElement(
				'span',
				array( 'class' => 'articleFeedbackv5-filter-label' )
			)
			. $this->msg( 'articlefeedbackv5-special-filter-label-before' )->escaped()
			. Html::closeElement( 'span' )
			. $filterSelect->getHTML()
			. $this->msg( 'articlefeedbackv5-special-filter-label-after' )->escaped()
			. Html::closeElement( 'div' )


			. Html::closeElement( 'div' )
		);

		$out->addHTML(
			Html::element(
				'div',
				array(
					'id'    => 'articleFeedbackv5-show-feedback',
				)
			)
			. Html::element(
				'a',
				array(
					'href' => '#',
					'id'   => 'articleFeedbackv5-show-more'
				),
				$this->msg( 'articlefeedbackv5-special-more' )->text()
			)
		);
	}

	/**
	 * Takes an associative array of label to value and converts the message
	 * names into localized strings
	 *
	 * @param  $options array the options, indexed by label
	 * @return array    the options, indexed by localized and escaped text
	 */
	private function selectMsg( array $options ) {
		$newOpts = array();
		foreach ( $options as $label => $value ) {
			$newOpts[$this->msg( $label )->escaped()] = $value;
		}
		return $newOpts;
	}

	/**
	 * Grabs the overall rating for a page
	 *
	 * @param  $pageId int the page id
	 * @return array   the overall rating, as array (found => %, rating => avg)
	 */
	private function fetchOverallRating( $pageId ) {
		$rv = array();
		$dbr = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			array(
				'aft_article_feedback_ratings_rollup',
				'aft_article_field'
			),
			array(
				'arr_total / arr_count AS rating',
				'afi_name'
			),
			array(
				'arr_page_id' => $pageId,
				'arr_field_id = afi_id',
				"afi_name IN ('found', 'rating')"
			)
		);

		foreach ( $rows as $row ) {
			if ( $row->afi_name == 'found' ) {
				$rv['found'] = ( int ) ( 100 * $row->rating );
			} elseif ( $row->afi_name == 'rating' ) {
				$rv['rating'] = ( int ) $row->rating;
			}
		}

		return $rv;
	}

	private function getFilterCounts( $pageId ) {
		$rv   = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			'aft_article_filter_count',
			array(
				'afc_filter_name',
				'afc_filter_count'
			),
			array(
				'afc_page_id' => $pageId
			),
			array(),
			__METHOD__
		);

		foreach( $rows as $row ) {
			$rv[ $row->afc_filter_name ] = $row->afc_filter_count;
		}

		return $rv;
	}
}

