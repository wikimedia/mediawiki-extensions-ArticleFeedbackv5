/**
 * ArticleFeedback special page
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Greg Chiasson <gchiasson@omniti.com>
 * @author     Yoni Shostak <yoni@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

( function ( $ ) {

// {{{ articleFeedbackv5special definition

	$.articleFeedbackv5special = {};

	// {{{ Properties

	/**
	 * What page is this?
	 */
	$.articleFeedbackv5special.page = undefined;

	/**
	 * Are we at the watchlist page?
	 */
	$.articleFeedbackv5special.watchlist = undefined;

	/**
	 * The url to which to send the pull request
	 */
	$.articleFeedbackv5special.apiUrl = mw.util.wikiScript( 'api' );

	/**
	 * The current user type
	 *
	 * anon = Unregistered
	 * registered = Registered
	 * editor = Autoconfirmed
	 * monitor = Rollbacker / Reviewer
	 * oversighter = Oversighter
	 *
	 * @see http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Access_and_permissions
	 */
	$.articleFeedbackv5special.userType = undefined;

	/**
	 * The referral -- how the user got to this page
	 *
	 * url  = Typed in the url directly
	 * cta  = Clicked on the link in CTA 5
	 * talk = Clicked on the link in the talk page
	 */
	$.articleFeedbackv5special.referral = undefined;

	/**
	 * Controls for the list: sort, filter, continue flag, etc
	 */
	$.articleFeedbackv5special.listControls = {
		filter: mw.config.get( 'afStartingFilter' ),
		feedbackId: mw.config.get( 'afStartingFeedbackId' ), // Permalinks require a feedback ID
		sort: mw.config.get( 'afStartingSort' ),
		sortDirection: mw.config.get( 'afStartingSortDirection' ),
		offset: null,
		disabled: false,	// Prevent (at least limit) a flood of ajax requests.
		allowMultiple: false
	};

	/**
	 * User activity: for each feedback record on this page, anything the user
	 * has done (flagged as abuse, marked as helpful/unhelpful)
	 *
	 * @var object
	 */
	$.articleFeedbackv5special.activity = {};

	/**
	 * Filter cookie name (page id is appended on init)
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.filterCookieName = 'last-filter';

	/**
	 * User activity cookie name (page id is appended on init)
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.activityCookieName = 'aft-activity';

	/**
	 * Currently displayed panel host element id attribute value
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.currentPanelHostId = undefined;

	/**
	 * Callback to be executed when tipsy form is submitted
	 *
	 * @var function
	 */
	$.articleFeedbackv5special.tipsyCallback = undefined;

	/**
	 * Highlighted feedback ID
	 *
	 * @var int
	 */
	$.articleFeedbackv5special.highlightId = undefined;

	/**
	 * Action note flyover panel HTML template
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.notePanelHtmlTemplate = '\
		<div class="articleFeedbackv5-flyover-header">\
			<h3 id="articleFeedbackv5-noteflyover-caption"></h3>\
			<a id="articleFeedbackv5-noteflyover-close" href="#"></a>\
		</div>\
		<form class="articleFeedbackv5-form-flyover">\
			<div id="articleFeedbackv5-noteflyover-description"></div>\
			<label id="articleFeedbackv5-noteflyover-label" for="articleFeedbackv5-noteflyover-note"></label>\
			<input type="text" id="articleFeedbackv5-noteflyover-note" name="articleFeedbackv5-noteflyover-note" />\
			<div class="articleFeedbackv5-flyover-footer">\
				<a id="articleFeedbackv5-noteflyover-submit" class="articleFeedbackv5-flyover-button" href="#"></a>\
				<a class="articleFeedbackv5-flyover-help" id="articleFeedbackv5-noteflyover-help" href="#"></a>\
				<div class="clear"></div>\
			</div>\
		</form>';

	/**
	 * Mask HMTL template
	 */
	$.articleFeedbackv5special.maskHtmlTemplate = '\
		<div class="articleFeedbackv5-post-screen">\
			<div class="articleFeedbackv5-mask-text-wrapper">\
				<span class="articleFeedbackv5-mask-text">\
					<span class="articleFeedbackv5-mask-info"></span>\
					<span class="articleFeedbackv5-mask-view"><a href="#" onclick="return false;">\
					</a></span>\
				</span>\
			</div>\
		</div>';

	/**
	 * Loading tag template
	 */
	$.articleFeedbackv5special.loadingTemplate = '\
		<div id="articleFeedbackv5-feedback-loading">\
			<span class="articleFeedbackv5-loading-message"></span>\
		</div>';

	// }}}
	// {{{ Init methods

	// {{{ setup

	/**
	 * Sets up the page
	 */
	$.articleFeedbackv5special.setup = function() {
		// Get the user type
		if ( mw.user.anonymous() ) {
			$.articleFeedbackv5special.userType = 'anon';
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-oversighter'] ) {
			$.articleFeedbackv5special.userType = 'oversighter';
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-monitor'] ) {
			$.articleFeedbackv5special.userType = 'monitor';
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-editor'] ) {
			$.articleFeedbackv5special.userType = 'editor';
		} else {
			$.articleFeedbackv5special.userType = 'registered';
		}

		// Get the referral
		$.articleFeedbackv5special.referral = mw.config.get( 'afReferral' );

		// Set up config vars
		$.articleFeedbackv5special.page = mw.config.get( 'afPageId' );
		$.articleFeedbackv5special.watchlist = mw.config.get( 'wgCanonicalSpecialPageName' ) == 'ArticleFeedbackv5Watchlist' ? 1 : 0;

		// check if there is feedback
		$.articleFeedbackv5special.emptyMessage();

		// Initialize clicktracking
		$.aftTrack.init({
			pageName: $.articleFeedbackv5special.page,
			revisionId: 0,
			isSpecial: true
		});

		// Grab the user's activity out of the cookie
		$.articleFeedbackv5special.loadActivity();

		// Bind events
		$.articleFeedbackv5special.setBinds();

		// set tipsy defaults, once
		$.fn.tipsy.defaults = {
			delayIn: 0,					// delay before showing tooltip (ms)
			delayOut: 0,				// delay before hiding tooltip (ms)
			fade: false,				// fade tooltips in/out?
			fallback: '',				// fallback text to use when no tooltip text
			gravity: $.fn.tipsy.autoWE,	// gravity according to directionality
			html: true,					// is tooltip content HTML?
			live: false,				// use live event support?
			offset: 10,					// pixel offset of tooltip from element
			opacity: 1.0,				// opacity of tooltip
			title: 'title',				// attribute/callback containing tooltip text
			trigger: 'manual'			// how tooltip is triggered - hover | focus | manual
		};

		// clicking anywhere (but tipsy) should close an open tipsy
		$( document ).click( function(e) {
			if (
				// if a panel is currently open
				$.articleFeedbackv5special.currentPanelHostId !== undefined &&
				// and we did not just open it
				$.articleFeedbackv5special.currentPanelHostId != $( e.target ).attr( 'id' ) &&
				// and we clicked outside of the open panel
				$( e.target ).parents( '.tipsy' ).length == 0
			) {
				$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
				$.articleFeedbackv5special.currentPanelHostId = undefined;
			}
		} );

		// Link to help is dependent on the group the user belongs to
		var helpLink = mw.msg( 'articlefeedbackv5-help-special-linkurl' );
		if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-oversighter'] ) {
			helpLink = mw.msg( 'articlefeedbackv5-help-special-linkurl-oversighters' );
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-monitor'] ) {
			helpLink = mw.msg( 'articlefeedbackv5-help-special-linkurl-monitors' );
		} else if ( mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-editor'] ) {
			helpLink = mw.msg( 'articlefeedbackv5-help-special-linkurl-editors' );
		}

		// localize tipsies
		for ( var action in $.articleFeedbackv5special.actions ) {
			var $container = $( '<div></div>' );
			if ( $.articleFeedbackv5special.actions[action].hasTipsy && $.articleFeedbackv5special.actions[action].tipsyHtml == undefined ) {
				$container.html( $.articleFeedbackv5special.notePanelHtmlTemplate );
				$container.find( '#articleFeedbackv5-noteflyover-caption' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-caption' ) );
				$container.find( '#articleFeedbackv5-noteflyover-description' ).html( mw.config.get( 'mw.msg.articlefeedbackv5-noteflyover-' + action + '-description' ) );
				$container.find( '#articleFeedbackv5-noteflyover-label' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-label' ) );
				$container.find( '#articleFeedbackv5-noteflyover-submit' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-submit' ) );
				// will add an 'action' attribute to the link
				$container.find( '#articleFeedbackv5-noteflyover-help' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-help' ) );
				$container.find( '#articleFeedbackv5-noteflyover-help' ).attr( 'href', helpLink + mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-help-link' ) );
			} else {
				$container.html( $.articleFeedbackv5special.actions[action].tipsyHtml );
			}
			$.articleFeedbackv5special.actions[action].tipsyHtml = $container.localize( { 'prefix': 'articlefeedbackv5-' } ).html();
		}

		// Add a loading tag to the top and hide it
		var $loading1 = $( $.articleFeedbackv5special.loadingTemplate );
		$loading1.attr( 'id', $loading1.attr( 'id' ) + '-top' );
		$loading1.find( '.articleFeedbackv5-loading-message' ).text( mw.msg( 'articlefeedbackv5-loading-tag' ) );
		$loading1.hide();
		$( '#articleFeedbackv5-show-feedback' ).before( $loading1 );

		// Add a loading tag to the bottom and hide it
		var $loading2 = $( $.articleFeedbackv5special.loadingTemplate );
		$loading2.attr( 'id', $loading2.attr( 'id' ) + '-bottom' );
		$loading2.find( '.articleFeedbackv5-loading-message' ).text( mw.msg( 'articlefeedbackv5-loading-tag' ) );
		$loading2.hide();
		$( '#articleFeedbackv5-show-more' ).before( $loading2 );
		$( '#articleFeedbackv5-refresh-list' ).button();

		// Is there a highlighted ID?
		var hash = window.location.hash.replace( '#', '' );
		if ( hash.match( /^\w+$/ ) && $.articleFeedbackv5special.filter != 'id' ) {
			$.articleFeedbackv5special.highlightId = hash;
			$.articleFeedbackv5special.pullHighlight();
		}

		// Process preloaded feedback
		$.articleFeedbackv5special.processControls(
			mw.config.get( 'afCount' ),
			mw.config.get( 'afFilterCount' ),
			mw.config.get( 'afOffset' ),
			mw.config.get( 'afShowMore' )
		);
		$.articleFeedbackv5special.processFeedback();

		// Track an impression
		$.aftTrack.track( 'feedback_page-impression-' +
			$.articleFeedbackv5special.referral + '-' +
			$.articleFeedbackv5special.userType );

		// Add BETA label next to the title
		var label = $( '<p id="articleFeedbackv5-beta-label"></p>' );
		label.text( mw.msg( 'articlefeedbackv5-beta-label' ) );
		$( '#firstHeading' ).prepend( label );
	};

	// }}}
	// {{{ checkClickTracking

	/**
	 * Checks whether click tracking is turned on
	 *
	 * Only track users who have been assigned to the tracking group; don't bucket
	 * at all if we're set to always ignore or always track.
	 */
	$.articleFeedbackv5special.checkClickTracking = function () {
		var b = mw.config.get( 'wgArticleFeedbackv5Tracking' );
		if ( b.buckets.ignore == 100 && b.buckets.track == 0 ) {
			return false;
		}
		if ( b.buckets.ignore == 0 && b.buckets.track == 100 ) {
			return true;
		}
		var key = 'ext.articleFeedbackv5@' + b.version + '-tracking';
		return ( 'track' === mw.user.bucket( key, b ) );
	};

	// }}}
	// {{{ setBinds

	/**
	 * Binds events for each of the controls
	 */
	$.articleFeedbackv5special.setBinds = function() {
		// Filter select
		$( '#articleFeedbackv5-filter-select' ).bind( 'change', function( e ) {
			var id = $(this).val();
			if ( id == '' || id == 'X' ) {
				return false;
			}
			$.articleFeedbackv5special.toggleFilter( id );
			$.articleFeedbackv5special.loadFeedback( true, false );
			return false;
		} );

		// Disable the dividers
		$( '#articleFeedbackv5-filter-select option[value=]' ).attr( 'disabled', true );
		$( '#articleFeedbackv5-filter-select option[value=X]' ).attr( 'disabled', true );

		// Filter links
		$( '.articleFeedbackv5-filter-link' ).bind( 'click', function( e ) {
			e.preventDefault();
			var	id = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-special-filter-' );
			$.articleFeedbackv5special.toggleFilter( id );
			$.articleFeedbackv5special.loadFeedback( true, false );
		} );

		// Sort select
		$( '#articleFeedbackv5-sort-select' ).bind( 'change', function( e ) {
			var sort = $(this).val().split( '-' );
			if ( sort == '' ) {
				return false;
			}
			$.articleFeedbackv5special.toggleSort( sort[0], sort[1] );
			$.articleFeedbackv5special.loadFeedback( true, false );
			return false;
		} );

		// Disable the dividers
		$( '#articleFeedbackv5-sort-select option[value=]' ).attr( 'disabled', true );

		// Show more
		$( '#articleFeedbackv5-show-more' ).bind( 'click', function( e ) {
			$.articleFeedbackv5special.loadFeedback( false, false );
			return false;
		} );

		// Refresh list
		$( '#articleFeedbackv5-refresh-list' ).bind( 'click', function( e ) {
			$.articleFeedbackv5special.listControls.offset = null;
			$.articleFeedbackv5special.loadFeedback( true, false );
			return false;
		} );

		// When clicking the hidden post mask, remove the mask
		$( document ).on( 'click touchstart', '.articleFeedbackv5-post-screen', function() {
			// don't hide if it's only an empty mask (= when insufficient permissions
			// won't let the user see the feedback's details)
			if ( $( this ).parent( '.articleFeedbackv5-feedback-emptymask' ) === 0 ) {
				$( this ).hide();
			}
		});

		// Bind actions
		for ( var action in $.articleFeedbackv5special.actions ) {
			$( document ).on( 'click touchstart', '.articleFeedbackv5-' + action + '-link', function( e ) {
				var action = $( this ).data( 'action' );

				if ( !$( this ).hasClass( 'inactive' ) ) {
					$.articleFeedbackv5special.actions[action].click( e );
				}
			} );
		}

		// Bind submit actions on flyover panels (post-flag comments)
		$( document ).on( 'click touchstart', '#articleFeedbackv5-noteflyover-submit', function( e ) {
			e.preventDefault();

			if ( typeof $.articleFeedbackv5special.tipsyCallback == 'function' ) {
				// execute and clear callback function
				$.articleFeedbackv5special.tipsyCallback( e );
				$.articleFeedbackv5special.tipsyCallback = undefined;
			} else {
				var $container = $( '#' + $.articleFeedbackv5special.currentPanelHostId ).closest( '.articleFeedbackv5-feedback' );
				var id = $container.data( 'id' );
				var logId = $container.find( '#articleFeedbackv5-note-link-' + id ).data( 'log-id' );

				$.articleFeedbackv5special.addNote(
					id,
					logId,
					$( '#articleFeedbackv5-noteflyover-note' ).attr( 'value' )
				);
			}

			// hide tipsy
			$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
			$.articleFeedbackv5special.currentPanelHostId = undefined;
		} );

		// bind flyover panel close button
		$( document ).on( 'click touchstart', '#articleFeedbackv5-noteflyover-close', function( e ) {
			e.preventDefault();
			$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
			$.articleFeedbackv5special.currentPanelHostId = undefined;
		} );
	};

	// }}}
	// {{{ bindTipsies

	/**
	 * Bind panels to controls - that cannot be 'live' events due to jQuery.tipsy
	 * limitations. This function should be invoked after feedback posts are loaded,
	 * without parameters. The function should be invoked with the id parameter set
	 * after an action is executed and its link is replaced ith reverse action.
	 *
	 * @param id post id to bind panels for. If none is supplied, bind entire list.
	 */
	$.articleFeedbackv5special.bindTipsies = function( id ) {
		// single post or entire list?
		var $selector = !id ? $( '#articleFeedbackv5-show-feedback' ) : $( '.articleFeedbackv5-feedback[data-id="' + id + '"]' );

		// bind tipsies
		$selector.find( '.articleFeedbackv5-tipsy-link' )
			.tipsy( {
				title: function() {
					var action = $( this ).data( 'action' );
					return $.articleFeedbackv5special.actions[action].tipsyHtml;
				}
			} )
			.click( $.articleFeedbackv5special.toggleTipsy );
	};

	// }}}

	// }}}
	// {{{ Utility methods

	// {{{ emptyMessage

	/**
	 * Checks if there is feedback loaded and outputs a message if not
	 */
	$.articleFeedbackv5special.emptyMessage = function() {
		var $feedbackContainer = $( '#articleFeedbackv5-show-feedback' );
		if ( $feedbackContainer.children().length == 0 ) {
			var $message =
				$feedbackContainer.append(
					$( '<div id="articlefeedbackv5-no-feedback" />' ).text(
						mw.msg( 'articlefeedbackv5-no-feedback' )
					)
				);
		} else {
			$( '#articlefeedbackv5-no-feedback' ).remove();
		}
	};

	// {{{ toggleFilter

	/**
	 * Toggle on a certain filter
	 * Please note that this will _not_ automatically fetch the new data, which requires a call to loadFeedback
	 *
	 * @param id The id of the filter to be enabled
	 */
	$.articleFeedbackv5special.toggleFilter = function( id ) {
		$.articleFeedbackv5special.listControls.filter = id;
		$.articleFeedbackv5special.listControls.offset = null;
		$.articleFeedbackv5special.setSortByFilter( id );

		// track the filter change
		$.aftTrack.track( 'feedback_page-click-' +
				'f_' + $.articleFeedbackv5special.getFilterName( id ) + '-' +
				$.articleFeedbackv5special.referral + '-' +
				$.articleFeedbackv5special.userType );

		// update filter in select (if present) & text-links (if any)
		$( '#articleFeedbackv5-select-wrapper' ).removeClass( 'filter-active' );
		$( '.articleFeedbackv5-filter-link' ).removeClass( 'filter-active' );
		if ( $( '#articleFeedbackv5-filter-select option[value=' + id + ']' ).length > 0 ) {
			$( '#articleFeedbackv5-select-wrapper' ).addClass( 'filter-active' );
		} else {
			$( '#articleFeedbackv5-filter-select' ).val( '' );
		}
		$( '#articleFeedbackv5-special-filter-' + id).addClass( 'filter-active' );
	};

	// }}}
	// {{{ toggleSort

	/**
	 * Toggle on a certain sort
	 * Please note that this will _not_ automatically fetch the new data, which requires a call to loadFeedback
	 *
	 * @param sort The sorting method
	 * @param direction The direction to sort (asc/desc)
	 */
	$.articleFeedbackv5special.toggleSort = function( sort, direction ) {
		$.articleFeedbackv5special.listControls.sort = sort;
		$.articleFeedbackv5special.listControls.sortDirection = direction;
		$.articleFeedbackv5special.listControls.offset = null;

		$( '#articleFeedbackv5-sort-select' ).val( sort + '-' + direction );
	};

	// }}}
	// {{{ flagAction

	/**
	 * Utility method: Fire flagging-call upon clicking an action link
	 *
	 * @param e event
	 */
	$.articleFeedbackv5special.flagAction = function( e ) {
		e.preventDefault();

		var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
		if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
			var id = $container.data( 'id' );
			var pageId = $container.data( 'pageid' );
			var action = $( e.target ).data( 'action' );

			$.articleFeedbackv5special.flagFeedback(
				id,
				pageId,
				action,
				'',
				{}
			);
		}
	};

	// }}}
	// {{{ toggleTipsy

	/**
	 * Utility method: Toggles tipsy display for an action link
	 *
	 * @param e event
	 * @return true if showing tipsy, false if hiding
	 */
	$.articleFeedbackv5special.toggleTipsy = function( e ) {
		e.preventDefault();

		var $l = $( e.target );

		// are we hiding the current tipsy?
		if ( $l.attr( 'id' ) == $.articleFeedbackv5special.currentPanelHostId ) {
			$l.tipsy( 'hide' );
			$.articleFeedbackv5special.currentPanelHostId = undefined;
			return false;
		} else {
			// no, we're displaying another one
			if ( undefined != $.articleFeedbackv5special.currentPanelHostId ) {
				$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
			}
			$l.tipsy( 'show' );
			$.articleFeedbackv5special.currentPanelHostId = $l.attr( 'id' );
			return true;
		}
	};

	// }}}
	// {{{ stripID

	/**
	 * Utility method: Strips long IDs down to the specific bits we care about
	 */
	$.articleFeedbackv5special.stripID = function( object, toRemove ) {
		return $( object ).attr( 'id' ).replace( toRemove, '' );
	};

	// }}}
	// {{{ getFilterName

	/**
	 * Utility method: Gets the filter name from its internal-use ID
	 *
	 * @param  filter string the internal-use id of the filter
	 * @return string the filter name for use in clicktracking
	 */
	$.articleFeedbackv5special.getFilterName = function ( filter ) {
		return filter;
	};

	// }}}
	// {{{ setSortByFilter

	/**
	 * Utility method: Sets the sort type and direction according to the filter
	 * passed in
	 *
	 * @param filter string the internal-use id of the filter
	 */
	$.articleFeedbackv5special.setSortByFilter = function ( filter ) {
		var shortName = $.articleFeedbackv5special.getFilterName( filter );
		var defaults = mw.config.get( 'wgArticleFeedbackv5DefaultSorts' );
		if ( shortName in defaults ) {
			$.articleFeedbackv5special.toggleSort( defaults[shortName][0], defaults[shortName][1] );
		} else {
			$.articleFeedbackv5special.toggleSort( 'age', 'DESC' );
		}
	};

	// }}}

	// }}}
	// {{{ Process methods

	// {{{ flagFeedback

	/**
	 * Sends the request to mark a response
	 *
	 * @param id		int			the feedback id
	 * @param pageId	int			the page id
	 * @param action	string		action to execute
	 * @param note		string		note for action (default empty)
	 * @param options	object		key => value pairs of additional API action-specific parameters
	 */
	$.articleFeedbackv5special.flagFeedback = function ( id, pageId, action, note, options ) {
		// default parameters
		note = typeof note !== undefined ? note : '';

		if( $.articleFeedbackv5special.listControls.disabled ) {
			return false;
		}

		// This was causing problems with eg 'clicking helpful when the cookie
		// already says unhelpful', which is a case where two ajax requests
		// is perfectly legitimate.
		// Check another global variable to not disable ajax in that case.
		if( !$.articleFeedbackv5special.listControls.allowMultiple ) {
			// Put a lock on ajax requests to prevent another one from going
			// through while this is still running. Prevents manic link-clicking
			// messing up the counts, and generally seems like a good idea.
			$.articleFeedbackv5special.listControls.disabled = true;
		}

		// origin of the flag
		var source = 'unknown';
		if ( $.articleFeedbackv5special.watchlist ) {
			source = 'watchlist';
		} else if ( $.articleFeedbackv5special.listControls.filter == 'id' ) {
			source = 'permalink';
		} else if ( $.articleFeedbackv5special.page ) {
			source = 'article';
		} else {
			source = 'central';
		}

		// Merge request data and options objects (flat)
		var requestData = {
			'pageid'    : pageId,
			'feedbackid': id,
			'flagtype'  : action,
			'note'      : note,
			'source'    : source,
			'format'    : 'json',
			'action'    : 'articlefeedbackv5-flag-feedback'
		};
		// this "options" is currently solely used to add "toggle" to params, when appropriate
		$.extend( requestData, options );

		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'POST',
			'dataType': 'json',
			'data'    : requestData,
			'success': function ( data ) {
				var msg = 'articlefeedbackv5-error-flagging';
				if ( 'articlefeedbackv5-flag-feedback' in data ) {
					if ( 'result' in data['articlefeedbackv5-flag-feedback'] ) {
						if ( data['articlefeedbackv5-flag-feedback'].result == 'Success' ) {
							// replace entry by new render
							$( '.articleFeedbackv5-feedback[data-id='+id+']' )
								.replaceWith( data['articlefeedbackv5-flag-feedback'].render );

							// invoke the registered onSuccess callback for the executed action
							if ( 'onSuccess' in $.articleFeedbackv5special.actions[action] ) {
								$.articleFeedbackv5special.actions[action].onSuccess( id, data );
							}

							// re-mark active flags in reader tools
							$.articleFeedbackv5special.markActiveFlags( id );

							// re-enable ajax flagging
							$.articleFeedbackv5special.listControls.disabled = false;

							// re-bind panels (tipsies)
							$.articleFeedbackv5special.bindTipsies( id );
							return true;
						} else if ( data['articlefeedbackv5-flag-feedback'].result == 'Error' ) {
							mw.log( mw.msg( data['articlefeedbackv5-flag-feedback'].reason ) );
						}
					}
				}

				// re-enable ajax flagging
				$.articleFeedbackv5special.listControls.disabled = false;
			},
			'error': function ( data ) {
				$( '#articleFeedbackv5-' + type + '-link-' + id ).text( mw.msg( 'articlefeedbackv5-error-flagging' ) );

				// re-enable ajax flagging
				$.articleFeedbackv5special.listControls.disabled = false;
			}
		} );
		return false;
	};

	// {{{ addNote

	/**
	 * Updates the previous flag with a textual comment about it
	 *
	 * @param id		int			the feedback id
	 * @param logId		int			the log id
	 * @param note		string		note for action (default empty)
	 */
	$.articleFeedbackv5special.addNote = function ( id, logId, note ) {
		// note should be filled out or there's no point in firing this request
		if ( !note ) {
			return false;
		}

		if ( $.articleFeedbackv5special.listControls.disabled ) {
			return false;
		}
		$.articleFeedbackv5special.listControls.disabled = true;

		// Merge request data and options objects (flat)
		var requestData = {
			'logid'     : logId,
			'note'      : note,
			'format'    : 'json',
			'action'    : 'articlefeedbackv5-add-flag-note'
		};

		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'POST',
			'dataType': 'json',
			'data'    : requestData,
			'success': function ( data ) {
				if ( 'articlefeedbackv5-add-flag-note' in data ) {
					if ( 'result' in data['articlefeedbackv5-add-flag-note'] ) {
						if ( data['articlefeedbackv5-add-flag-note'].result == 'Success' ) {
							// remove "add note" link
							$( '#articleFeedbackv5-note-link-' + id ).remove();

							// change text to reflect note has been entered
							$( '#articleFeedbackv5-activity-link-' + id )
								.removeClass( 'activity-empty' )
								.text( mw.msg( 'articlefeedbackv5-viewactivity' ) );

							// re-enable ajax flagging
							$.articleFeedbackv5special.listControls.disabled = false;

							return true;
						}
					}
				}

				// re-enable ajax flagging
				$.articleFeedbackv5special.listControls.disabled = false;
			},
			'error': function ( data ) {
				// re-enable ajax flagging
				$.articleFeedbackv5special.listControls.disabled = false;
			}
		} );

		return false;
	};

	// }}}
	// {{{ loadActivityLog

	/**
	 * Load the activity log for a feedback post item
	 *
	 * @param id           int    feedback post item id
	 * @param continueInfo string should be null for the first request (first page), then the continue info returned from the last API call
	 * @param location     string where to put the results
	 */
	$.articleFeedbackv5special.loadActivityLog = function( id, pageId, continueInfo, location ) {
		var data = {
			'action': 'query',
			'list': 'articlefeedbackv5-view-activity',
			'format': 'json',
			'aafeedbackid': id,
			'aapageid': pageId
		};
		if ( continueInfo ) {
			data['aacontinue'] = continueInfo;
		}
		if ( location == '#articleFeedbackv5-permalink-activity-log' ) {
			data['aanoheader'] = true;
		}
		$.ajax( {
			'url': $.articleFeedbackv5special.apiUrl,
			'type': 'GET',
			'dataType': 'json',
			'data': data,
			'context': { location: location },
			'success': function( data ) {
				if ( data['articlefeedbackv5-view-activity'].hasHeader ) {
					$( location ).html( data['articlefeedbackv5-view-activity'].activity );
				} else {
					var $place = $( location ).find( '.articleFeedbackv5-activity-more' );
					if ( $place.length > 0 ) {
						$place.replaceWith( data['articlefeedbackv5-view-activity'].activity );
					} else {
						$( location ).html( data['articlefeedbackv5-view-activity'].activity );
					}
				}
				if ( data['query-continue'] && data['query-continue']['articlefeedbackv5-view-activity'] ) {
					$( location ).find( '.articleFeedbackv5-activity-more' )
						.data( 'continue', data['query-continue']['articlefeedbackv5-view-activity'].aacontinue )
						.click( function( e ) {
							e.preventDefault();
							$.articleFeedbackv5special.loadActivityLog(
								id,
								pageId,
								$( e.target ).data( 'continue' ),
								location
							);
						} );
				}
			},
			'error': function( data ) {
				// FIXME this messages isn't defined
				$( location ).text( mw.msg( 'articleFeedbackv5-view-activity-error' ) );
			}
		} );

		return false;
	};

	// }}}
	// {{{ loadFeedback

	/**
	 * Pulls in a set of responses.
	 *
	 * When a next-page load is requested, it appends the new responses; on a
	 * sort or filter change, the existing responses are removed from the view
	 * and replaced.
	 *
	 * @param resetContents   bool whether to remove the existing responses
	 * @param prependContents bool whether to prepend the results onto the existing feedback
	 */
	$.articleFeedbackv5special.loadFeedback = function ( resetContents, prependContents ) {
		// save this filter state
		$.articleFeedbackv5special.saveFilters();

		if ( resetContents || prependContents ) {
			$( '#articleFeedbackv5-feedback-loading-top' ).fadeIn();
		} else {
			$( '#articleFeedbackv5-feedback-loading-bottom' ).fadeIn();
		}
		var params = {
			'afvfpageid':         $.articleFeedbackv5special.page,
			'afvffilter':         $.articleFeedbackv5special.listControls.filter,
			'afvffeedbackid':     $.articleFeedbackv5special.listControls.feedbackId,
			'afvfsort':           $.articleFeedbackv5special.listControls.sort,
			'afvfsortdirection':  $.articleFeedbackv5special.listControls.sortDirection,
			'afvfoffset':         $.articleFeedbackv5special.listControls.offset,
			'afvfwatchlist':      $.articleFeedbackv5special.watchlist,
			'action':             'query',
			'format':             'json',
			'list':               'articlefeedbackv5-view-feedback',
			'maxage':             0
		};
		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'GET',
			'dataType': 'json',
			'data'    : params,
			'context': { info: params },
			'success': function ( data ) {
				if ( 'articlefeedbackv5-view-feedback' in data ) {
					if ( resetContents ) {
						$( '#articleFeedbackv5-show-feedback' ).empty();
					}
					if ( prependContents ) {
						$( '#articleFeedbackv5-show-feedback' ).prepend( data['articlefeedbackv5-view-feedback'].feedback );
					} else {
						$( '#articleFeedbackv5-show-feedback' ).append( data['articlefeedbackv5-view-feedback'].feedback );
					}
					if ( $.articleFeedbackv5special.highlightId ) {
						if (this.info.afvffeedbackid == $.articleFeedbackv5special.highlightId ) {
							$( '.articleFeedbackv5-feedback[data-id=' + $.articleFeedbackv5special.highlightId + ']:not(.articleFeedbackv5-feedback-highlighted)' ).hide();
							$.articleFeedbackv5special.highlightId = undefined;
						} else if ( !prependContents ) {
							$.articleFeedbackv5special.pullHighlight();
						}
					} else {
						$.articleFeedbackv5special.processControls(
							data['articlefeedbackv5-view-feedback']['count'],
							data['articlefeedbackv5-view-feedback']['filtercount'],
							data['articlefeedbackv5-view-feedback']['offset'],
							data['articlefeedbackv5-view-feedback']['more']
						);
					}
					$.articleFeedbackv5special.processFeedback();
				} else {
					$( '#articleFeedbackv5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				}
				if ( resetContents || prependContents ) {
					$( '#articleFeedbackv5-feedback-loading-top' ).fadeOut();
				} else {
					$( '#articleFeedbackv5-feedback-loading-bottom' ).fadeOut();
				}

				$.articleFeedbackv5special.emptyMessage();
			},
			'error': function ( data ) {
				$( '#articleFeedbackv5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				if ( resetContents || prependContents ) {
					$( '#articleFeedbackv5-feedback-loading-top' ).fadeOut();
				} else {
					$( '#articleFeedbackv5-feedback-loading-bottom' ).fadeOut();
				}
			}
		} );

		return false;
	};

	// }}}
	// {{{ pullHighlight

	/**
	 * Pulls in the highlighted feedback, if requested.
	 */
	$.articleFeedbackv5special.pullHighlight = function () {
		var old = {
			filter:         $.articleFeedbackv5special.listControls.filter,
			feedbackId:     $.articleFeedbackv5special.listControls.feedbackId,
			sort:           $.articleFeedbackv5special.listControls.sort,
			sortDirection:  $.articleFeedbackv5special.listControls.sortDirection,
			offset:         $.articleFeedbackv5special.listControls.offset,
			disabled:       $.articleFeedbackv5special.listControls.disabled,
			allowMultiple:  $.articleFeedbackv5special.listControls.allowMultiple,
			showMore:       $.articleFeedbackv5special.listControls.showMore
		};
		$.articleFeedbackv5special.listControls.feedbackId = $.articleFeedbackv5special.highlightId;
		$.articleFeedbackv5special.loadFeedback( false, true );
		for ( var key in old ) {
			$.articleFeedbackv5special.listControls[key] = old[key];
		}
	};

	// }}}
	// {{{ processControls

	/**
	 * Processes the controls of a set of responses
	 *
	 * @param count        int   the total number of responses
	 * @param filtercount  int   the number of responses for "featured" filter
	 * @param offset       index the offset
	 * @param showMore     bool  whether there are more records to show
	 */
	$.articleFeedbackv5special.processControls = function ( count, filtercount, offset, showMore ) {
		$( '#articleFeedbackv5-feedback-count-total' ).text( count );
		$( '#articleFeedbackv5-feedback-count-filter' ).text( filtercount );
		$.articleFeedbackv5special.listControls.offset = offset;
		if ( showMore ) {
			$( '#articleFeedbackv5-show-more').show();
		} else {
			$( '#articleFeedbackv5-show-more').hide();
		}
	};

	// }}}
	// {{{ markActiveFlags

	/**
	 * Mark reader tools als active when they've been flagged
	 * by this user already
	 */
	$.articleFeedbackv5special.markActiveFlags = function ( id ) {
		/*
		 * If the user already flagged as helpful/unhelpful, mark the
		 * button as active and change the action to undo-(un)helpful.
		 */
		if ( $.articleFeedbackv5special.getActivityFlag( id, 'helpful' ) ) {
			$( '#articleFeedbackv5-helpful-link-' + id )
				.addClass( 'helpful-active' )
				.data( 'action', 'undo-helpful' );
		} else if ( $.articleFeedbackv5special.getActivityFlag( id, 'unhelpful' ) ) {
			$( '#articleFeedbackv5-unhelpful-link-' + id )
				.addClass( 'helpful-active' )
				.data( 'action', 'undo-unhelpful' );
		}

		/**
		 * If the user already flagged as abusive, change the text to
		 * reflect this and change the action to unflag.
		 */
		if ( $.articleFeedbackv5special.getActivityFlag( id, 'flag' ) ) {
			$( '#articleFeedbackv5-flag-link-' + id )
				.text( mw.msg( 'articlefeedbackv5-abuse-saved' ) )
				.data( 'action', 'unflag' );
		}

		/**
		 * If the user already requested oversight, change action to unoversight.
		 */
		if ( $.articleFeedbackv5special.getActivityFlag( id, 'request' ) ) {
			var $link = $( '#articleFeedbackv5-request-link-' + id );

			if ( !$link.hasClass( 'inactive' ) ) {
				// oversight has been request: turn link into unrequest
				$link
					.text( mw.msg( 'articlefeedbackv5-form-unrequest' ) )
					.data( 'action', 'unrequest' )
					.removeClass( 'articleFeedbackv5-request-link' )
					.addClass( 'articleFeedbackv5-unrequest-link' );
			} else {
				// oversight request has been declined - mark as such
				$link
					.text( mw.msg( 'articlefeedbackv5-form-declined' ) );
			}
		}
	};

	// }}}
	// {{{ processFeedback

	/**
	 * Processes in a set of responses
	 */
	$.articleFeedbackv5special.processFeedback = function () {
		var $newList = $( '#articleFeedbackv5-show-feedback' );
		$newList.find( '.articleFeedbackv5-feedback' ).each( function () {
			var id = $( this ).data( 'id' );

			$.articleFeedbackv5special.markActiveFlags( id );
		} );

		$.articleFeedbackv5special.bindTipsies();
	};

	// }}}
	// {{{ getActivity

	/**
	 * Utility method: Gets the activity for a feedback ID
	 *
	 * @param  fid    int the feedback ID
	 * @param  action string the action
	 * @return bool   true if action executed by user, false if not
	 */
	$.articleFeedbackv5special.getActivityFlag = function ( fid, action ) {
		if ( fid in $.articleFeedbackv5special.activity && action in $.articleFeedbackv5special.activity[fid] ) {
			return $.articleFeedbackv5special.activity[fid][action];
		}
		return false;
	};

	// }}}
	// {{{ setActivityFlag

	/**
	 * Utility method: Sets an activity flag
	 *
	 * @param id    string the feedback id
	 * @param flag  string the flag name
	 * @param value string the value
	 */
	$.articleFeedbackv5special.setActivityFlag = function( fid, flag, value ) {
		if ( !( fid in $.articleFeedbackv5special.activity ) ) {
			$.articleFeedbackv5special.activity[fid] = [];
		}
		$.articleFeedbackv5special.activity[fid][flag] = value;
		$.articleFeedbackv5special.storeActivity();
	};

	// }}}
	// {{{ loadActivity

	/**
	 * Loads the user activity from the cookie
	 */
	$.articleFeedbackv5special.loadActivity = function () {
		var flatActivity = $.cookie( mw.config.get( 'wgCookiePrefix' ) + $.aftUtils.getCookieName( $.articleFeedbackv5special.activityCookieName ) );
		if ( flatActivity ) {
			// get "indexes" for each action - shorter than the action name string
			var actions = [];
			for ( var action in $.articleFeedbackv5special.actions ) {
				actions.push( action );
			}

			var flatActivity = flatActivity.split( '|' );
			for ( var i in flatActivity ) {
				var parts = flatActivity[i].split( ':' );
				var fid = parts[0];
				var indexes = parts[1].split( ',' );

				$.articleFeedbackv5special.activity[fid] = [];
				for ( var i in indexes ) {
					action = actions[indexes[i]];

					$.articleFeedbackv5special.activity[fid][action] = true;
				}
			}
		}
	};

	// }}}
	// {{{ storeActivity

	/**
	 * Stores the user activity to the cookie
	 * A person's own activity will be saved in a cookie; since there
	 * can be quite a lot of activity on quite a lot of feedback, let's
	 * make the value to be saved to the cookie rather short.
	 * The result will look like: '143:1,5|342:3'
	 */
	$.articleFeedbackv5special.storeActivity = function () {
		// get "indexes" for each action - shorter than the action name string
		var actions = [];
		for ( var action in $.articleFeedbackv5special.actions ) {
			actions.push( action );
		}

		var flatActivity = [];
		for ( var fid in $.articleFeedbackv5special.activity ) {
			var indexes = [];
			for ( var action in $.articleFeedbackv5special.activity[fid] ) {
				var index = actions.indexOf( action );

				// only save if action is known & true
				if ( $.articleFeedbackv5special.activity[fid][action] && index > -1 ) {
					indexes.push( index );
				}
			}

			if ( indexes.length > 0 ) {
				flatActivity.push( fid + ':' + indexes.join( ',' ) );
			}
		}

		// only the most recent 100 are of interest
		flatActivity = flatActivity.splice( -100 );

		$.cookie(
			mw.config.get( 'wgCookiePrefix' ) + $.aftUtils.getCookieName( $.articleFeedbackv5special.activityCookieName ),
			flatActivity.join( '|' ),
			{ 'expires': 365, 'path': '/' }
		);
	};

	// }}}
	// {{{ canBeFlagged

	/**
	 * Checks if a post can be flagged: post is not hidden/oversighted
	 * or user had permissions to (un)hide/(un)oversight
	 *
	 * @return bool true if post can be flagged, or false otherwise
	 */
	$.articleFeedbackv5special.canBeFlagged = function( $post ) {
		return $post.find( '.articleFeedbackv5-post-screen' ).length == 0 ||
			mw.config.get( 'wgArticleFeedbackv5Permissions' )['aft-editor'];
	};

	// }}}
	// {{{ saveFilters

	/**
	 * Saves the filters' current state to a cookie
	 */
	$.articleFeedbackv5special.saveFilters = function () {
		// don't save on permalink page
		if ( $.articleFeedbackv5special.listControls.filter == 'id' ) {
			return false;
		}

		// stringify filters data
		var filterParams = {
			'page': $.articleFeedbackv5special.page,
			'listControls': $.articleFeedbackv5special.listControls
		};
		filterParams = $.toJSON( filterParams );

		// note: we're overwriting the same cookie for every page; assuming that they won't like to come
		// back later to previous pages and find their previous settings again (plus less cookie size)
		$.cookie(
			mw.config.get( 'wgCookiePrefix' ) + $.aftUtils.getCookieName( $.articleFeedbackv5special.filterCookieName ),
			filterParams,
			{ 'expires': 1, 'path': '/' }
		);
	};

	// }}}

	// }}}
	// {{{ Actions

	/**
	 * Actions - available actions on the page.
	 *
	 * Each action is an object with the following properties:
	 * 		hasTipsy - true if the action needs a flyover panel
	 * 		tipsyHtml - html for the corresponding flyover panel
	 * 		click - click action
	 * 		onSuccess - callback to execute after action success. Callback parameters:
	 * 			id - respective post id
	 * 			data - any data returned by the AJAX call
	 */
	$.articleFeedbackv5special.actions = {

		// {{{ Vote helpful

		'helpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();

				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					var pageId = $container.data( 'pageid' );
					var action = $( e.target ).data( 'action' );

					$.articleFeedbackv5special.flagFeedback(
						id,
						pageId,
						action,
						'',
						{ toggle: $.articleFeedbackv5special.getActivityFlag( id, 'unhelpful' ) }
					);
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'helpful', true );
				$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', false )
			}
		},

		// }}}
		// {{{ Un-vote helpful

		'undo-helpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();

				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					var pageId = $container.data( 'pageid' );
					var action = $( e.target ).data( 'action' );

					$.articleFeedbackv5special.flagFeedback(
						id,
						pageId,
						action,
						'',
						{}
					);
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'helpful', false );
				$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', false )
			}
		},

		// }}}
		// {{{ Vote unhelpful

		'unhelpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();

				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					var pageId = $container.data( 'pageid' );
					var action = $( e.target ).data( 'action' );

					$.articleFeedbackv5special.flagFeedback(
						id,
						pageId,
						action,
						'',
						{ toggle: $.articleFeedbackv5special.getActivityFlag( id, 'helpful' ) }
					);
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'helpful', false );
				$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', true )
			}
		},

		// }}}
		// {{{ Un-vote unhelpful

		'undo-unhelpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();

				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					var pageId = $container.data( 'pageid' );
					var action = $( e.target ).data( 'action' );

					$.articleFeedbackv5special.flagFeedback(
						id,
						pageId,
						action,
						'',
						{}
					);
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'helpful', false );
				$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', false )
			}
		},

		// }}}
		// {{{ Flag post as abusive

		'flag': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();

				// only allow flagging if not yet flagged
				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					if ( !$.articleFeedbackv5special.getActivityFlag( id, 'flag' ) ) {
						var pageId = $container.data( 'pageid' );
						var action = $( e.target ).data( 'action' );

						$.articleFeedbackv5special.flagFeedback(
							id,
							pageId,
							action,
							'',
							{}
						);
					}
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'flag', true );
			}
		},

		// }}}
		// {{{ Unflag post as abusive

		'unflag': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();

				// only allow unflagging if flagged by this user
				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					if ( $.articleFeedbackv5special.getActivityFlag( id, 'flag' ) ) {
						var pageId = $container.data( 'pageid' );
						var action = $( e.target ).data( 'action' );

						$.articleFeedbackv5special.flagFeedback(
							id,
							pageId,
							action,
							'',
							{}
						);
					}
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'flag', false );
			}
		},

		// }}}
		// {{{ Feature post action

		'feature': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Un-feature post action

		'unfeature': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Mark resolved post action

		'resolve': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Unmark as resolved post action

		'unresolve': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Mark post as non-actionable action

		'noaction': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Unmark post as non-actionable action

		'unnoaction': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Hide post action

		'hide': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Show post action

		'unhide': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Request oversight action

		'request': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': function() {
				// tipsy has been opened - bind flag submission

				$.articleFeedbackv5special.tipsyCallback = function( e ) {
					var $container = $( '#' + $.articleFeedbackv5special.currentPanelHostId ).closest( '.articleFeedbackv5-feedback' );
					if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
						var id = $container.data( 'id' );
						var pageId = $container.data( 'pageid' );
						var note = $( '#articleFeedbackv5-noteflyover-note' ).val();

						$.articleFeedbackv5special.flagFeedback(
							id,
							pageId,
							'request',
							note,
							{}
						);
					}
				};
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'request', true );
			}
		},

		// }}}
		// {{{ Cancel oversight request action

		'unrequest': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': function( e ) {
				e.preventDefault();

				// only allow unrequesting if requested by this user
				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
					var id = $container.data( 'id' );
					if ( $.articleFeedbackv5special.getActivityFlag( id, 'request' ) ) {
						var pageId = $container.data( 'pageid' );
						var action = $( e.target ).data( 'action' );

						$.articleFeedbackv5special.flagFeedback(
							id,
							pageId,
							action,
							'',
							{}
						);
					}
				}
			},
			'onSuccess': function( id, data ) {
				$.articleFeedbackv5special.setActivityFlag( id, 'request', false );
			}
		},

		// }}}
		// {{{ Oversight post action

		'oversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': function() {
				// tipsy has been opened - bind flag submission

				$.articleFeedbackv5special.tipsyCallback = function( e ) {
					var $container = $( '#' + $.articleFeedbackv5special.currentPanelHostId ).closest( '.articleFeedbackv5-feedback' );
					if ( $.articleFeedbackv5special.canBeFlagged( $container ) ) {
						var id = $container.data( 'id' );
						var pageId = $container.data( 'pageid' );
						var note = $( '#articleFeedbackv5-noteflyover-note' ).val();

						$.articleFeedbackv5special.flagFeedback(
							id,
							pageId,
							'oversight',
							note,
							{}
						);
					}
				};
			},
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Un-oversight action

		'unoversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		// }}}
		// {{{ Decline oversight action

		'decline': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.flagAction,
			'onSuccess': function( id, data ) {
				// activity flag is not particularly useful here
			}
		},

		/*
		 * Well, those below are not really "actions", but we can use the
		 * structure that has been setup for actions, for these as well ;)
		 */

		// }}}
		// {{{ View activity log action

		'activity': {
			'hasTipsy': true,
			'tipsyHtml': '\
				<div>\
					<div class="articleFeedbackv5-flyover-header">\
						<h3 id="articleFeedbackv5-noteflyover-caption"><html:msg key="activity-pane-header" /></h3>\
						<a id="articleFeedbackv5-noteflyover-helpbutton" href="#"></a>\
						<a id="articleFeedbackv5-noteflyover-close" href="#"></a>\
					</div>\
					<div id="articleFeedbackv5-activity-log"></div>\
				</div>',
			'click': function( e ) {
				// upon executing this, tipsy will be open already
				var $container = $( e.target ).closest( '.articleFeedbackv5-feedback' );
				$.articleFeedbackv5special.loadActivityLog( $container.data( 'id' ), $container.data( 'pageid' ), 0, '#articleFeedbackv5-activity-log' );
			}
		},

		// }}}
		// {{{ View activity log action on permalink

		'activity2': {
			'click': function( e ) {
				e.preventDefault();

				if ( $( e.target ).data( 'started' ) == true ) {
					$( '#articleFeedbackv5-permalink-activity-log' ).fadeOut();
					$( e.target ).text( mw.msg( 'articlefeedbackv5-permalink-activity-more' ) );
					$( e.target ).data( 'started', false );
				} else {
					$container = $( '#articleFeedbackv5-show-feedback .articleFeedbackv5-feedback' );
					$.articleFeedbackv5special.loadActivityLog( $container.data( 'id' ), $container.data( 'pageid' ), 0, '#articleFeedbackv5-permalink-activity-log' );
					$( '#articleFeedbackv5-permalink-activity-log' ).fadeIn();
					$( e.target ).text( mw.msg( 'articlefeedbackv5-permalink-activity-fewer' ) );
					$( e.target ).data( 'started', true );
				}
			}
		}

		// }}}

	};

	// }}}

// }}}

} )( jQuery );

