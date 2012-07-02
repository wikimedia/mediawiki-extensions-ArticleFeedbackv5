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
		/* (ua.indexOf( 'msie 8' ) != -1) || */
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

		var enable = false;
		var whitelist = mw.config.get( 'aftv5Whitelist', -1 );
		var v4odds = mw.config.get( 'wgArticleFeedbackLotteryOdds', -1 );
		var pageId = mw.config.get( 'aftv5PageId', -1 );

		// aftv5Whitelist true = show
		if ( whitelist ) { 
			enable = true;
		}

		// aftv4 doesn't win lottery (AF5v5 is inverse of AFTv4 lottery, for now) = show
		else if ( !(( Number( pageId ) % 1000 ) < Number( v4odds ) * 10) ) {
			enable = true
		}

		// no vars = cached
		// -> either it's really old, from when allowance code was only in JS = probably don't show
		// -> either it's not too old, so PHP allowance let it through = show
		// there's no way to know though, so we just won't show a thing, for now

		// blacklist has been handled by PHP; if it's blacklisted, this JS won't be loaded

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
		var link = $( '<a id="articlefeedbackv5-talk-feedback-link"></a>' );
		link.text( mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.html( link.html() + ' &raquo;' );
		link.attr( 'href', $.aftTrack.trackingUrl( url, track_id ) );
		$( '#firstHeading' ).append( link );

		// Track an impression
		$.aftTrack.trackClick( 'talk_page_view_feedback-impression' );
	}

} );
