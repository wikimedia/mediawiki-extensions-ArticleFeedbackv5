/**
 * Script for Article Feedback Extension: Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {
	// Check if the talk page link can be shown
	if ( mediaWiki.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {

		var filter = '*';
		/*
		 * If AFT is disabled for this page, we'll only want to show the link if
		 * there's leftover featured feedback.
		 */
		if ( !$.aftUtils.verify( 'talk' ) ) {
			filter = 'featured';
		}

		var api = new mw.Api();
		api.get( {
			'pageid': $.aftUtils.article().id,
			'filter': filter,
			'action': 'articlefeedbackv5-get-count',
			'format': 'json'
		} )
		.done( function ( data ) {
			if ( 'articlefeedbackv5-get-count' in data && 'count' in data['articlefeedbackv5-get-count'] ) {
				var count = data['articlefeedbackv5-get-count'].count;

				if ( count > 0 ) {
					// Build the url to the Special:ArticleFeedbackv5 page
					var url =
						mediaWiki.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
						mediaWiki.util.wikiUrlencode( mediaWiki.config.get( 'aftv5Article' ).title );
					url += ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( { ref: 'talk', filter: 'featured' } );

					// Add the link to the feedback-page next to the title
					var $link = $( '<a id="articlefeedbackv5-talk-feedback-link"></a>' )
						.text( mediaWiki.msg( 'articlefeedbackv5-talk-view-feedback' ) )
						.attr( 'href', url )
						.click( { trackingId: 'talk_page_view_feedback-button_click' }, $.aftTrack.trackEvent );

					/*
					 * Add the link next to #siteSub. Append to #siteSub node if
					 * it's visible, so we inherit it's style. Otherwise, add as
					 * new node, right after #siteSub
					 */
					if ( $( '#siteSub' ).is( ':visible' ) ) {
						$link.appendTo( '#siteSub' );
					} else {
						$link.insertAfter( '#siteSub' );
					}

					// Track an impression
					$.aftTrack.init();
					$.aftTrack.track( 'talk_page_view_feedback-impression' );
				}
			}
		} );
	}
} );
