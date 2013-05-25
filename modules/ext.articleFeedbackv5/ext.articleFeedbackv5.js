/*
 * Script for Article Feedback Extension
 */
( function( mw, $ ) {

var $aftDiv, legacyskins, api;

// Load at the bottom of the article
$aftDiv = $( '<div id="mw-articlefeedbackv5"></div>' );

// Put on bottom of article before #catlinks (if it exists)
// Except in legacy skins, which have #catlinks above the article but inside content-div.
legacyskins = [ 'standard', 'cologneblue', 'nostalgia' ];
if ( $( '#catlinks' ).length && $.inArray( mw.config.get( 'skin' ), legacyskins ) < 0 ) {
	$aftDiv.insertBefore( '#catlinks' );
} else {
	// CologneBlue, Nostalgia, ...
	mw.util.$content.append( $aftDiv );
}

// Init AFTv5 feedback form
$aftDiv.articleFeedbackv5();

// Check if the article page link can be shown
if (
	mw.config.get( 'wgArticleFeedbackv5ArticlePageLink' ) &&
	mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-editor']
) {
	api = new mw.Api();
	api.get( {
		pageid: $.aftUtils.article().id,
		filter: 'featured',
		action: 'articlefeedbackv5-get-count',
		format: 'json'
	} )
	.done( function ( data ) {
		var count, url;

		if ( 'articlefeedbackv5-get-count' in data && 'count' in data['articlefeedbackv5-get-count'] ) {
			count = data['articlefeedbackv5-get-count'].count;

			if ( count > 0 ) {
				// Build the url to the Special:ArticleFeedbackv5 page
				url =
					mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
					mw.util.wikiUrlencode( mw.config.get( 'aftv5Article' ).title );
				url += ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( { ref: 'article', filter: 'featured' } );

				// Add the link to the feedback-page next to the title
				$( '<a id="articlefeedbackv5-article-feedback-link"></a>' )
					.msg( 'articlefeedbackv5-article-view-feedback', count )
					.attr( 'href', url )
					.click( { trackingId: 'article_page_view_feedback-button_click' }, $.aftTrack.trackEvent )
					.insertAfter( '#siteSub' );

				// Track an impression
				$.aftTrack.init();
				$.aftTrack.track( 'article_page_view_feedback-impression' );
			}
		}
	} );
}

// Add basic edit tracking, making use of $.aftTrack() already being set up
if ( $.aftTrack.clickTrackingOn ) {
	$( 'span.editsection a, #ca-edit a, #ca-viewsource a' ).each( function() {
		var trackingId, url;

		if ( $( this ).is( '#ca-edit a' ) ) {
			trackingId = 'edit_tab_link';
		} else if ( $( this ).is( '#ca-viewsource a' ) ) {
			trackingId = 'view_source_tab_link';
		} else {
			trackingId = 'section_edit_link';
		}

		url = $( this ).attr( 'href' );
		url += ( url.indexOf( '?' ) >= 0 ? '&' : '?' ) + $.param( {
			'articleFeedbackv5_click_tracking': 1,
			'articleFeedbackv5_ct_cttoken': $.cookie( 'clicktracking-session' ),
			'articleFeedbackv5_ct_usertoken': mw.user.id(),
			'articleFeedbackv5_ct_event': trackingId
		} );

		$( this )
			.attr( 'href', url )
			.click( { trackingId: trackingId + '-click' }, $.aftTrack.trackEvent );
	} );
}

} )( mediaWiki, jQuery );
