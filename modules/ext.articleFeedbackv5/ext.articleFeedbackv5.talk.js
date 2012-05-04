/*
 * Script for Article Feedback Extension on Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	var ua = navigator.userAgent.toLowerCase();
	// Rule out MSIE 6, iPhone, iPod, iPad, Android
	if(
		(ua.indexOf( 'msie 6' ) != -1) ||
		/*(ua.indexOf( 'msie 7' ) != -1) ||*/
		(ua.indexOf( 'firefox/2') != -1) ||
		(ua.indexOf( 'firefox 2') != -1) ||
		(ua.indexOf( 'android' ) != -1) ||
		(ua.indexOf( 'iphone' ) != -1) ||
		(ua.indexOf( 'ipod' ) != -1 ) ||
		(ua.indexOf( 'ipad' ) != -1)
	) {
		return;
	}

	// Check if the talk page link can be shown
	if( mw.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {
		var link = $( '<a id="articleFeedbackv5-talk-view-feedback"></a>' );
		link.text( mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.attr( 'title', mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );

		// Build the url to the Special:ArticleFeedbackv5 page
		link.attr( 'href', mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' + mw.util.wikiUrlencode( mw.config.get( 'wgTitle' ) ) );
//		link.attr( 'href', mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' + mw.config.get( 'wgRelevantPageName' ).substr( mw.config.get( 'wgRelevantPageName' ).indexOf( ':' ) + 1 ) ); // alternative

		// Add the link to the feedback-page next to the title
		$( '#firstHeading' ).append( link );
	}

} );
