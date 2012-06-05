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

	mw.loader.load( 'ext.articleFeedbackv5' );
	// Load the IE-specific module
	if( navigator.appVersion.indexOf( 'MSIE 7' ) != -1 ) {
		mw.loader.load( 'ext.articleFeedbackv5.ie' );
	}

} );
