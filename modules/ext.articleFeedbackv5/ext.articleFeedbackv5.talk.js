/*
 * Script for Article Feedback Extension on Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	var ua = navigator.userAgent.toLowerCase();
	// Rule out MSIE 6/7/8, iPhone, iPod, iPad, Android
	if(
		(ua.indexOf( 'msie 6' ) != -1) ||
		(ua.indexOf( 'msie 7' ) != -1) ||
		(ua.indexOf( 'msie 8' ) != -1) ||
		(ua.indexOf( 'firefox/2') != -1) ||
		(ua.indexOf( 'firefox 2') != -1) ||
		(ua.indexOf( 'android' ) != -1) ||
		(ua.indexOf( 'iphone' ) != -1) ||
		(ua.indexOf( 'ipod' ) != -1 ) ||
		(ua.indexOf( 'ipad' ) != -1)
	) {
		return;
	}

	// Check if the talk page link can be shown
	if ( mw.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {

		// Is this page enabled?
		var enable = false;
		var whitelist = mw.config.get( 'aftv5Whitelist', -1 );
		if ( whitelist == -1 ) {
			// The html is cached, so always on (we know it's whitelisted)
			enable = true;
		} else if ( whitelist ) {
			// It's whitelisted, so always on
			enable = true;
		} else {
			// It's a lottery article, so test that
			var pageId = mw.config.get( 'aftv5PageId', -1 );
			if ( pageId < 1 ) {
				// Oops, we don't have that information after all
				enable = false;
			} else {
				// Lottery inclusion (inverse of AFTv4, if we have a related article id)
				var v4odds = mw.config.get( 'wgArticleFeedbackLotteryOdds', 0 );
				enable = pageId == 0 || !( ( Number( pageId ) % 1000 )
					< Number( v4odds ) * 10 );
			}
		}
		if ( !enable ) {
			return;
		}

		// Initialize clicktracking
		// NB: Using the talk page's namespace, title, and rev id, not
		// the article's as in the front end tracking
		$.aftTrack.init();

		// Build the url to the Special:ArticleFeedbackv5 page
		var params = { ref: 'talk' };
		var track_id = 'talk_page_view_feedback-button_click';
		var url = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
			mw.util.wikiUrlencode( mw.config.get( 'wgTitle' ) ) +
			'?' + $.param( params );

		// Add the link to the feedback-page next to the title
		var link = $( '<a id="articleFeedbackv5-talk-view-feedback"></a>' );
		link.text( mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.html( link.html() + ' &raquo;' );
		link.attr( 'title', mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.attr( 'href', $.aftTrack.trackingUrl( url, track_id ) );
		$( '#firstHeading' ).append( link );

		// Track an impression
		$.aftTrack.trackClick( 'talk_page_view_feedback-impression' );
	}

} );
