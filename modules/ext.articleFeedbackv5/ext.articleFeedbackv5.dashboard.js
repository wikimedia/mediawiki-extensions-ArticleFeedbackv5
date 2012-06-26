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

	var enable = false;
	var whitelist = mw.config.get( 'aftv5Whitelist', -1 );
	var v4odds = mw.config.get( 'wgArticleFeedbackLotteryOdds', -1 );
	var pageId = mw.config.get( 'aftv5PageId', -1 );

	// special page central feedback page = show
	if ( mw.config.get( 'wgNamespaceNumber' ) == -1 && pageId == 0 ) {
		enable = true;
	}

	// aftv5Whitelist true = show
	else if ( whitelist ) { 
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
		// Remove the extension's output & replace it with a warning that
		// feedback is disabled for the page.
		var warning = $( '#articlefeedbackv5-header-message' ).text( mw.msg( 'articlefeedbackv5-page-disabled' ) );
		$( '#articleFeedbackv5-special-wrap' ).empty().append( warning );
		return;
	}

	// Otherwise, we're good to go!
	$.articleFeedbackv5special.setup();

} );

