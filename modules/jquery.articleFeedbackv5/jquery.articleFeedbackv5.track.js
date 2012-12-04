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

	/**
	 * Is this the front end or the special page?
	 */
	$.aftTrack.isSpecial = false;

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
		// Fill in options from the config
		config = config || {};
		if ( 'pageName' in config ) {
			$.aftTrack.pageName = config.pageName;
		}
		if ( 'revisionId' in config ) {
			$.aftTrack.revisionId = config.revisionId;
		}
		if ( 'isSpecial' in config && config.isSpecial ) {
			$.aftTrack.isSpecial = true;
		}
		// Are we tracking clicks?
		var b = mw.config.get( 'wgArticleFeedbackv5Tracking' );
		var key = 'ext.articleFeedbackv5@' + b.version + '-tracking';
		var setting = mw.user.bucket( key, b );
		if ( setting == 'track' ||
			( setting == 'track-special' && $.aftTrack.isSpecial ) ||
			( setting == 'track-front' && !$.aftTrack.isSpecial ) ) {
			$.aftTrack.clickTrackingOn = true;
		} else {
			$.aftTrack.clickTrackingOn = false;
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
		tmp.push( mw.user.id() );
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
		/*
		 * $.trackActionWithInfo ends with a $.post to submit the data to
		 * ClickTracking API. Let's make sure that this call is synchronous
		 * to make ensure that the call completes before the event leading
		 * up to this call propagates.
		 * An example of why we do not want this to be an async call, would
		 * be attempting to track clicking a link. The default behaviour
		 * after clicking a link will be that you're redirected to the link's
		 * href, which in turn would cancel out any async call that has not
		 * yet been completed - this will make sure not to propagate before
		 * completing the call.
		 */
		var current = $.ajaxSetup().async;
		$.ajaxSetup( { async: false } );

		var success = true;
		if ( $.aftTrack.clickTrackingOn && $.isFunction( $.trackActionWithInfo ) ) {
			$.trackActionWithInfo(
				$.aftTrack.prefix( trackingId ),
				$.aftTrack.additional()
			).fail( function() {
				success = false;
			});
		}

		// reset previous default (a)sync state
		$.ajaxSetup( { async: current } );

		return success;
	};

	// }}}

// }}}

} )( jQuery );
