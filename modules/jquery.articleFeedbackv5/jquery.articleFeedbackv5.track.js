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

	/**
	 * Array of events caught by trackEvent.
	 *
	 * @var array
	 */
	$.aftTrack.events = [];

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
		if ( $.aftTrack.clickTrackingOn ) {
			// @todo: implement EventLogging if/once requested
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
		/*
		 * Manually triggered (by this function) 2nd event. At this point, the
		 * ClickTracking call has been completed. We can now resume the event's
		 * default behavior or other bound event handlers (if any), but abort
		 * this function, it has been run.
		 */
		if ( ! ( e.type in $.aftTrack.events ) ) {
			$.aftTrack.events[e.type] = [];
		}
		var eventIndex = $.inArray( e.target, $.aftTrack.events[e.type] );
		if ( eventIndex > -1 ) {
			$.aftTrack.events[e.type].splice( eventIndex, 1 );
			return true;
		}
		$.aftTrack.events[e.type].push( e.target );

		// sanity check: valid call?
		if ( typeof e.data == 'undefined' || typeof e.data.trackingId == 'undefined' ) {
			return false;
		}

		/*
		 * IE does not appear to support a way to simulate a real event. Since we can
		 * not resume an event nor trigger an exact new one, let's just not stop this
		 * one (for IE) - I'm aware that this may result in aborted ClickTracking calls,
		 * but correct UX behavior is more important
		 */
		if ( typeof e.target.fireEvent == 'undefined' ) {
			/**
			 * $.trackActionWithInfo ends with a $.post to submit the data to
			 * ClickTracking API. We do not want any default behavior to
			 * interrupt that ajax call, so prevent any default behavior (e.g.
			 * redirect to a clicked link's href) until the call has completed
			 */
			e.preventDefault();
			e.stopPropagation();
		}

		// submit call to ClickTracking API
		$.aftTrack.track( e.data.trackingId )
			.done( function () {
				if ( typeof e.target.dispatchEvent != 'undefined' ) {
					/*
					 * Multiple browsers support dispatchEvent, though with
					 * different behavior.
					 * Opera (maybe other browsers as well) supports the general
					 * "Events" as parameter for createEvent. Firefox (and maybe
					 * others) need an exact e.g. "MouseEvent" for mouse events.
					 * Opera will know what to do based on the first parameter
					 * passed to initEvent (which is the actual event to perform),
					 * Firefox however will ignore this.
					 * IE9 does not appear to do much either way.
					 * IE8 and lower do not even support dispatchEvent.
					 * I do want to use the general "Events" though to keep this
					 * code general-purpose.
					 * If evt.eventPhase equals 0 (browsers other than Opera), the
					 * event will not properly be triggered, in which case we fall
					 * back to other methods.
					 *
					 * @see https://developer.mozilla.org/en-US/docs/DOM/event.eventPhase
					 */
					var evt = document.createEvent( 'Events' );
					evt.initEvent( e.type, e.bubbles, e.cancelable );
				}
				if ( typeof evt != 'undefined' && evt.eventPhase > 0 ) {
					e.target.dispatchEvent( evt );
				} else {
					if ( typeof e.target.fireEvent != 'undefined' ) {
						/*
						 * IE-specific; IE does not support <target>.<event>();
						 * This will only fire attached events, but will not simulate
						 * a "real event" (as in: triggering a click on a link will
						 * not make the browser follow the link)
						 */
						e.target.fireEvent( 'on' + e.type );
					} else {
						/**
						 * Firefox, Safari, Chrome & possibly others simulate a "real
						 * event" from this (Opera & IE do not)
						 */
						e.target[ e.type ]();
					}
				}
			}
		);

		return true;
	};

	// }}}

// }}}

} )( jQuery );
