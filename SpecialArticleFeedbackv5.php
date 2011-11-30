<?php
class SpecialArticleFeedbackv5 extends SpecialPage {
	private $api;

	public function __construct() {
		parent::__construct( 'ArticleFeedbackv5' );
	}

	public function execute( $title ) {
		global $wgOut;
		$pageId    = $this->pageIdFromTitle( $title );

		if( !$pageId ) { 
			# Probably this is bad.
			print "That ain't no good kind of page.";
			die();
		}

		$wgOut->setPagetitle( "Feedback for $title" );
#		$wgOut->addModules( 'jquery.articleFeedbackv5.special' );

		$this->api = $this->getApi();
		$output    = '';
		$ratings   = $this->api->fetchOverallRating( $pageId );
		$found     = isset( $ratings['found'] )  ? $ratings['found']  : null;
		$rating    = isset( $ratings['rating'] ) ? $ratings['rating'] : null;

		$output .= "[[Wikipedia:$title|Go to Article]]
			| [[Wikipedia:$title|Discussion page]]
			| [[Wikipedia:$title|What's this?]]\n";

		if( $found ) {
			$output .= " $found% of users found what they were looking for. ";
		}

		if( $rating ) {
			$output .= " Rating: $rating/5 ";
		}

		$output .= "\n== Feedback ==\n";

		$wgOut->addWikiText( $output );

		$wgOut->addHTML(<<<EOH
<!-- This is a terrible, terrible hack. I'm taking it out as soon as I stop
     being an idiot and sort this ResourceLoader thing out -->
<script> var hackPageId = $pageId; </script>
<script src="http://wikidev.int.omniti.net/extensions/ArticleFeedbackv5/modules/jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.js"></script>
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
Showing 0 posts (of 0)
</span>
<br>
<div style="border:1px solid red;" id="aft5-show-feedback">Loading...</div>
<a href="#" id="aft5-more">More</a>
EOH
		);
		# "more" link to load the next 50 and append it.
	}

	protected static function formatNumber( $number ) {
		global $wgLang;
		return $wgLang->formatNum( number_format( $number, 2 ) );
	}

	protected function pageIdFromTitle( $title ) {
		$dbr = wfGetDB( DB_SLAVE );
		return $dbr->selectField(
			'page',
			'page_id',
			array( 'page_title' => $title )
		);
	}

	private function getApi() {
		$q   = new ApiQuery(
		 'ApiQuery', 'articlefeedbackv5-view-feedback' );
		$api = new ApiViewFeedbackArticleFeedbackv5( 
		 $q, 'articlefeedbackv5-view-feedback' );
		return $api;
	}
}
