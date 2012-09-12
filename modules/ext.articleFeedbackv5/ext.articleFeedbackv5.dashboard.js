/*
 * This script determines if Special:ArticleFeedbackv5/<Article> should display
 * the feedback dashboard or not.
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	var showError = function( message ) {
		var warning = $( '#articlefeedbackv5-header-message' ).text( message );
		$( '#articleFeedbackv5-special-wrap' ).empty().append( warning );
	};

	// AFT is enabled
	if ( $.aftVerify.verify( 'special' ) ) {
		// no entries yet for this page
		if ( $( '#articleFeedbackv5-show-feedback' ).children().length == 0 ) {
			showError( mw.msg( 'articlefeedbackv5-no-feedback' ) );

		// launch AFT
		} else {
			$.articleFeedbackv5special.setup();
		}

	// AFT is not enabled
	} else {
		// unsupported browser
		if ( $.aftVerify.useragent() === false ) {
			showError( mw.msg( 'articlefeedbackv5-unsupported-message' ) );

		// AFT disabled for this page
		} else {
			showError( mw.msg( 'articlefeedbackv5-page-disabled' ) );
		}
	}

} );
