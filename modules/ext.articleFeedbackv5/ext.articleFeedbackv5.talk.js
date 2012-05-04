/*
 * Script for Article Feedback Extension on Talk pages
 */

/**
 * Global debug function
 *
 * @param any Output message
 */
aft5_debug = function( any ) {
	if ( typeof console != 'undefined' ) {
		console.log( any );
	}
}

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

	// Build the url to the Special ArticleFeedbackv5 page
	var aftUrl = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + mw.util.wikiUrlencode( mw.config.get( 'wgTitle' ) );
//	var aftUrl = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + mw.config.get( 'wgRelevantPageName' ).substr( mw.config.get( 'wgRelevantPageName' ).indexOf( ':' ) + 1 ); // alternative

	// Add the link to the feedback-page next to the title
	$( '#firstHeading' ).append( '<a id="articleFeedbackv5-talk-view-feedback" href="' + aftUrl + '" title="' + mw.msg( 'articlefeedbackv5-talk-view-feedback' ) + '">' + mw.msg( 'articlefeedbackv5-talk-view-feedback' ) + '</a>' );

} );
