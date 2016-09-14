/**
 * Script for Article Feedback Extension: Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	// Check if the watchlist is enabled & link can be shown
	if ( mediaWiki.config.get( 'wgArticleFeedbackv5Watchlist' ) && mediaWiki.config.get( 'wgArticleFeedbackv5WatchlistLink' ) ) {

		// Check if we're not dealing with anon user
		if ( mediaWiki.user.isAnon() ) {
			return;
		}

		// Initialize clicktracking
		// NB: Using the talk page's namespace, title, and rev id, not
		// the article's as in the front end tracking
		$.aftTrack.init();

		// Build the url to the Special:ArticleFeedbackv5Watchlist page
		var params = { ref: 'watchlist' };
		var url = mediaWiki.config.get( 'wgScript' ) + '?title=' +
			encodeURIComponent( mediaWiki.config.get( 'wgArticleFeedbackv5SpecialWatchlistUrl' ) ) +
			'&' + $.param( params );

		// Add the link to the feedback-page next to the title
		var link = $( '<a id="articlefeedbackv5-watchlist-feedback-link"></a>' );
		link
			.text( mediaWiki.msg( 'articlefeedbackv5-watchlist-view-feedback' ) )
			.attr( 'href', url )
			.click( { trackingId: 'watchlist_view_feedback-button_click' }, $.aftTrack.trackEvent );

		$( '#contentSub' ).append( link );

		// Track an impression
		$.aftTrack.track( 'watchlist_view_feedback-impression' );
	}

} );
