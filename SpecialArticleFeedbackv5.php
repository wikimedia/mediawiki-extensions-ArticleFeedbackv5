<?php
class SpecialArticleFeedbackv5 extends SpecialPage {
	public function __construct() {
		parent::__construct( 'ArticleFeedbackv5' );
	}

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
		$found   = isset( $ratings['found'] )  ? $ratings['found']  : null;
		$rating  = isset( $ratings['rating'] ) ? $ratings['rating'] : null;

		$out->setPagetitle( "Feedback for $title" );

		if( !$pageId ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
		} else {
			$out->addHTML(
				Linker::link(
					Title::newFromText( $param ),
					$this->msg( 'articlefeedbackv5-go-to-article' )->escaped()
				)
				.' | '.
				Linker::link(
					Title::newFromText( $param ),
					$this->msg( 'articlefeedbackv5-discussion-page' )->escaped()
				)
				.' | '.
				Linker::link(
					Title::newFromText( $param ),
					$this->msg( 'articlefeedbackv5-whats-this' )->escaped()
				)
			);
		}

		if( $found ) {
			$out->addWikiMsg( 'articlefeedbackv5-percent-found', $found );
		}

		if( $rating ) {
			$out->addWikiMsg( 'articlefeedbackv5-overall-rating', $rating);
		}

		$out->addWikiMsg( 'articlefeedbackv5-special-title' );

		$showing = $this->msg(
			'articlefeedbackv5-special-showing',
			Html::element( 'span', array( 'id' => 'aft-feedback-count-shown' ), '0'),
			Html::element( 'span', array( 'id' => 'aft-feedback-count-total' ), '0')
		);

		$out->addJsConfigVars( 'afPageId', $pageId );
		$out->addModules( 'jquery.articleFeedbackv5.special' );

		$filterSelect = new XmlSelect( false, 'aft5-filter' );
		$filterSelect->addOptions( $this->selectMsg( array(
			'articlefeedbackv5-special-filter-visible' => 'visible',
			'articlefeedbackv5-special-filter-invisible' => 'invisible',
			'articlefeedbackv5-special-filter-all' => 'all',
		) ) );

		$sortSelect = new XmlSelect( false, 'aft5-sort' );
		$sortSelect->addOptions( $this->selectMsg( array(
			'articlefeedbackv5-special-sort-newest' => 'newest',
			'articlefeedbackv5-special-sort-oldest' => 'oldest',
		) ) );

		$out->addHTML($this->msg('articlefeedbackv5-special-filter-label-before')->escaped()
			. $filterSelect->getHTML()
			. $this->msg('articlefeedbackv5-special-filter-label-after')->escaped()
			. ' | '
			. $this->msg('articlefeedbackv5-special-sort-label-before')->escaped()
			. $sortSelect->getHTML()
			. $this->msg('articlefeedbackv5-special-sort-label-after')->escaped()
			. Html::element( 'span', array( 'id' => 'aft-showing' ), $showing )
			. Html::element( 'div', array( 'id' => 'aft5-show-feedback',
					'style' => 'border:1px solid red;' ), '' )
			. Html::element( 'a', array( 'href' => '#', 'id' => 'aft5-show-more' ),
					$this->msg( 'articlefeedbackv5-special-more' )->escaped() )
		);
	}

	/**
	 * Takes an associative array of label to value and converts the message
	 * names into localized strings
	 *
	 * @param array $options
	 * @return array
	 */
	private function selectMsg( array $options ) {
		$newOpts = array();
		foreach ( $options as $label => $value ) {
			$newOpts[$this->msg( $label )->escaped()] = $value;
		}

		return $newOpts;
	}

	private function fetchOverallRating( $pageId ) {
		$rv   = array();
		$dbr  = wfGetDB( DB_SLAVE );
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

		foreach( $rows as $row ) {
			if( $row->afi_name == 'found' ) {
				$rv['found']  = ( int ) ( 100 * $row->rating );
			} elseif( $row->afi_name == 'rating' ) {
				$rv['rating'] = ( int ) $row->rating;
			}
		}

		return $rv;
	}
}
