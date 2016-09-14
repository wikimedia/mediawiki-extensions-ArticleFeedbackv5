/*
 * This script determines if Special:ArticleFeedbackv5/<Article> should display
 * the feedback dashboard or not.
 */

/*** Main entry point ***/
jQuery( function( $ ) {
	var showError = function( message ) {
		var warning = $( '#articlefeedbackv5-header-message' ).text( message );
		$( '#articleFeedbackv5-special-wrap' )
			.hide()
			.after( warning );
	};

	// AFT is enabled
	if ( $.aftUtils.verify( 'special' ) ) {
		$.articleFeedbackv5special.setup();

	// AFT is not enabled
	} else {
		// unsupported browser
		if ( $.aftUtils.useragent() === false ) {
			showError( mediaWiki.msg( 'articlefeedbackv5-unsupported-message' ) );

		// AFT disabled for this page
		} else {
			showError( mediaWiki.msg( 'articlefeedbackv5-page-disabled' ) );
		}
	}
} );
