/**
 * Script for Article Feedback Extension: Article pages
 */
( function( mw, $ ) {

// Is AFT enabled here?
if ( $.aftUtils.verify( 'article' ) ) {
	var removeAft, removeAftInterval;

	removeAft = function() {
		var $aft = $( '#mw-articlefeedback' );
		if ( $aft.length > 0 ) {
			$aft.remove();
		} else {
			clearInterval( removeAftInterval );
		}
	};
	removeAftInterval = setInterval( removeAft, 100 );

	mw.loader.load( 'ext.articleFeedbackv5' );
	// Load the IE-specific module
	if ( navigator.appVersion.indexOf( 'MSIE 7' ) !== -1 ) {
		mw.loader.load( 'ext.articleFeedbackv5.ie' );
	}
}

} )( mediaWiki, jQuery );
