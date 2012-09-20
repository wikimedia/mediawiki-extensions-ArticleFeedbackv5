/**
 * Script for Article Feedback Extension: Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	// Check if the talk page link can be shown
	if ( mw.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {

		// Check if AFT is enabled
		var enable = $.aftVerify.verify( 'talk' );
		if ( !enable ) {
			return;
		}

		// Initialize clicktracking
		// NB: Using the talk page's namespace, title, and rev id, not
		// the article's as in the front end tracking
		$.aftTrack.init();

		// Build the url to the Special:ArticleFeedbackv5 page
		var params = { ref: 'talk' };
		var track_id = 'talk_page_view_feedback-button_click';
		var url = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
			mw.util.wikiUrlencode( mw.config.get( 'aftv5Article' ).title );
		url = url + ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( params );

		// Add the link to the feedback-page next to the title
		var link = $( '<a id="articlefeedbackv5-talk-feedback-link"></a>' );
		link.text( mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.html( link.html() + ' &raquo;' );
		link.attr( 'href', $.aftTrack.trackingUrl( url, track_id ) );
		$( '#firstHeading' ).append( link );

		// Track an impression
		$.aftTrack.trackClick( 'talk_page_view_feedback-impression' );
	}

} );
