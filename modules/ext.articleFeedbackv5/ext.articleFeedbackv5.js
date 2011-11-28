/*
 * Script for Article Feedback Extension
 */
( function( $ ) {

// Only track users who have been assigned to the tracking group
var tracked = 'track' === mw.user.bucket(
	'ext.articleFeedbackv5-tracking', mw.config.get( 'wgArticleFeedbackv5Tracking' )
);

function trackClick( id ) {
	// Track the click so we can figure out how useful this is
	if ( tracked && $.isFunction( $.trackActionWithInfo ) ) {
		$.trackActionWithInfo( $.articleFeedbackv5.prefix( id ), mw.config.get( 'wgTitle' ) );
	}
}

var config = { };

/* Load at the bottom of the article */
var $aftDiv = $( '<div id="mw-articlefeedbackv5"></div>' ).articleFeedbackv5( config );

// Put on bottom of article before #catlinks (if it exists)
// Except in legacy skins, which have #catlinks above the article but inside content-div.
var legacyskins = [ 'standard', 'cologneblue', 'nostalgia' ];
if ( $( '#catlinks' ).length && $.inArray( mw.config.get( 'skin' ), legacyskins ) < 0 ) {
	$aftDiv.insertBefore( '#catlinks' );
} else {
	// CologneBlue, Nostalgia, ...
	mw.util.$content.append( $aftDiv );
}

/* Add link so users can navigate to the feedback tool from the toolbox */
var $tbAft = $( '<li id="t-articlefeedbackv5"><a href="#mw-articlefeedbackv5"></a></li>' )
	.find( 'a' )
		// TODO: Find out whether this needs to change per bucket.  Bucketing
		// logic may need to move out of the jquery component into here.
		.text( mw.msg( 'articlefeedbackv5-bucket5-form-switch-label' ) )
		.click( function() {
			// Click tracking
			trackClick( 'toolbox-link' );
			// Get the image, set the count and an interval.
			var $box = $( '#mw-articlefeedbackv5' );
			var count = 0;
			var interval = setInterval( function() {
				// Animate the opacity over .2 seconds
				$box.animate( { 'opacity': 0.5 }, 100, function() {
					// When finished, animate it back to solid.
					$box.animate( { 'opacity': 1.0 }, 100 );
				} );
				// Clear the interval once we've reached 3.
				if ( ++count >= 3 ) {
					clearInterval( interval );
				}
			}, 200 );
			return true;
		} )
		.end();
$( '#p-tb' ).find( 'ul' ).append( $tbAft );

} )( jQuery );
