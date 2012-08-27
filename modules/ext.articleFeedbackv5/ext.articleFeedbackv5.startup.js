/**
 * Script for Article Feedback Extension: Article pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	// Is AFT enabled here?
	var enable = $.aftVerify.verify( 'article' );
	if ( enable ) {
		var removeAft = function() {
			var $aft = $( '#mw-articlefeedback' );
			if ( $aft.length > 0 ) {
				$aft.remove();
			} else {
				clearInterval( removeAftInterval );
			}
		};
		var removeAftInterval = setInterval( removeAft, 100 );

		mw.loader.load( 'ext.articleFeedbackv5' );
		// Load the IE-specific module
		if ( navigator.appVersion.indexOf( 'MSIE 7' ) != -1 ) {
			mw.loader.load( 'ext.articleFeedbackv5.ie' );
		}
	}

} );
