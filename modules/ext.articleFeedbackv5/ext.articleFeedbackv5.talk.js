/*
 * Script for Article Feedback Extension on Talk pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {

	var ua = navigator.userAgent.toLowerCase();
	// Rule out MSIE 6, iPhone, iPod, iPad, Android
	if (
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
	if ( mw.config.get( 'wgArticleFeedbackv5TalkPageLink' ) ) {

		// Click tracking methods
		var clickTrackingOn = function () {
			var b = mw.config.get( 'wgArticleFeedbackv5Tracking' );
			if ( b.buckets.ignore == 100 && b.buckets.track == 0 ) {
				return false;
			}
			if ( b.buckets.ignore == 0 && b.buckets.track == 100 ) {
				return true;
			}
			var key = 'ext.articleFeedbackv5@' + b.version + '-tracking'
			return ( 'track' === mw.user.bucket( key, b ) );
		};

		var prefix = function ( track_id ) {
			var version = mw.config.get( 'wgArticleFeedbackv5Tracking' ).version || 0;
			return 'ext.articleFeedbackv5@' + version + '-' + track_id;
		};

		var trackingUrl = function ( url, track_id ) {
			if ( clickTrackingOn() ) {
				// NB: Using the talk page's namespace, title, and rev id, not
				// the article's as in the front end tracking
				return mw.config.get( 'wgScriptPath' ) + '/api.php?' + $.param( {
					'action': 'clicktracking',
					'format' : 'json',
					'eventid': prefix( track_id ),
					'namespacenumber': mw.config.get( 'wgNamespaceNumber' ),
					'token': $.cookie( 'clicktracking-session' ),
					'additional': mw.config.get( 'wgTitle' ) + '|' + mw.config.get( 'wgCurRevisionId' ),
					'redirectto': url
				} );
			} else {
				return url;
			}
		};

		var trackClick = function ( track_id ) {
			if ( clickTrackingOn() && $.isFunction( $.trackActionWithInfo ) ) {
				$.trackActionWithInfo(
					prefix( track_id ),
					mw.config.get( 'wgTitle' ) + '|' + mw.config.get( 'wgCurRevisionId' )
				);
			}
		};

		// Build the url to the Special:ArticleFeedbackv5 page
		var params = { ref: 'talk' };
		var track_id = 'talk_page_view_feedback-button_click';
		var url = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' +
			mw.util.wikiUrlencode( mw.config.get( 'wgTitle' ) ) +
			'?' + $.param( params );

		// Add the link to the feedback-page next to the title
		var link = $( '<a id="articleFeedbackv5-talk-view-feedback"></a>' );
		link.text( mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.attr( 'title', mw.msg( 'articlefeedbackv5-talk-view-feedback' ) );
		link.attr( 'href', trackingUrl( url, track_id ) );
		$( '#firstHeading' ).append( link );

		// Track an impression
		trackClick( 'talk_page_view_feedback-impression' );
	}

} );
