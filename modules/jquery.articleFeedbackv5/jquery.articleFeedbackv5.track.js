/**
 * ArticleFeedback tracking plugin
 *
 * This file creates the plugin that will be used to track usage of the Article
 * Feedback tool.
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

( function ( $ ) {

// {{{ aftTrack definition

	$.aftTrack = {};

	// {{{ Properties

	/**
	 * Are we tracking clicks?
	 */
	$.aftTrack.clickTrackingOn = false;

	/**
	 * The page name we'll be sending with the tracking
	 */
	$.aftTrack.pageName = mw.config.get( 'wgPageName' );

	/**
	 * The revision ID we'll be sending with the tracking
	 */
	$.aftTrack.revisionId = mw.config.get( 'wgCurRevisionId' );

	// }}}
	// {{{ init

	/**
	 * Initializes the object
	 *
	 * The init method sets up the object once the plugin has been called.
	 *
	 * @param config object the config object
	 */
	$.aftTrack.init = function ( config ) {
		// Are we tracking clicks?
		var b = mw.config.get( 'wgArticleFeedbackv5Tracking' );
		if ( b.buckets.ignore == 100 && b.buckets.track == 0 ) {
			$.aftTrack.clickTrackingOn = false;
		} else if ( b.buckets.ignore == 0 && b.buckets.track == 100 ) {
			$.aftTrack.clickTrackingOn = true;
		}
		var key = 'ext.articleFeedbackv5@' + b.version + '-tracking'
		$.aftTrack.clickTrackingOn = ( 'track' === mw.user.bucket( key, b ) );
		// Fill in options from the config
		config = config || {};
		if ( 'tracking' in config ) {
			$.aftTrack.clickTrackingOn = config.tracking ? true : false;
		}
		if ( 'pageName' in config ) {
			$.aftTrack.pageName = config.pageName;
		}
		if ( 'revisionId' in config ) {
			$.aftTrack.revisionId = config.revisionId;
		}
	};

	// }}}
	// {{{ prefix

	/**
	 * Utility method: Prefixes a key for tracking event names with extension and
	 * version information
	 *
	 * @param  key    string name of event to prefix
	 * @return string prefixed event name
	 */
	$.aftTrack.prefix = function ( key ) {
		var version = mw.config.get( 'wgArticleFeedbackv5Tracking' ).version || 0;
		return 'ext.articleFeedbackv5@' + version + '-' + key;
	};

	// }}}
	// {{{ additional

	/**
	 * Builds the additional data to pass with events
	 *
	 * @return string the additional data string
	 */
	$.aftTrack.additional = function () {
		var tmp = new Array();
		if ( $.aftTrack.pageName != '' ) {
			tmp.push( $.aftTrack.pageName );
		}
		if ( $.aftTrack.revisionId != 0 ) {
			tmp.push( $.aftTrack.revisionId );
		}
		return tmp.join( '|' );
	};

	// }}}
	// {{{ trackClick

	/**
	 * Tracks a click
	 *
	 * @param trackingId string the tracking ID
	 */
	$.aftTrack.trackClick = function ( trackingId ) {
		if ( $.aftTrack.clickTrackingOn && $.isFunction( $.trackActionWithInfo ) ) {
			$.trackActionWithInfo(
				$.aftTrack.prefix( trackingId ),
				$.aftTrack.additional()
			);
		}
	};

	// }}}
	// {{{ trackingUrl

	/**
	 * Creates a URL that tracks a particular click
	 *
	 * @param url        string the url so far
	 * @param trackingId string the tracking ID
	 */
	$.aftTrack.trackingUrl = function ( url, trackingId ) {
		if ( $.aftTrack.clickTrackingOn ) {
			return $.aftTrack.trackActionURL( url, $.aftTrack.prefix( trackingId ) );
		} else {
			return url;
		}
	};

	// }}}
	// {{{ trackActionURL

	/**
	 * Rewrites a URL to one that runs through the ClickTracking API module
	 * which registers the event and redirects to the real URL
	 *
	 * This is a copy of the one out of the clicktracking javascript API
	 * we have to do our own because there is no "additional" option in that
	 * API which we need for the article title
	 *
	 * @param string url url to redirect to
	 * @param string id  the tracking id
	 */
	$.aftTrack.trackActionURL = function( url, id ) {
		return mw.config.get( 'wgScriptPath' ) + '/api.php?' + $.param( {
			'action': 'clicktracking',
			'format' : 'json',
			'eventid': id,
			'namespacenumber': mw.config.get( 'wgNamespaceNumber' ),
			'token': $.cookie( 'clicktracking-session' ),
			'additional': $.aftTrack.additional(),
			'redirectto': url
		} );
	};

	// }}}

// }}}

} )( jQuery );


