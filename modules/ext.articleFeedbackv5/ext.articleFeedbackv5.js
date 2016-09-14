/*
 * Script for Article Feedback Extension
 */
( function( $, mw ) {

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

	// Check if the article page link can be shown
	if ( mw.config.get( 'wgArticleFeedbackv5ArticlePageLink' ) &&
		mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-editor'] ) {

		var api = new mw.Api();
		api.get( {
			'pageid': $.aftUtils.article().id,
			'filter': 'featured',
			'action': 'articlefeedbackv5-get-count',
			'format': 'json'
		} )
		.done( function ( data ) {
			if ( 'articlefeedbackv5-get-count' in data && 'count' in data['articlefeedbackv5-get-count'] ) {
				var count = data['articlefeedbackv5-get-count'].count;

				if ( count > 0 ) {
					// Build the url to the Special:ArticleFeedbackv5 page
					var url =
						mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
						mw.util.wikiUrlencode( mw.config.get( 'aftv5Article' ).title );
					url += ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( { ref: 'article', filter: 'featured' } );

					// Add the link to the feedback-page next to the title
					var $link = $( '<a id="articlefeedbackv5-article-feedback-link"></a>' )
						.msg( 'articlefeedbackv5-article-view-feedback', count )
						.attr( 'href', url )
						.click( { trackingId: 'article_page_view_feedback-button_click' }, $.aftTrack.trackEvent );

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
					$.aftTrack.track( 'article_page_view_feedback-impression' );
				}
			}
		} );
	}

	/* Add basic edit tracking, making use of $.aftTrack() already being set up */
	if ( $.aftTrack.clickTrackingOn ) {
		var editEventBase = $.aftTrack.prefix( $aftDiv.articleFeedbackv5( 'experiment' ) );

		$( 'span.editsection a, #ca-edit a, #ca-viewsource a' ).each( function() {
			var event;
			if ( $(this).is( '#ca-edit a' ) ) {
				event = 'edit_tab_link';
			} else if ( $(this).is( '#ca-viewsource a' ) ) {
				event = 'view_source_tab_link';
			} else {
				event = 'section_edit_link';
			}

			var href = $( this ).attr( 'href' );
			var editUrl = href + ( href.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( {
				'articleFeedbackv5_click_tracking': 1,
				'articleFeedbackv5_ct_cttoken': $.cookie( 'clicktracking-session' ),
				'articleFeedbackv5_ct_usertoken': mw.user.id(),
				'articleFeedbackv5_ct_event': editEventBase + '-' + event
			} );

			$(this)
				.attr( 'href', editUrl )
				.click( { trackingId: event + '-click' }, $.aftTrack.trackEvent );
		} );
	}

}( jQuery, mediaWiki ) );
