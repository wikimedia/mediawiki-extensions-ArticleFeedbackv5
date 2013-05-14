/**
 * Script for Article Feedback Extension: Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	// Check if the talk page link can be shown
	if ( mw.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {
		$.ajax( {
			'url'     : mw.util.wikiScript( 'api' ),
			'type'    : 'GET',
			'dataType': 'json',
			'data'    : {
				'pageid': $.aftUtils.article().id,
				'filter': 'featured',
				'action': 'articlefeedbackv5-get-count',
				'format': 'json'
			},
			'success': function ( data ) {
				if ( 'articlefeedbackv5-get-count' in data && 'count' in data['articlefeedbackv5-get-count'] ) {
					var count = data['articlefeedbackv5-get-count']['count'];

					if ( count > 0 ) {
						// Build the url to the Special:ArticleFeedbackv5 page
						var params = { ref: 'talk', filter: 'featured' };

						var url = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
							mw.util.wikiUrlencode( mw.config.get( 'aftv5Article' ).title );
						url = url + ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( params );

						// Add the link to the feedback-page next to the title
						var link = $( '<a id="articlefeedbackv5-talk-feedback-link"></a>' );
						link
							.text( mw.msg( 'articlefeedbackv5-talk-view-feedback', count ) )
							.html( link.html() + ' &raquo;' )
							.attr( 'href', url )
							.click( { trackingId: 'talk_page_view_feedback-button_click' }, $.aftTrack.trackEvent );
						$( '#firstHeading' ).append( link );

						// Check if AFT is enabled
						if ( $.aftUtils.verify( 'talk' ) ) {
							// Initialize clicktracking
							// NB: Using the talk page's namespace, title, and rev id, not
							// the article's as in the front end tracking
							$.aftTrack.init();

							// Track an impression
							$.aftTrack.track( 'talk_page_view_feedback-impression' );
						}
					}
				}
			}
		} );
	}

} );
