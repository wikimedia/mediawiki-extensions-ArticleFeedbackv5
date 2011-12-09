<?php
class SpecialArticleFeedbackv5 extends SpecialPage {
	public function __construct() {
		parent::__construct( 'ArticleFeedbackv5' );
	}

	public function execute( $param ) {
		global $wgOut;

		$title = Title::newFromText( $param );
		if ( $title ) {
			$pageId = $title->getArticleID();
		} else {
			$wgOut->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}

		$ratings = $this->fetchOverallRating( $pageId );
		$found   = isset( $ratings['found'] )  ? $ratings['found']  : null;
		$rating  = isset( $ratings['rating'] ) ? $ratings['rating'] : null;

		$wgOut->setPagetitle( "Feedback for $title" );

		if( !$pageId ) {
			$wgOut->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
		} else {
			$wgOut->addHTML(
				Linker::link(
					Title::newFromText( $param ),
					wfMessage( 'articlefeedbackv5-go-to-article' )->escaped()
				)
				.' | '.
				Linker::link(
					Title::newFromText( $param ),
					wfMessage( 'articlefeedbackv5-discussion-page' )->escaped()
				)
				.' | '.
				Linker::link(
					Title::newFromText( $param ),
					wfMessage( 'articlefeedbackv5-whats-this' )->escaped()
				)
			);
		}

		if( $found ) {
			$wgOut->addWikiMsg( 'articlefeedbackv5-percent-found', $found );
		}

		if( $rating ) {
			$wgOut->addWikiMsg( 'articlefeedbackv5-overall-rating', $rating);
		}

		$wgOut->addWikiMsg( 'articlefeedbackv5-special-title' );

		$showing = wfMessage(
			'articlefeedbackv5-special-showing',
			'<span id="aft5-feedback-count-shown">0</span>',
			'<span id="aft5-feedback-count-total">0</span>'
		);

		$wgOut->addHTML('
<script> var hackPageId = '.$pageId.'; </script>
<script src="/extensions/ArticleFeedbackv5/modules/jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.js"></script>'
.wfMessage('articlefeedbackv5-special-filter-label-before')->escaped()
.'<select id="aft5-filter">
	<option value="visible">'.wfMessage( 'articlefeedbackv5-special-filter-visible' )->escaped().'</option>
	<option value="invisible">'.wfMessage( 'articlefeedbackv5-special-filter-invisible' )->escaped().'</option>
	<option value="all">'.wfMessage( 'articlefeedbackv5-special-filter-all' )->escaped().'</option>
</select>'
.wfMessage('articlefeedbackv5-special-filter-label-after')->escaped()
.' | '
.wfMessage('articlefeedbackv5-special-sort-label-before')->escaped()
.'<select id="aft5-sort">
	<option value="newest">'.wfMessage( 'articlefeedbackv5-special-sort-newest' )->escaped().'</option>
	<option value="oldest">'.wfMessage( 'articlefeedbackv5-special-sort-oldest' )->escaped().'</option>
</select>'
.wfMessage('articlefeedbackv5-special-sort-label-after')->escaped()
.'<br>
<span id="aft5-showing"> '.$showing.' </span>
<br>
<div style="border:1px solid red;" id="aft5-show-feedback"></div>
<a href="#" id="aft5-show-more">'
.wfMessage( 'articlefeedbackv5-special-more' )->escaped()
.'</a>
		');
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


	protected static function formatNumber( $number ) {
		global $wgLang;
		return $wgLang->formatNum( number_format( $number, 2 ) );
	}
}
