/*
 * This script determines if Special:ArticleFeedbackv5/<Article> should display
 * the feedback dashboard or not.
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
};

/*** Main entry point ***/
jQuery( function( $ ) {

	// Is AFT enabled here?
	var enable = $.aftVerify.verify( 'special' );
	if ( !enable ) {
		// Remove the extension's output & replace it with a warning
		if ( $.aftVerify.checks.useragent === false ) {
			// The browser isn't supported
			var msg = 'articlefeedbackv5-unsupported-message';
		} else {
			// Feedback is disabled for the page
			var msg = 'articlefeedbackv5-page-disabled';
		}
		var warning = $( '#articlefeedbackv5-header-message' ).text( mw.msg( msg ) );
		$( '#articleFeedbackv5-special-wrap' ).empty().append( warning );
		return;
	}

	// Otherwise, we're good to go!
	$.articleFeedbackv5special.setup();

} );

