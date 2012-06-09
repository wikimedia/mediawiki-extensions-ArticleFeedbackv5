/*
 * Script for Article Feedback Extension
 */
( function( $ ) {

/* Load at the bottom of the article */
var $aftDiv = $( '<div id="mw-articlefeedbackv5"></div>' );

// Put on bottom of article before #catlinks (if it exists)
// Except in legacy skins, which have #catlinks above the article but inside content-div.
var legacyskins = [ 'standard', 'cologneblue', 'nostalgia' ];
if ( $( '#catlinks' ).length && $.inArray( mw.config.get( 'skin' ), legacyskins ) < 0 ) {
	$aftDiv.insertBefore( '#catlinks' );
} else {
	// CologneBlue, Nostalgia, ...
	mw.util.$content.append( $aftDiv );
}

$aftDiv.articleFeedbackv5();

/* Add basic edit tracking, making use of $.aftTrack() already being set up */
if ( $.aftTrack.clickTrackingOn ) {
	var clickTrackingSession = $.cookie( 'clicktracking-session' );
	var editEventBase = $.aftTrack.prefix( $aftDiv.articleFeedbackv5( 'experiment' ) );
	$( 'span.editsection a, #ca-edit a' ).each( function() {
		if ( $(this).is( '#ca-edit a' ) ) {
			var event = 'edit_tab_link';
		} else {
			var event = 'section_edit_link';
		}
		var href = $( this ).attr( 'href' );
		var editUrl = href + ( href.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( {
			'articleFeedbackv5_click_tracking': 1,
			'articleFeedbackv5_ct_token': clickTrackingSession,
			'articleFeedbackv5_ct_event': editEventBase + '-' + event
		} );
		$(this).attr( 'href', $.articleFeedbackv5.trackingUrl( editUrl, event + '-click' ) );
	} );
}

} )( jQuery );
