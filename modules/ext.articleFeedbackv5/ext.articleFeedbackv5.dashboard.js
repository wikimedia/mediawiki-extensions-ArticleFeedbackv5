/*
 * Script for Article Feedback Extension
 */

/**
 * Global debug function
 *
 * @param any Output message
 */
aft5_debug = function( any ) {
	if ( typeof console != 'undefined' ) {
		console.log( any );
	}
}

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
		// Remove the extension's output & replace it with a warning that the browser isn't supported
		var warning = $( '#articlefeedbackv5-header-message' ).text( mw.msg( 'articlefeedbackv5-unsupported-message' ) );
		$( '#articleFeedbackv5-special-wrap' ).empty().append( warning );
		return;
	}

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
			// This is the central page, so always on
			enable = true;
		} else {
			// Lottery inclusion (inverse of AFTv4, if we have a related article id)
			var v4odds = mw.config.get( 'wgArticleFeedbackLotteryOdds', -1 );
			enable = pageId == 0 || !( ( Number( pageId ) % 1000 )
				< Number( v4odds ) * 10 );
		}
	}

	if ( !enable ) {
		// Remove the extension's output & replace it with a warning that
		// feedback is disabled for the page.
		var warning = $( '#articlefeedbackv5-header-message' ).text( mw.msg( 'articlefeedbackv5-page-disabled' ) );
		$( '#articleFeedbackv5-special-wrap' ).empty().append( warning );
		return;
	}

	// Otherwise, we're good to go!
	$.articleFeedbackv5special.setup();

} );

