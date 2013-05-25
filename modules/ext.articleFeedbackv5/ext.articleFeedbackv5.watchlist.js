/**
 * Script for Article Feedback Extension: Watchlist pages
 */
( function( mw, $ ) {

var url;

// Check if the watchlist is enabled & link can be shown
if ( mw.config.get( 'wgArticleFeedbackv5Watchlist' ) && mw.config.get( 'wgArticleFeedbackv5WatchlistLink' ) ) {

	// Check if we're not dealing with anon user
	if ( mw.user.anonymous() ) {
		return;
	}

	// Initialize clicktracking
	// NB: Using the talk page's namespace, title, and rev id, not
	// the article's as in the front end tracking
	$.aftTrack.init();

	// Build the url to the Special:ArticleFeedbackv5Watchlist page
	url =
		mw.config.get( 'wgScript' ) + '?title=' +
		encodeURIComponent( mw.config.get( 'wgArticleFeedbackv5SpecialWatchlistUrl' ) ) +
		'&' + $.param( { ref: 'watchlist' } );

	// Add the link to the feedback-page next to the title
	$( '<a id="articlefeedbackv5-watchlist-feedback-link"></a>' )
		.text( mw.msg( 'articlefeedbackv5-watchlist-view-feedback' ) )
		.attr( 'href', url )
		.click( { trackingId: 'watchlist_view_feedback-button_click' }, $.aftTrack.trackEvent )
		.insertAfter( '#siteSub' );

	// Track an impression
	$.aftTrack.track( 'watchlist_view_feedback-impression' );
}

} )( mediaWiki, jQuery );
