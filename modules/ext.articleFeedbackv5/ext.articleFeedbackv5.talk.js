/**
 * Script for Article Feedback Extension: Talk pages
 */
( function( mw, $ ) {

// Check if the talk page link can be shown
if ( mw.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {

	var filter, api;

	filter = '*';
	/*
	 * If AFT is disabled for this page, we'll only want to show the link if
	 * there's leftover featured feedback.
	 */
	if ( !$.aftUtils.verify( 'talk' ) ) {
		filter = 'featured';
	}

	api = new mw.Api();
	api.get( {
		pageid: $.aftUtils.article().id,
		filter: filter,
		action: 'articlefeedbackv5-get-count',
		format: 'json'
	} )
	.done( function ( data ) {
		var count, url;

		if ( 'articlefeedbackv5-get-count' in data && 'count' in data['articlefeedbackv5-get-count'] ) {
			count = data['articlefeedbackv5-get-count']['count'];

			if ( count > 0 ) {
				// Build the url to the Special:ArticleFeedbackv5 page
				url =
					mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
					mw.util.wikiUrlencode( mw.config.get( 'aftv5Article' ).title );
				url += ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( { ref: 'talk', filter: 'featured' } );

				// Add the link to the feedback-page next to the title
				$( '<a id="articlefeedbackv5-talk-feedback-link"></a>' )
					.text( mw.msg( 'articlefeedbackv5-talk-view-feedback' ) )
					.attr( 'href', url )
					.click( { trackingId: 'talk_page_view_feedback-button_click' }, $.aftTrack.trackEvent )
					.insertAfter( '#siteSub' );

				// Track an impression
				$.aftTrack.init();
				$.aftTrack.track( 'talk_page_view_feedback-impression' );
			}
		}
	} );
}

} )( mediaWiki, jQuery );
