/**
 * ArticleFeedback special page
 *
 * This file handles the display of feedback responses and moderation tools for
 * privileged users.  The flow goes like this:
 *
 * User arrives at special page -> basic markup is created without data -> ajax
 * request is sent to pull most recent feedback
 *
 * For each change to the selected filter or sort method, or when more feedback
 * is requested, another ajax request is sent.
 *
 * This file is long, so it's commented with manual fold markers.  To use folds
 * this way in vim:
 *   set foldmethod=marker
 *   set foldlevel=0
 *   set foldcolumn=0
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Greg Chiasson <gchiasson@omniti.com>
 * @version    $Id$
 */

( function ( $ ) {

// {{{ articleFeedbackv5special definition

	// TODO: jam sort/filter options into URL anchors, and use them as defaults if present.

	$.articleFeedbackv5special = {};

	// {{{ Properties

	/**
	 * What page is this?
	 */
	$.articleFeedbackv5special.page = undefined;

	/**
	 * The url to which to send the pull request
	 */
	$.articleFeedbackv5special.apiUrl = undefined;

	/**
	 * Controls for the list: sort, filter, continue flag, etc
	 */
	$.articleFeedbackv5special.listControls = {
		filter: 'comment',
		filterValue: undefined, // Permalinks require a feedback ID
		sort: 'age',
		sortDirection: 'desc',
		limit: 25,
		continue: null,
		continueId: null, // Sort of a tie-breaker for continue values.
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
	 * User activity cookie name (page id is appended on init)
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.activityCookieName = 'activity-';
	
	/**
	 * Currently displayed panel host element id attribute value
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.currentPanelHostId = undefined;
	
	/**
	 * Action note flyover panel HTML template
	 *
	 * @var string
	 */
	$.articleFeedbackv5special.notePanelHtmlTemplate = '\
		<div class="articlefeedbackv5-flyover-header">\
			<h3 id="articlefeedbackv5-noteflyover-caption">' + mw.msg( 'articlefeedbackv5-noteflyover-hide-caption' ) + '</h3>\
			<a id="articlefeedbackv5-noteflyover-close" href="#"></a>\
		</div>\
		<form class="articlefeedbackv5-form-flyover">\
			<label id="articlefeedbackv5-noteflyover-label" for="articlefeedbackv5-noteflyover-note">'
				+ mw.msg( 'articlefeedbackv5-noteflyover-hide-label' ) +
			'</label>\
			<textarea id="articlefeedbackv5-noteflyover-note" name="articlefeedbackv5-noteflyover-note"></textarea>\
			<div class="articlefeedbackv5-flyover-footer">\
				<a id="articlefeedbackv5-noteflyover-submit" class="articlefeedbackv5-flyover-button" href="#">'
					+ mw.msg( 'articlefeedbackv5-noteflyover-hide-submit' ) +
				'</a>\
				<a class="articlefeedbackv5-flyover-help" id="articlefeedbackv5-noteflyover-help" href="'
					+ mw.msg( 'articlefeedbackv5-noteflyover-hide-help-link' ) + '">'
					+ mw.msg( 'articlefeedbackv5-noteflyover-hide-help' ) + 
				'</a>\
			</div>\
		</form>';
				
	/**
	 * Formatted and localized flyover panel HTMLs. These are initialized once on page setup.
	 *
	 * @var object
	 */
	$.articleFeedbackv5special.notePanelHtml = {
		'hide': undefined,
		'show': undefined,
		'requestoversight': undefined,
		'unrequestoversight': undefined,
		'oversight': undefined,
		'declineoversight': undefined,
		'unoversight': undefined
	};
				
	/**
	 * Activity log flyover panel HTML
	 * 
	 * @var string
	 */
	$.articleFeedbackv5special.logPanelHtml = '\
		<div>\
			<div class="articlefeedbackv5-flyover-header">\
				<h3>Activity log</h3>\
				<a>HELP</a>\
				<a>CLOSE</a>\
			</div>\
			<div>SOME ACTIVITY HERE\
			</div>\
			<div class="articlefeedbackv5-activityflyover-viewactivity">FOOTER HERE\
			</div>\
		</div>';

	// }}}
	// {{{ Init methods

	// {{{ setup

	/**
	 * Sets up the page
	 */
	$.articleFeedbackv5special.setup = function() {
		// Set up config vars, event binds, and do initial fetch.
		$.articleFeedbackv5special.apiUrl = mw.util.wikiScript( 'api' );
		$.articleFeedbackv5special.page = mw.config.get( 'afPageId' );
		$.articleFeedbackv5special.setBinds();

		// Process anything we found in the URL hash
		// Permalinks.
		var id = window.location.href.match(/\/(\d+)$/)
		if( id ) {
			$.articleFeedbackv5special.listControls.filter      = 'id';
			$.articleFeedbackv5special.listControls.filterValue = id[1];
		}

		// Bold the default sort, hide arrows
		$( '#articleFeedbackv5-special-sort-age' ).addClass( 'sort-active' );
		$( '.articleFeedbackv5-sort-arrow').hide();

		// Grab the user's activity out of the cookie
		$.articleFeedbackv5special.activityCookieName += $.articleFeedbackv5special.page;
		$.articleFeedbackv5special.loadActivity();

		// initialize flyover panels for actions	
		$.articleFeedbackv5special.initFlyoverPanels();
		
		// Initial load
		$.articleFeedbackv5special.loadFeedback( true );
	};

	// }}}
	// {{{ initFlyoverPanels
	
	/**
	 * Initialize the action note flyover panels
	 */
	$.articleFeedbackv5special.initFlyoverPanels = function() {
		// set tipsy defaults, once
		$.fn.tipsy.defaults = {
			delayIn: 0,				// delay before showing tooltip (ms)
			delayOut: 0,			// delay before hiding tooltip (ms)
			fade: false,			// fade tooltips in/out?
			fallback: '',			// fallback text to use when no tooltip text
			gravity: 'e',			// gravity
			html: true,				// is tooltip content HTML?
			live: false,			// use live event support?
			offset: 10,				// pixel offset of tooltip from element
			opacity: 1.0,			// opacity of tooltip
			title: 'title',			// attribute/callback containing tooltip text
			trigger: 'manual'		// how tooltip is triggered - hover | focus | manual
		};
		
		// i18n, create specific panels from template
		var container = $( '<div></div>' );
		container.html( $.articleFeedbackv5special.notePanelHtmlTemplate );
		for( var action in $.articleFeedbackv5special.notePanelHtml ) {
			container.find( '#articlefeedbackv5-noteflyover-caption' ).html( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-caption' ) );
			container.find( '#articlefeedbackv5-noteflyover-label' ).html( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-label' ) );
			container.find( '#articlefeedbackv5-noteflyover-submit' ).html( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-submit' ) );
			container.find( '#articlefeedbackv5-noteflyover-submit' ).attr( 'rel', action );
			container.find( '#articlefeedbackv5-noteflyover-help' ).html( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-help' ) );
			container.find( '#articlefeedbackv5-noteflyover-help' ).attr( 'href', mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-help-link' ) );
			$.articleFeedbackv5special.notePanelHtml[action] = container.html();
		}
	}
	
	// }}}
	// {{{ setBinds

	/**
	 * Binds events for each of the controls
	 */
	$.articleFeedbackv5special.setBinds = function() {
		$( '#articleFeedbackv5-filter-select' ).bind( 'change', function( e ) {
			$.articleFeedbackv5special.listControls.filter     = $(this).val();
			$.articleFeedbackv5special.listControls.continue   = null;
			$.articleFeedbackv5special.listControls.continueId = null;
			$.articleFeedbackv5special.loadFeedback( true );
			return false;
		} );

		$( '.articleFeedbackv5-sort-link' ).bind( 'click', function( e ) {
			var	id     = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-special-sort-' ),
				oldId  = $.articleFeedbackv5special.listControls.sort;

			// set direction = desc...
			$.articleFeedbackv5special.listControls.sort       = id;
			$.articleFeedbackv5special.listControls.continue   = null;
			$.articleFeedbackv5special.listControls.continueId = null;

			// unless we're flipping the direction on the current sort.
			if( id == oldId && $.articleFeedbackv5special.listControls.sortDirection == 'desc' ) {
				$.articleFeedbackv5special.listControls.sortDirection = 'asc';
			}  else {
				$.articleFeedbackv5special.listControls.sortDirection = 'desc';
			}

			$.articleFeedbackv5special.loadFeedback( true );
			// draw arrow and load feedback posts
			$.articleFeedbackv5special.drawSortArrow();

			return false;
		} );

		$( '#articleFeedbackv5-show-more' ).bind( 'click', function( e ) {
			$.articleFeedbackv5special.loadFeedback( false );
			return false;
		} );

		$( '.articleFeedbackv5-permalink' ).live( 'click', function( e ) {
			var id = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-permalink-' );
			$.articleFeedbackv5special.listControls.filter      = 'id';
			$.articleFeedbackv5special.listControls.filterValue = id;
			$.articleFeedbackv5special.listControls.continue    = null;
			$.articleFeedbackv5special.listControls.continueId  = null;
			$.articleFeedbackv5special.loadFeedback( true );
		} );

		$( '.articleFeedbackv5-comment-toggle' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.toggleComment( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-comment-toggle-' ) );
			return false;
		} );

		// Helpful and unhelpful
		$.each( ['helpful', 'unhelpful' ], function ( index, value ) { 
			$( '.articleFeedbackv5-' + value + '-link' ).live( 'click', function( e ) {
				e.preventDefault();
				var $l = $( e.target );
				if ( $l.closest( '.articleFeedbackv5-feedback' ).data( 'hidden' )
					|| $l.closest( '.articleFeedbackv5-feedback' ).data( 'deleted' ) ) {
					return false;
				}
				var id = $l.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
				var activity = $.articleFeedbackv5special.getActivity( id );
				if ( activity[value] ) {
					$.articleFeedbackv5special.flagFeedback( id, value, -1 );
				} else if ( 'helpful' == value && activity.unhelpful ) {
					// Allow multiple simultaneous ajax requests in this case.
					$.articleFeedbackv5special.listControls.allowMultiple = true;
					$.articleFeedbackv5special.flagFeedback( id, 'unhelpful', -1 );
					$.articleFeedbackv5special.flagFeedback( id, 'helpful', 1 );
					$.articleFeedbackv5special.listControls.allowMultiple = false;
				} else if ( 'unhelpful' == value && activity.helpful ) {
					// Allow multiple simultaneous ajax requests in this case.
					$.articleFeedbackv5special.listControls.allowMultiple = true;
					$.articleFeedbackv5special.flagFeedback( id, 'helpful', -1 );
					$.articleFeedbackv5special.flagFeedback( id, 'unhelpful', 1 );
					$.articleFeedbackv5special.listControls.allowMultiple = false;
				} else {
					$.articleFeedbackv5special.flagFeedback( id, value, 1 );
				}
			} )
		} );

		// Flag/Unflag as abuse
		$( '.articleFeedbackv5-abuse-link' ).live( 'click', function( e ) {
			e.preventDefault();
			var $l = $( e.target );
			if ( $l.closest( '.articleFeedbackv5-feedback' ).data( 'hidden' )
				|| $l.closest( '.articleFeedbackv5-feedback' ).data( 'deleted' ) ) {
				return false;
			}
			var id = $l.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
			var activity = $.articleFeedbackv5special.getActivity( id );
			if ( activity.abuse ) {
				$.articleFeedbackv5special.flagFeedback( id, 'abuse', -1 );
			} else {
				$.articleFeedbackv5special.flagFeedback( id, 'abuse', 1 );
			}
		} );

		// Prepare an array of actions to bind
		var actions = ['activity'];
		for( var action in $.articleFeedbackv5special.notePanelHtml ) { actions.push(action); }
		// Bind tipsies
		for( var index in actions ) {
			var action = actions[index];
			$( '.articleFeedbackv5-' + action + '-link' ).live( 'click', function( e ) {
				e.preventDefault();
				var $l = $( e.target );
				// are we hiding the current tipsy?
				if( $l.attr( 'id' ) == $.articleFeedbackv5special.currentPanelHostId ) {
					$l.tipsy( 'hide' );
					$.articleFeedbackv5special.currentPanelHostId = undefined;
				} else {
					// no, we're displaying another one
					if( undefined != $.articleFeedbackv5special.currentPanelHostId ) {
						$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
					}
					$l.tipsy( 'show' );
					$.articleFeedbackv5special.currentPanelHostId= $l.attr( 'id' );
				}
			} );
		}
		
		// Bind submit actions on flyover panels
		$( '#articlefeedbackv5-noteflyover-submit' ).live( 'click', function( e ) {
			e.preventDefault();
			var $l = $( '#' + $.articleFeedbackv5special.currentPanelHostId );
			var action = $( e.target ).attr( 'rel' );
			var id = $l.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
			var activity = $.articleFeedbackv5special.getActivity( id );
			var note = $( '#articlefeedbackv5-noteflyover-note' ).attr( 'value' );
			switch( action ) {
				case 'hide':
					$.articleFeedbackv5special.flagFeedback( id, 'hide', 1, note );
					break;
				case 'show':
					$.articleFeedbackv5special.flagFeedback( id, 'hide', -1, note );
					break;
				case 'oversight':
					$.articleFeedbackv5special.flagFeedback( id, 'delete', 1, note );
					break;
				case 'unoversight':
					$.articleFeedbackv5special.flagFeedback( id, 'delete', -1, note );
					break;
				case 'declineoversight':
					$.articleFeedbackv5special.flagFeedback( id, 'resetdelete', 1, note );
					break;
				case 'requestoversight':
					$.articleFeedbackv5special.flagFeedback( id, 'oversight', 1, note );
					break;
				case 'unrequestoversight':
					$.articleFeedbackv5special.flagFeedback( id, 'oversight', -1, note );
					break;
			}
			// hide tipsy
			$l.tipsy( 'hide' );
			$.articleFeedbackv5special.currentPanelHostId = undefined;
		} );
		
		// Delete/Undelete this post
		/*$( '.articleFeedbackv5-delete-link' ).live( 'click', function( e ) {
			e.preventDefault();
			var $l = $( e.target );
			var id = $l.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
			var activity = $.articleFeedbackv5special.getActivity( id );
			if ( activity.delete 
			  || $( e.target ).text() == mw.msg('articlefeedbackv5-form-undelete') 
			) {
				$.articleFeedbackv5special.flagFeedback( id, 'delete', -1 );
			} else {
				$.articleFeedbackv5special.flagFeedback( id, 'delete', 1 );
			}
		} );*/
	}

	// }}}
	// {{{ bindPanels
	
	/**
	 * Bind panels to controls - that cannot be 'live' events due to jQuery.typsy
	 * limitations. This function should be invoked after feedback posts are loaded,
	 * without parameters. The function should be invoked with the id parameter set
	 * after an action is executed and its link is replaced with reverse action.
	 *
	 * @param id post id to bind panels for. If none is supplied, bind entire list.
	 */
	$.articleFeedbackv5special.bindPanels = function( id ) {
		// single post or entire list?		
		var $selector = !id ? $( '#articleFeedbackv5-show-feedback' ) : $( '.articleFeedbackv5-feedback[rel="' + id + '"]' );

		// hide/show action
		$selector.find( '.articleFeedbackv5-hide-link' ).tipsy( {
			title: function() {
				var activity = $.articleFeedbackv5special.getActivity( id );
				return ( activity.hide || this.text == mw.msg( 'articlefeedbackv5-form-unhide' ) ) ?
					$.articleFeedbackv5special.notePanelHtml['show'] : $.articleFeedbackv5special.notePanelHtml['hide'];
			}
		} );
		
		// oversight/unoversight action
		$selector.find( '.articleFeedbackv5-oversight-link' ).tipsy( {
			title: function() {
				var activity = $.articleFeedbackv5special.getActivity( id );
				return ( activity.delete || this.text == mw.msg('articlefeedbackv5-form-undelete') ) ?
					$.articleFeedbackv5special.notePanelHtml['unoversight'] : $.articleFeedbackv5special.notePanelHtml['oversight'];
			}
		} );
		
		// request/unrequest oversight action
		$selector.find( '.articleFeedbackv5-requestoversight-link' ).tipsy( {
			title: function() {
				var activity = $.articleFeedbackv5special.getActivity( id );
				return ( activity.requestoversight || this.text == mw.msg('articlefeedbackv5-form-unrequestoversight') ) ?
					$.articleFeedbackv5special.notePanelHtml['unrequestoversight'] : $.articleFeedbackv5special.notePanelHtml['requestoversight'];
			}
		} );
		
		// decline oversight action
		$selector.find( '.articleFeedbackv5-declineoversight-link' ).tipsy( {
			title: function() {
				return $.articleFeedbackv5special.notePanelHtml['declineoversight'];
			}
		} );
		
		// view activity action
		$selector.find( '.articleFeedbackv5-activity-link' ).tipsy( {
			title: function() {
				return $.articleFeedbackv5special.logPanelHtml;
			}
		} );
	}
	
	// }}}
	
	// }}}
	// {{{ Utility methods

	// {{{ toggleComment
	$.articleFeedbackv5special.toggleComment = function( id ) { 
		if( $( '#articleFeedbackv5-comment-toggle-' + id ).text() 
		 == mw.msg( 'articlefeedbackv5-comment-more' ) ) {
			$( '#articleFeedbackv5-comment-short-' + id ).hide();
			$( '#articleFeedbackv5-comment-full-' + id ).show();
			$( '#articleFeedbackv5-comment-toggle-' + id ).text(
				mw.msg( 'articlefeedbackv5-comment-less' )
			);
		} else {
			$( '#articleFeedbackv5-comment-short-' + id ).show();
			$( '#articleFeedbackv5-comment-full-' + id ).hide();
			$( '#articleFeedbackv5-comment-toggle-' + id ).text(
				mw.msg( 'articlefeedbackv5-comment-more' )
			);
		}
	};

	// }}}
	// {{{ drawSortArrow

	$.articleFeedbackv5special.drawSortArrow = function() { 
		var	id  = $.articleFeedbackv5special.listControls.sort,
			dir = $.articleFeedbackv5special.listControls.sortDirection;

		$( '.articleFeedbackv5-sort-arrow' ).removeClass( 'sort-asc' );
		$( '.articleFeedbackv5-sort-arrow' ).removeClass( 'sort-desc' );
		$( '.articleFeedbackv5-sort-arrow' ).hide();
		$( '.articleFeedbackv5-sort-link' ).removeClass( 'sort-active' );

		$( '#articleFeedbackv5-sort-arrow-' + id ).show();
		$( '#articleFeedbackv5-sort-arrow-' + id ).addClass( 'sort-' + dir );
		$( '#articleFeedbackv5-special-sort-' + id).addClass( 'sort-active' );
	};

	// }}}
	// {{{ stripID

	// Utility method for stripping long IDs down to the specific bits we care about.
	$.articleFeedbackv5special.stripID = function( object, toRemove ) {
		return $( object ).attr( 'id' ).replace( toRemove, '' );
	};

	// }}}
	// {{{ prefix

	/**
	 * Utility method: Prefixes a key for cookies or events with extension and
	 * version information
	 *
	 * @param  key    string name of event to prefix
	 * @return string prefixed event name
	 */
	$.articleFeedbackv5special.prefix = function ( key ) {
		var version = mw.config.get( 'wgArticleFeedbackv5Tracking' ).version || 0;
		return 'ext.articleFeedbackv5@' + version + '-' + key;
	};

	// }}}
	// {{{ encodeActivity

	/**
	 * Utility method: Turns the user activity object into an encoded string
	 *
	 * @param  activity object the activity object
	 * @return string   the encoded string
	 */
	$.articleFeedbackv5special.encodeActivity = function ( activity ) {
		var encoded = '';
		for ( var fb in activity ) {
			var info = activity[fb];
			var buffer = fb + ':';
			if ( info.helpful ) {
				buffer += 'H';
			} else if ( info.unhelpful ) {
				buffer += 'U';
			}
			if ( info.abuse ) {
				buffer += 'A';
			}
			if ( info.hide ) {
				buffer += 'I';
			}
			if ( info.delete ) {
				buffer += 'D';
			}
			encoded += encoded == '' ? buffer : ';' + buffer;
		}
		return encoded;
	};

	// }}}
	// {{{ decodeActivity

	/**
	 * Utility method: Turns the encoded string into a user activity object
	 *
	 * @param  encoded string the encoded string
	 * @return object  the activity object
	 */
	$.articleFeedbackv5special.decodeActivity = function ( encoded ) {
		var entries = encoded.split( ';' );
		var activity = {};
		for ( var i = 0; i < entries.length; ++i ) {
			var parts = entries[i].split( ':' );
			if ( parts.length != 2 ) {
				continue;
			}
			var fb   = parts[0];
			var info = parts[1];
			var obj  = { helpful: false, unhelpful: false, abuse: false, hide: false, delete: false };
			if ( fb.length > 0 && info.length > 0 ) {
				if ( info.search( /H/ ) != -1 ) {
					obj.helpful = true;
				}
				if ( info.search( /U/ ) != -1 ) {
					obj.unhelpful = true;
				}
				if ( info.search( /A/ ) != -1 ) {
					obj.abuse = true;
				}
				if ( info.search( /I/ ) != -1 ) {
					obj.hide = true;
				}
				if ( info.search( /D/ ) != -1 ) {
					obj.delete = true;
				}
				activity[fb] = obj;
			}
		}
		return activity;
	};

	// }}}
	// {{{ getActivity

	/**
	 * Utility method: Gets the activity for a feedback ID
	 *
	 * @param  fid    int the feedback ID
	 * @return object the activity object
	 */
	$.articleFeedbackv5special.getActivity = function ( fid ) {
		if ( !( fid in $.articleFeedbackv5special.activity ) ) {
			$.articleFeedbackv5special.activity[fid] = { helpful: false, unhelpful: false, abuse: false, hide: false, delete: false };
		}
		return $.articleFeedbackv5special.activity[fid];
	};

	// }}}
	// {{{ markHidden

	/**
	 * Utility method: Marks a feedback row hidden
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.markHidden = function ( $row ) {
		if ( $row.data( 'hidden' ) ) {
			$.articleFeedbackv5special.unmarkHidden();
		}
		$row.addClass( 'articleFeedbackv5-feedback-hidden' )
			.data( 'hidden', true );
		$( '<span class="articleFeedbackv5-feedback-hidden-marker"></span>' )
			.text( mw.msg( 'articlefeedbackv5-hidden' ) )
			.insertBefore( $row.find( '.articleFeedbackv5-comment-wrap h3' ) );
	};

	// }}}
	// {{{ unmarkHidden

	/**
	 * Utility method: Unmarks as hidden a feedback row
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmarkHidden = function ( $row ) {
		$row.removeClass( 'articleFeedbackv5-feedback-hidden' )
			.data( 'hidden', false );
		$row.find( '.articleFeedbackv5-feedback-hidden-marker' ).remove();
	};

	// }}}
	// {{{ markDeleted

	/**
	 * Utility method: Marks a feedback row deleted
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.markDeleted = function ( $row ) {
		if ( $row.data( 'deleted' ) ) {
			$.articleFeedbackv5special.unmarkDeleted();
		}
		$row.addClass( 'articleFeedbackv5-feedback-deleted' )
			.data( 'deleted', true );
		var $marker = $( '<span class="articleFeedbackv5-feedback-deleted-marker"></span>' )
			.text( mw.msg( 'articlefeedbackv5-deleted' ) )
			.insertBefore( $row.find( '.articleFeedbackv5-comment-wrap h3' ) );
	};

	// }}}
	// {{{ unmarkDeleted

	/**
	 * Utility method: Unmarks as deleted a feedback row
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmarkDeleted = function ( $row ) {
		$row.removeClass( 'articleFeedbackv5-feedback-deleted' )
			.data( 'deleted', false );
		$row.find( '.articleFeedbackv5-feedback-deleted-marker' ).remove();
	};

	// }}}
	
	// }}}
	// {{{ Process methods

	// {{{ flagFeedback

	/**
	 * Sends the request to mark a response
	 *
	 * @param id   int    the feedback id
	 * @param type string the type of mark (valid values: hide, abuse, delete, helpful, unhelpful)
	 * @param dir  int    the direction of the mark (-1 = tick down; 1 = tick up)
	 * @param note string note for action (default empty)
	 */
	$.articleFeedbackv5special.flagFeedback = function ( id, type, dir, note ) {
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

		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'POST',
			'dataType': 'json',
			'data'    : {
				'pageid'    : $.articleFeedbackv5special.page,
				'feedbackid': id,
				'flagtype'  : type,
				'direction' : dir > 0 ? 'increase' : 'decrease',
				'note'		: note,
				'format'    : 'json',
				'action'    : 'articlefeedbackv5-flag-feedback'
			},
			'success': function ( data ) {
				var msg = 'articlefeedbackv5-error-flagging';
				if ( 'articlefeedbackv5-flag-feedback' in data ) {
					if ( 'result' in data['articlefeedbackv5-flag-feedback'] ) {
						if ( data['articlefeedbackv5-flag-feedback'].result == 'Success' ) {
							var $l = $( '#articleFeedbackv5-' + type + '-link-' + id );
							// Helpful or unhelpful
							if ( 'helpful' in data['articlefeedbackv5-flag-feedback'] ) {
								$( '#articleFeedbackv5-helpful-votes-' + id ).text( data['articlefeedbackv5-flag-feedback'].helpful );
							}
							if ( 'helpful' == type || 'unhelpful' == type ) {
								if ( dir > 0 ) {
									$l.addClass( 'helpful-active' );
								} else {
									$l.removeClass( 'helpful-active' );
								}
							// Abusive
							} else if ( 'abuse' == type ) {
								if ( dir > 0 ) {
									if( mw.config.get( 'afCanEdit' ) == 1 ) {
										$l.text( mw.msg( 'articlefeedbackv5-abuse-saved', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
									} else {
										$l.text( mw.msg( 'articlefeedbackv5-abuse-saved-masked', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
									}
								} else {
									if( mw.config.get( 'afCanEdit' ) == 1 ) {
										$l.text( mw.msg( 'articlefeedbackv5-form-abuse', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
									} else {
										$l.text( mw.msg( 'articlefeedbackv5-form-abuse-masked', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
									}
								}
								$l.attr( 'rel', data['articlefeedbackv5-flag-feedback'].abuse_count );
								if ( data['articlefeedbackv5-flag-feedback'].abusive ) {
									$l.addClass( 'abusive' );
								} else {
									$l.removeClass( 'abusive' );
								}
								if ( data['articlefeedbackv5-flag-feedback']['abuse-hidden'] ) {
									$.articleFeedbackv5special.markHidden( $l.closest( '.articleFeedbackv5-feedback' ) );
								}
							// Hide
							} else if ( 'hide' == type ) {
								if ( dir > 0 ) {
									$l.text( mw.msg( 'articlefeedbackv5-form-unhide' ) );
									$.articleFeedbackv5special.markHidden( $l.closest( '.articleFeedbackv5-feedback' ) );
								} else {
									$l.text( mw.msg( 'articlefeedbackv5-form-hide' ) );
									$.articleFeedbackv5special.unmarkHidden( $l.closest( '.articleFeedbackv5-feedback' ) );
								}
							// Delete
							} else if ( 'delete' == type ) {
								if ( dir > 0 ) {
									$l.text( mw.msg( 'articlefeedbackv5-form-undelete' ) );
									$.articleFeedbackv5special.markDeleted( $l.closest( '.articleFeedbackv5-feedback' ) );
								} else {
									$l.text( mw.msg( 'articlefeedbackv5-form-delete' ) );
									$.articleFeedbackv5special.unmarkDeleted( $l.closest( '.articleFeedbackv5-feedback' ) );
								}
							}
							// Save activity
							if ( !( id in $.articleFeedbackv5special.activity ) ) {
								$.articleFeedbackv5special.activity[id] = { helpful: false, unhelpful: false, abuse: false, hide: false, delete: false };
							}
							$.articleFeedbackv5special.activity[id][type] = dir > 0 ? true : false;
							$.articleFeedbackv5special.storeActivity();
							$.articleFeedbackv5special.bindPanels( id );
						} else if ( data['articlefeedbackv5-flag-feedback'].result == 'Error' ) {
							mw.log( mw.msg( data['articlefeedbackv5-flag-feedback'].reason ) );
						}
					}
				}
				// Re-enable ajax flagging.
				$.articleFeedbackv5special.listControls.disabled = false;
			},
			'error': function ( data ) {
				$( '#articleFeedbackv5-' + type + '-link-' + id ).text( mw.msg( 'articlefeedbackv5-error-flagging' ) );
				// Re-enable ajax flagging.
				$.articleFeedbackv5special.listControls.disabled = false;
			}
		} );
		return false;
	}

	// }}}
	// {{{ loadFeedback

	/**
	 * Pulls in a set of responses.
	 *
	 * When a next-page load is requested, it appends the new responses; on a
	 * sort or filter change, the existing responses are removed from the view
	 * and replaced.
	 *
	 * @param resetContents bool whether to remove the existing responses
	 */
	$.articleFeedbackv5special.loadFeedback = function ( resetContents ) {
		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'GET',
			'dataType': 'json',
			'data'    : {
				'afvfpageid'        : $.articleFeedbackv5special.page,
				'afvffilter'        : $.articleFeedbackv5special.listControls.filter,
				'afvffiltervalue'   : $.articleFeedbackv5special.listControls.filterValue,
				'afvfsort'          : $.articleFeedbackv5special.listControls.sort,
				'afvfsortdirection' : $.articleFeedbackv5special.listControls.sortDirection,
				'afvflimit'         : $.articleFeedbackv5special.listControls.limit,
				'afvfcontinue'      : $.articleFeedbackv5special.listControls.continue,
				'afvfcontinueid'    : $.articleFeedbackv5special.listControls.continueId,
				'action'  : 'query',
				'format'  : 'json',
				'list'    : 'articlefeedbackv5-view-feedback',
				'maxage'  : 0
			},
			'success': function ( data ) {
				if ( 'articlefeedbackv5-view-feedback' in data ) {
					if ( resetContents ) {
						$( '#articleFeedbackv5-show-feedback' ).empty();
					}
					var $newList = $( '#articleFeedbackv5-show-feedback' ).append( data['articlefeedbackv5-view-feedback'].feedback );
					$newList.find( '.articleFeedbackv5-feedback' ).each( function () {
						var id = $( this ).attr( 'rel' );
						if ( id in $.articleFeedbackv5special.activity ) {
							var activity = $.articleFeedbackv5special.getActivity( id );
							if ( activity.helpful ) {
								$( this ).find( '#articleFeedbackv5-helpful-link-' + id ).addClass( 'helpful-active' );
							}
							if ( activity.unhelpful ) {
								$( this ).find( '#articleFeedbackv5-unhelpful-link-' + id ).addClass( 'helpful-active' );
							}
							if ( activity.abuse ) {
								var $l = $( this ).find( '#articleFeedbackv5-abuse-link-' + id );
								if( mw.config.get( 'afCanEdit' ) == 1 ) {
									$l.text( mw.msg( 'articlefeedbackv5-abuse-saved', $l.attr( 'rel' ) ) );
								} else {
									$l.text( mw.msg( 'articlefeedbackv5-abuse-saved-masked', $l.attr( 'rel' ) ) );
								}
							}
						}
						if ( $( this ).hasClass( 'articleFeedbackv5-feedback-hidden' ) ) {
							$.articleFeedbackv5special.markHidden( $( this ) );
						}
						if ( $( this ).hasClass( 'articleFeedbackv5-feedback-deleted' ) ) {
							$.articleFeedbackv5special.markDeleted( $( this ) );
						}
					} );
					$( '#articleFeedbackv5-feedback-count-total' ).text( data['articlefeedbackv5-view-feedback'].count );
					$.articleFeedbackv5special.listControls.continue   = data['articlefeedbackv5-view-feedback'].continue;
					$.articleFeedbackv5special.listControls.continueId = data['articlefeedbackv5-view-feedback'].continueid;
					if( data['articlefeedbackv5-view-feedback'].more ) {
						$( '#articleFeedbackv5-show-more').show();
					} else {
						$( '#articleFeedbackv5-show-more').hide();
					}
					$.articleFeedbackv5special.bindPanels();
				} else {
					$( '#articleFeedbackv5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				}
			},
			'error': function ( data ) {
				$( '#articleFeedbackv5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
			}
		} );

		return false;
	}

	// }}}
	// {{{ loadActivity

	/**
	 * Loads the user activity from the cookie
	 */
	$.articleFeedbackv5special.loadActivity = function () {
		var flatActivity = 	$.cookie( $.articleFeedbackv5special.prefix( $.articleFeedbackv5special.activityCookieName ) );
		if ( flatActivity ) {
			$.articleFeedbackv5special.activity = $.articleFeedbackv5special.decodeActivity( flatActivity );
		}
	}

	// }}}
	// {{{ storeActivity

	/**
	 * Stores the user activity to the cookie
	 */
	$.articleFeedbackv5special.storeActivity = function () {
		var flatActivity = $.articleFeedbackv5special.encodeActivity( $.articleFeedbackv5special.activity );
		$.cookie(
			$.articleFeedbackv5special.prefix( $.articleFeedbackv5special.activityCookieName ),
			flatActivity,
			{ 'expires': 365, 'path': '/' }
		);
	}

	// }}}

	// }}}

// }}}

} )( jQuery );

