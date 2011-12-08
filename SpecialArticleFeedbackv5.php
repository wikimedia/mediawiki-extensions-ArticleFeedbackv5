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

		$wgOut->addHTML(<<<EOH
<script> var hackPageId = $pageId; </script>
<script src="/extensions/ArticleFeedbackv5/modules/jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.js"></script>
<!--
Show only: <select id="aft5-filter">
<option>visible</option>
<option>all</option>
</select>
<br/>
Sort:
<select id="aft5-sort">
<option>newest</option>
<option>oldest</option>
</select>
-->
<br>
<span id="aft5-showing">
Showing <span id="aft5-feedback-count-shown">0</span> posts (of <span id="aft5-feedback-count-total">0</span>)
</span>
<br>
<div style="border:1px solid red;" id="aft5-show-feedback"></div>
<a href="#" id="aft5-show-more">More</a>
EOH
		);
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
