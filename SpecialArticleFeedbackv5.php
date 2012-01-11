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

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'ArticleFeedbackv5' );
	}

	/**
	 * Executes the special page
	 *
	 * @param $param string the parameter passed in the url
	 */
	public function execute( $param ) {
		$out = $this->getOutput();
		$title = Title::newFromText( $param );
		if ( $title ) {
			$pageId = $title->getArticleID();
		} else {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}

		$ratings = $this->fetchOverallRating( $pageId );
		$found = isset( $ratings['found'] ) ? $ratings['found'] : null;
		$rating = isset( $ratings['rating'] ) ? $ratings['rating'] : null;

		$out->setPagetitle(
			$this->msg( 'articlefeedbackv5-special-pagetitle', $title )->escaped()
		);

		if ( !$pageId ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
		} else {
			# TODO: Fix links.
			$out->addHTML(
				Html::openElement(
					'div', 
					array( 'id' => 'aft5-header-links' )
				)
				.Linker::link(
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
				.Html::closeElement( 'div' )
			);
		}

		$out->addHTML( 
			Html::openElement(
				'div', 
				array( 'id' => 'aft5-showing-count-wrap' )
			)
			.$this->msg(
				'articlefeedbackv5-special-showing',
				Html::element( 'span', array( 'id' => 'aft-feedback-count-total' ), '0' )
			)
			.Html::closeElement( 'div' )
		);

		if ( $found ) {
			$out->addHtml(
				Html::openElement(
					'div', 
					array( 'id' => 'aft5-percent-found-wrap' )
				)
				.$this->msg( 'articlefeedbackv5-percent-found', $found )->escaped()
				.Html::closeElement( 'div' )
			);
		}

#		if ( $rating ) {
#			$out->addWikiMsg( 'articlefeedbackv5-overall-rating', $rating );
#		}

		$out->addWikiMsg( 'articlefeedbackv5-special-title' );

		$out->addJsConfigVars( 'afPageId', $pageId );
		$out->addModules( 'jquery.articleFeedbackv5.special' );


		$sortLabels = array();
		$sortOpts   = array( 'newest', 'oldest' );
		foreach( $sortOpts as $sort ) {
			$sortLabels[] = Html::element( 
				'a',
				array(
					'href'  => '#',
					'id'    => 'articlefeedbackv5-special-sort-'.$sort,
					'class' => 'aft5-sort-link' 
				),
				$this->msg( 'articlefeedbackv5-special-sort-'.$sort )->text()
			);
		}

		$filterSelect = new XmlSelect( false, 'aft5-filter' );
		$filterSelect->addOptions( $this->selectMsg( array(
			'articlefeedbackv5-special-filter-visible'   => 'visible',
			'articlefeedbackv5-special-filter-invisible' => 'invisible',
			'articlefeedbackv5-special-filter-all'       => 'all',
		)));

		$out->addHTML(
			Html::openElement(
				'div', 
				array( 'id' => 'aft5-sort-filter-controls' )
			)
			.$this->msg( 'articlefeedbackv5-special-sort-label-before' )->escaped()
			.implode( $this->msg( 'pipe-separator' )->escaped(), $sortLabels )
			.$this->msg( 'articlefeedbackv5-special-sort-label-after' )->escaped()

			.$this->msg( 'articlefeedbackv5-special-filter-label-before' )->escaped()
			.$filterSelect->getHTML()
			.$this->msg( 'articlefeedbackv5-special-filter-label-after' )->escaped()
			.Html::element( 
				'a',
				array(
					'href'  => '#',
					'id'    => 'articlefeedbackv5-special-add-feedback',
				),
				$this->msg( 'articlefeedbackv5-special-add-feedback' )->text()
                        )
			.Html::closeElement( 'div' )
		);

		$out->addHTML(
			Html::element(
				'div', 
				array(
					'id'    => 'aft5-show-feedback',
					'style' => 'border:1px solid red;'
				), ''
			)
			.Html::element( 
				'a', 
				array( 
					'href' => '#', 
					'id'   => 'aft5-show-more' 
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

}

