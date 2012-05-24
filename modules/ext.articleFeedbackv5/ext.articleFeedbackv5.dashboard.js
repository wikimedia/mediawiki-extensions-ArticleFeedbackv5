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
		// Remove the extension's output & replace it with a warning to the user that his browser isn't supported
		var warning = $( '#articlefeedbackv5-beta-message' ).text( mw.msg( 'articlefeedbackv5-unsupported-message' ) );
		$( '#mw-content-text' ).empty().append( warning );

		return;
	}

	// Otherwise, we're good to go!
	$.articleFeedbackv5special.setup();

} );

