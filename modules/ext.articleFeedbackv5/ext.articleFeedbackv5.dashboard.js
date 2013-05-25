/*
 * This script determines if Special:ArticleFeedbackv5/<Article> should display
 * the feedback dashboard or not.
 */

/*** Main entry point ***/
( function( mw, $ ) {

var errorMessage;

// AFT is enabled
if ( $.aftUtils.verify( 'special' ) ) {
	$.articleFeedbackv5special.setup();

// AFT is not enabled
} else {
	errorMessage = mw.msg( 'articlefeedbackv5-page-disabled' );

	// unsupported browser
	if ( $.aftUtils.useragent() === false ) {
		errorMessage = mw.msg( 'articlefeedbackv5-unsupported-message' );
	}

	// display error message
	$( '#articlefeedbackv5-header-message' )
		.text( errorMessage )
		.insertAfter( $( '#articleFeedbackv5-special-wrap' ).hide() );
}

} )( mediaWiki, jQuery );
