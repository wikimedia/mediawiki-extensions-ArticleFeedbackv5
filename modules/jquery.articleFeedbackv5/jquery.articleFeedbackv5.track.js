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
	// {{{ track

	/**
	 * Send something toward ClickTracking API
	 *
	 * @param string trackingId
	 */
	$.aftTrack.track = function ( trackingId ) {
		if ( $.aftTrack.clickTrackingOn && $.isFunction( $.trackActionWithInfo ) ) {
			return $.trackActionWithInfo(
				$.aftTrack.prefix( trackingId ),
				$.aftTrack.additional()
			);
		}

		return $.Deferred().resolve();
	};

	// }}}
	// {{{ trackEvent

	/**
	 * Tracks an event
	 * Example usage: $(this).click( { trackingId: 'trackingId' }, $.aftTrack.trackEvent );
	 *
	 * @param object e
	 */
	$.aftTrack.trackEvent = function ( e ) {
		// sanity check: valid call?
		if ( typeof e.data == 'undefined' || typeof e.data.trackingId == 'undefined' ) {
			return false;
		}

		/**
		 * $.trackActionWithInfo ends with a $.post to submit the data to
		 * ClickTracking API. We do not want any default behaviour to
		 * interrupt that ajax call, so prevent any default behaviour (e.g.
		 * redirect to a clicked link's href) until the call has completed
		 */
		e.preventDefault();
		e.stopPropagation();

		// submit call to ClickTracking API
		$.aftTrack.track( e.data.trackingId )
			.done( function () {
				/*
				 * At this point, the ClickTracking call has been completed.
				 * We can now resume the event's default behaviour (if any).
				 * To accomplish this, we'll:
				 * * unbind this event (don't want to track & prevent default again)
				 * * trigger the event anew (to let it complete normal behaviour)
				 * * re-bind this event (allowing subsequent events to be tracked again)
				 */
				$( e.target ).off( e.type, $.aftTrack.trackEvent );

				if ( typeof e.target.fireEvent == 'function' ) {
					e.target.fireEvent( e.type );
				} else {
					e.target[ e.type ]();
				}

				$( e.target ).bind( e.type, e.data, $.aftTrack.trackEvent );
			}
		);

		return true;
	};

	// }}}

// }}}

} )( jQuery );
