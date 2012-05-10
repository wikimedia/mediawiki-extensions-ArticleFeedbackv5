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
 * TODO: jam sort/filter options into URL anchors, and use them as defaults if present.
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Greg Chiasson <gchiasson@omniti.com>
 * @author     Yoni Shostak <yoni@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
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
	 * The url to which to send the pull request
	 */
	$.articleFeedbackv5special.apiUrl = mw.util.wikiScript( 'api' );

	/**
	 * Controls for the list: sort, filter, continue flag, etc
	 */
	$.articleFeedbackv5special.listControls = {
		filter: mw.config.get( 'afStartingFilter' ),
		filterValue: mw.config.get( 'afStartingFilterValue' ), // Permalinks require a feedback ID
		sort: mw.config.get( 'afStartingSort' ),
		sortDirection: mw.config.get( 'afStartingSortDirection' ),
		limit: mw.config.get( 'afStartingLimit' ),
		continue: null,
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
			<h3 id="articlefeedbackv5-noteflyover-caption"></h3>\
			<a id="articlefeedbackv5-noteflyover-close" href="#"></a>\
		</div>\
		<form class="articlefeedbackv5-form-flyover">\
			<label id="articlefeedbackv5-noteflyover-label" for="articlefeedbackv5-noteflyover-note"></label>\
			<textarea id="articlefeedbackv5-noteflyover-note" name="articlefeedbackv5-noteflyover-note"></textarea>\
			<div class="articlefeedbackv5-flyover-footer">\
				<a id="articlefeedbackv5-noteflyover-submit" class="articlefeedbackv5-flyover-button" href="#"></a>\
				<a class="articlefeedbackv5-flyover-help" id="articlefeedbackv5-noteflyover-help" href="#"></a>\
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
						<html:msg key="mask-view-contents"/ >\
					</a></span>\
				</span>\
			</div>\
		</div>';

	/**
	 * Featured marker HMTL template
	 */
	$.articleFeedbackv5special.featuredMarkerTemplate = '\
		<span class="articleFeedbackv5-featured-marker">\
			<html:msg key="featured-marker" />\
		</span>';

	/**
	 * Resolved marker HMTL template
	 */
	$.articleFeedbackv5special.resolvedMarkerTemplate = '\
		<span class="articleFeedbackv5-resolved-marker">\
			<html:msg key="resolved-marker" />\
		</span>';

	/**
	 * Loading tag template
	 */
	$.articleFeedbackv5special.loadingTemplate = '\
		<div id="articleFeedbackv5-feedback-loading">\
			<span class="articleFeedbackv5-loading-message"><html:msg key="loading-tag" /></span>\
		</div>'

	// }}}
	// {{{ Init methods

	// {{{ setup

	/**
	 * Sets up the page
	 */
	$.articleFeedbackv5special.setup = function() {
		// Set up config vars, event binds, and do initial fetch.
		$.articleFeedbackv5special.page = mw.config.get( 'afPageId' );
		$.articleFeedbackv5special.setBinds();

		// Hide arrows
		$( '.articleFeedbackv5-sort-arrow').hide();

		// Grab the user's activity out of the cookie
		$.articleFeedbackv5special.activityCookieName += $.articleFeedbackv5special.page;
		$.articleFeedbackv5special.loadActivity();

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

		// i18n, create action-specific tipsy panels from template
		var container = $( '<div></div>' );
		container.html( $.articleFeedbackv5special.notePanelHtmlTemplate );
		for ( var action in $.articleFeedbackv5special.actions ) {
			if ( $.articleFeedbackv5special.actions[action].hasTipsy && (undefined == $.articleFeedbackv5special.actions[action].tipsyHtml) ) {
				container.find( '#articlefeedbackv5-noteflyover-caption' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-caption' ) );
				container.find( '#articlefeedbackv5-noteflyover-label' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-label' ) );
				container.find( '#articlefeedbackv5-noteflyover-submit' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-submit' ) );
				// will add an 'action' attribute to the link
				container.find( '#articlefeedbackv5-noteflyover-submit' ).attr( 'action', action );
				container.find( '#articlefeedbackv5-noteflyover-help' ).text( mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-help' ) );
				container.find( '#articlefeedbackv5-noteflyover-help' ).attr( 'href', mw.msg( 'articlefeedbackv5-noteflyover-' + action + '-help-link' ) );
				$.articleFeedbackv5special.actions[action].tipsyHtml = container.html();
			}
		}

		// Add a loading tag to the top and hide it
		var $loading1 = $( $.articleFeedbackv5special.loadingTemplate );
		$loading1.attr( 'id', $loading1.attr( 'id' ) + '-top' );
		$loading1.localize( { 'prefix': 'articlefeedbackv5-' } );
		$loading1.hide();
		$( '#articleFeedbackv5-show-feedback' ).before( $loading1 );

		// Add a loading tag to the bottom and hide it
		var $loading2 = $( $.articleFeedbackv5special.loadingTemplate );
		$loading2.attr( 'id', $loading2.attr( 'id' ) + '-bottom' );
		$loading2.localize( { 'prefix': 'articlefeedbackv5-' } );
		$loading2.hide();
		$( '#articleFeedbackv5-show-more' ).before( $loading2 );

		// Initial load
		$.articleFeedbackv5special.processFeedback(
			mw.config.get( 'afCount' ),
			mw.config.get( 'afContinue' ),
			mw.config.get( 'afShowMore' )
		);
	};

	// }}}
	// {{{ setBinds

	/**
	 * Binds events for each of the controls
	 */
	$.articleFeedbackv5special.setBinds = function() {
		$( '#articleFeedbackv5-filter-select' ).bind( 'change', function( e ) {
			if ( $(this).val() == '' ) {
				return false;
			}
			$.articleFeedbackv5special.listControls.filter   = $(this).val();
			$.articleFeedbackv5special.listControls.continue = null;
			$( '.articleFeedbackv5-filter-link' ).removeClass( 'filter-active' );
			$.articleFeedbackv5special.loadFeedback( true );
			return false;
		} );

		$( '.articleFeedbackv5-filter-link' ).bind( 'click', function( e ) {
			var	id = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-special-filter-' );
			$.articleFeedbackv5special.listControls.filter   = id;
			$.articleFeedbackv5special.listControls.continue = null;
			$.articleFeedbackv5special.loadFeedback( true );
			$.articleFeedbackv5special.drawSortArrow();
			$( '.articleFeedbackv5-filter-link' ).removeClass( 'filter-active' );
			$( '#articleFeedbackv5-special-filter-' + id).addClass( 'filter-active' );
			$( '#articleFeedbackv5-filter-select' ).val( '' );
		} );

		$( '.articleFeedbackv5-sort-link' ).bind( 'click', function( e ) {
			var	id     = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-special-sort-' ),
				oldId  = $.articleFeedbackv5special.listControls.sort;

			// set direction = desc...
			$.articleFeedbackv5special.listControls.sort       = id;
			$.articleFeedbackv5special.listControls.continue   = null;

			// unless we're flipping the direction on the current sort.
			if ( id == oldId && $.articleFeedbackv5special.listControls.sortDirection == 'desc' ) {
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
			$.articleFeedbackv5special.loadFeedback( true );
		} );

		$( '.articleFeedbackv5-comment-toggle' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.toggleComment( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-comment-toggle-' ) );
			return false;
		} );

		// Bind actions
		for ( var action in $.articleFeedbackv5special.actions ) {
			$( '.articleFeedbackv5-' + action + '-link' ).live( 'click', function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				var classes = $link.attr( 'class' ).split( ' ' );
				var actionName = '';
				for ( var i = 0; i < classes.length; ++i ) {
					if ( classes[i].match( /^articleFeedbackv5-/ ) ) {
						actionName = classes[i].replace( 'articleFeedbackv5-', '' ).replace( '-link', '' );
						break;
					}
				}
				if ( actionName && !$link.hasClass( 'inactive' ) ) {
					$.articleFeedbackv5special.actions[actionName].click(e);
				}
			} );
		}

		// Bind submit actions on flyover panels (flag actions)
		$( '#articlefeedbackv5-noteflyover-submit' ).live( 'click', function( e ) {
			e.preventDefault();
			$.articleFeedbackv5special.flagFeedback(
				$( '#' + $.articleFeedbackv5special.currentPanelHostId ).closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ),
				$( e.target ).attr( 'action' ),
				$( '#articlefeedbackv5-noteflyover-note' ).attr( 'value' ),
				{ } );

			// hide tipsy
			$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
			$.articleFeedbackv5special.currentPanelHostId = undefined;
		} );

		// bind flyover panel close button
		$( '#articlefeedbackv5-noteflyover-close' ).live( 'click', function( e ) {
			e.preventDefault();
			$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
			$.articleFeedbackv5special.currentPanelHostId = undefined;
		} );
	}

	// }}}
	// {{{ bindPanels

	/**
	 * Bind panels to controls - that cannot be 'live' events due to jQuery.tipsy
	 * limitations. This function should be invoked after feedback posts are loaded,
	 * without parameters. The function should be invoked with the id parameter set
	 * after an action is executed and its link is replaced ith reverse action.
	 *
	 * @param id post id to bind panels for. If none is supplied, bind entire list.
	 */
	$.articleFeedbackv5special.bindPanels = function( id ) {
		// single post or entire list?
		var $selector = !id ? $( '#articleFeedbackv5-show-feedback' ) : $( '.articleFeedbackv5-feedback[rel="' + id + '"]' );

		for ( var action in $.articleFeedbackv5special.actions ) {
			$selector.find( '.articleFeedbackv5-' + action + '-link' )
				.attr( 'action', action )
				.tipsy( {
					title: function() {
						return $.articleFeedbackv5special.actions[$( this ).attr( 'action' )].tipsyHtml;
					}
				} );
		}
	}

	// }}}

	// }}}
	// {{{ Utility methods

	// {{{ toggleTipsy

	/**
	 * Utility method: Toggles tipsy display for an action link
	 *
	 * @param e event
	 * @returns true if showing tipsy, false if hiding
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
			if( undefined != $.articleFeedbackv5special.currentPanelHostId ) {
				$( '#' + $.articleFeedbackv5special.currentPanelHostId ).tipsy( 'hide' );
			}
			$l.tipsy( 'show' );
			$.articleFeedbackv5special.currentPanelHostId = $l.attr( 'id' );
			return true;
		}
	}

	// }}}
	// {{{ toggleComment

	/**
	 * Utility method: Toggles a comment between short and full displays
	 *
	 * @param id string the comment id
	 */
	$.articleFeedbackv5special.toggleComment = function( id ) {
		if ( $( '#articleFeedbackv5-comment-toggle-' + id ).text() == mw.msg( 'articlefeedbackv5-comment-more' ) ) {
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

	/**
	 * Utility method: Resets the sort arrows according to the currently selected
	 * sort and direction
	 */
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

	/**
	 * Utility method: Strips long IDs down to the specific bits we care about
	 */
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
	// {{{ markFeatured

	/**
	 * Utility method: Marks a feedback row featured
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.markFeatured = function ( $row ) {
		$row.addClass( 'articleFeedbackv5-feedback-featured' )
			.data( 'featured', true );
		$row.find( '.articleFeedbackv5-featured-marker' ).remove();
		$marker = $( $.articleFeedbackv5special.featuredMarkerTemplate );
		$marker.localize( { 'prefix': 'articlefeedbackv5-' } );
		$row.find( '.articleFeedbackv5-comment-tags' ).append( $marker );
	};

	// }}}
	// {{{ unmarkFeatured

	/**
	 * Utility method: Unmarks as featured a feedback row
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmarkFeatured = function ( $row ) {
		$row.removeClass( 'articleFeedbackv5-feedback-featured' )
			.data( 'featured', false );
		$row.find( '.articleFeedbackv5-featured-marker' ).remove();
	};

	// }}}
	// {{{ markResolved

	/**
	 * Utility method: Marks a feedback row as resolved
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.markResolved = function ( $row ) {
		$row.addClass( 'articleFeedbackv5-feedback-resolved' )
			.data( 'resolved', true );
		$row.find( '.articleFeedbackv5-resolved-marker' ).remove();
		$marker = $( $.articleFeedbackv5special.resolvedMarkerTemplate );
		$marker.localize( { 'prefix': 'articlefeedbackv5-' } );
		$row.find( '.articleFeedbackv5-comment-tags' ).append( $marker );
	};

	// }}}
	// {{{ unmarkResolved

	/**
	 * Utility method: Unmarks as resolved a feedback row
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmarkResolved = function ( $row ) {
		$row.removeClass( 'articleFeedbackv5-feedback-resolved' )
			.data( 'resolved', false );
		$row.find( '.articleFeedbackv5-resolved-marker' ).remove();
	};

	// }}}
	// {{{ markHidden

	/**
	 * Utility method: Marks a feedback row hidden
	 *
	 * @param $row element the feedback row
	 * @param line string  the mask line
	 */
	$.articleFeedbackv5special.markHidden = function ( $row, line ) {
		$row.addClass( 'articleFeedbackv5-feedback-hidden' );
		$.articleFeedbackv5special.maskPost( $row, line );
	};

	// }}}
	// {{{ unmarkHidden

	/**
	 * Utility method: Unmarks as hidden a feedback row
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmarkHidden = function ( $row ) {
		$row.removeClass( 'articleFeedbackv5-feedback-hidden' );
		if ( !$row.hasClass( 'articleFeedbackv5-feedback-deleted' ) ) {
			$.articleFeedbackv5special.unmaskPost( $row );
		}
	};

	// }}}
	// {{{ maskPost

	/**
	 * Utility method: Masks a comment that's been marked
	 * hidden/oversighted/etc.
	 *
	 * @param $row element the feedback row
	 * @param line string  the mask line
	 */
	$.articleFeedbackv5special.maskPost = function( $row, line ) {
		var $screen = $row.find( '.articleFeedbackv5-post-screen' );
		if ( 0 == $screen.length ) {
			$screen = $( $.articleFeedbackv5special.maskHtmlTemplate );
			$screen.localize( { 'prefix': 'articlefeedbackv5-' } );
			$screen.find( '.articleFeedbackv5-mask-info' ).html( line );
			$row.prepend( $screen );
		}
		if ( !$screen.hasClass( 'articleFeedbackv5-post-screen-on' ) ) {
			$screen.addClass( 'articleFeedbackv5-post-screen-on' );
		}
		if ( $screen.hasClass( 'articleFeedbackv5-post-screen-off' ) ) {
			$screen.removeClass( 'articleFeedbackv5-post-screen-off' );
		}
		$screen.click( function( e ) {
			$.articleFeedbackv5special.unmaskPost(
				$( e.target ).closest( '.articleFeedbackv5-feedback' )
			);
		} );
		$.articleFeedbackv5special.adjustMask( $row, $screen );
	}

	// }}}
	// {{{ unmaskPost

	/**
	 * Utility method: Unmasks a comment that was previously marked
	 * hidden/oversighted/etc.
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmaskPost = function( $row ) {
		$row.find( '.articleFeedbackv5-post-screen' )
			.addClass( 'articleFeedbackv5-post-screen-off' )
			.removeClass( 'articleFeedbackv5-post-screen-on' );
	}

	// }}}
	// {{{ adjustMask

	/**
	 * Utility method: Adjusts the mask on a comment to match its height
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.adjustMask = function( $row ) {
		var $screen = $row.find( '.articleFeedbackv5-post-screen' );
		$screen.height( $row.innerHeight() );
		$screen.find( '.articleFeedbackv5-mask-text-wrapper')
			.css( 'top', $screen.innerHeight() / 2 - 12 );
	}

	// }}}
	// {{{ markDeleted

	/**
	 * Utility method: Marks a feedback row deleted
	 *
	 * @param $row element the feedback row
	 * @param line string  the mask line
	 */
	$.articleFeedbackv5special.markDeleted = function ( $row, line ) {
		$row.addClass( 'articleFeedbackv5-feedback-deleted' );
		$.articleFeedbackv5special.maskPost( $row, line );
	};

	// }}}
	// {{{ unmarkDeleted

	/**
	 * Utility method: Unmarks as deleted a feedback row
	 *
	 * @param $row element the feedback row
	 */
	$.articleFeedbackv5special.unmarkDeleted = function ( $row ) {
		$row.removeClass( 'articleFeedbackv5-feedback-deleted' );
		if ( !$row.hasClass( 'articleFeedbackv5-feedback-hidden' ) ) {
			$.articleFeedbackv5special.unmaskPost( $row );
		}
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
	$.articleFeedbackv5special.setActivityFlag = function( id, flag, value ) {
		// no activity for this post yet, create default structure
		if ( !( id in $.articleFeedbackv5special.activity ) ) {
			$.articleFeedbackv5special.activity[id] = { 'helpful': false, 'unhelpful': false, 'abuse': false, 'hide': false, 'delete': false };
		}
		$.articleFeedbackv5special.activity[id][flag] = value;
		$.articleFeedbackv5special.storeActivity();
	};

	// }}}

	// }}}
	// {{{ Process methods

	// {{{ flagFeedback

	/**
	 * Sends the request to mark a response
	 *
	 * @param id   		int			the feedback id
	 * @param action	string		action to execute
	 * @param note 		string 		note for action (default empty)
	 * @param options	object		key => value pairs of additonal API action-specific parameters
	 */
	$.articleFeedbackv5special.flagFeedback = function ( id, action, note, options ) {
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

		// Merge request data and options objects (flat)
		var requestData = {
			'pageid'    : $.articleFeedbackv5special.page,
			'feedbackid': id,
			'flagtype'  : $.articleFeedbackv5special.actions[action].apiFlagType,
			'direction' : $.articleFeedbackv5special.actions[action].apiFlagDir > 0 ? 'increase' : 'decrease',
			'note'		: note,
			'format'    : 'json',
			'action'    : 'articlefeedbackv5-flag-feedback'
		};
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
							// invoke the registered onSuccess callback for the executed action
							if( undefined != $.articleFeedbackv5special.actions[action].onSuccess ) {
								$.articleFeedbackv5special.actions[action].onSuccess( id, data );
							}

							// Re-enable ajax flagging.
							$.articleFeedbackv5special.listControls.disabled = false;

							// re-bind panels (tipsies)
							$.articleFeedbackv5special.bindPanels( id );
							return true;
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
	// {{{ loadActivityLog

	/**
	 * Load the activity log for a feedback post item
	 *
	 * @param id           int    feedback post item id
	 * @param continueInfo string should be null for the first request (first page), then the continue info returned from the last API call
	 */
	$.articleFeedbackv5special.loadActivityLog = function( id, continueInfo ) {
		var data = {
			'action':			'query',
			'list':				'articlefeedbackv5-view-activity',
			'format':			'json',
			'aafeedbackid':		id
		};
		if ( continueInfo ) {
			data['aacontinue'] = continueInfo;
		}
		$.ajax( {
			'url': 		$.articleFeedbackv5special.apiUrl,
			'type': 	'GET',
			'dataType': 'json',
			'data': 	data,
			'success': function( data ) {
				if( data['articlefeedbackv5-view-activity'].hasHeader ) {
					$( '#articlefeedbackv5-activity-log' ).html( data['articlefeedbackv5-view-activity'].activity );
				} else {
					$( '#articlefeedbackv5-activity-log' )
						.find( '.articleFeedbackv5-activity-more' ).replaceWith( data['articlefeedbackv5-view-activity'].activity );
				}
				if( data['query-continue'] && data['query-continue']['articlefeedbackv5-view-activity'] ) {
					$( '#articlefeedbackv5-activity-log' ).find( '.articleFeedbackv5-activity-more' )
						.attr( 'rel', data['query-continue']['articlefeedbackv5-view-activity'].aacontinue )
						.click( function( e ) {
							$.articleFeedbackv5special.loadActivityLog(
								$( '#' + $.articleFeedbackv5special.currentPanelHostId ).closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ),
								$( e.target ).attr( 'rel') );
						} );
				}
			},
			'error': function( data ) {
				// FIXME this messages isn't defined
				$( '#articlefeedbackv5-activity-log' ).text( mw.msg( 'articleFeedbackv5-view-activity-error' ) );
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
	 * @param resetContents bool whether to remove the existing responses
	 */
	$.articleFeedbackv5special.loadFeedback = function ( resetContents ) {
		if ( resetContents ) {
			$( '#articleFeedbackv5-feedback-loading-top' ).fadeIn();
		} else {
			$( '#articleFeedbackv5-feedback-loading-bottom' ).fadeIn();
		}
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
					$( '#articleFeedbackv5-show-feedback' ).append( data['articlefeedbackv5-view-feedback'].feedback );
					$.articleFeedbackv5special.processFeedback(
						data['articlefeedbackv5-view-feedback'].count,
						data['articlefeedbackv5-view-feedback'].continue,
						data['articlefeedbackv5-view-feedback'].more
					);
				} else {
					$( '#articleFeedbackv5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				}
				if ( resetContents ) {
					$( '#articleFeedbackv5-feedback-loading-top' ).fadeOut();
				} else {
					$( '#articleFeedbackv5-feedback-loading-bottom' ).fadeOut();
				}
			},
			'error': function ( data ) {
				$( '#articleFeedbackv5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				if ( resetContents ) {
					$( '#articleFeedbackv5-feedback-loading-top' ).fadeOut();
				} else {
					$( '#articleFeedbackv5-feedback-loading-bottom' ).fadeOut();
				}
			}
		} );

		return false;
	};

	// }}}
	// {{{ processFeedback

	/**
	 * Processes in a set of responses
	 *
	 * @param count        int   the total number of responses
	 * @param continueInfo mixed the first continue value
	 * @param showMore     bool  whether there are more records to show
	 */
	$.articleFeedbackv5special.processFeedback = function ( count, continueInfo, showMore ) {
		var $newList = $( '#articleFeedbackv5-show-feedback' );
		$newList.find( '.articleFeedbackv5-feedback' ).each( function () {
			var id = $( this ).attr( 'rel' );
			if ( id in $.articleFeedbackv5special.activity ) {
				var activity = $.articleFeedbackv5special.getActivity( id );
				if ( activity.helpful ) {
					$( this ).find( '#articleFeedbackv5-helpful-link-' + id )
						.addClass( 'helpful-active' )
						.removeClass( 'articleFeedbackv5-helpful-link' )
						.addClass( 'articleFeedbackv5-reversehelpful-link' )
						.attr( 'id', 'articleFeedbackv5-reversehelpful-link-' + id );
				}
				if ( activity.unhelpful ) {
					$( this ).find( '#articleFeedbackv5-unhelpful-link-' + id )
						.addClass( 'helpful-active' )
						.removeClass( 'articleFeedbackv5-unhelpful-link' )
						.addClass( 'articleFeedbackv5-reverseunhelpful-link' )
						.attr( 'id', 'articleFeedbackv5-reverseunhelpful-link-' + id );
				}
				if ( activity.abuse ) {
					var $l = $( this ).find( '#articleFeedbackv5-abuse-link-' + id );
					if( mw.config.get( 'afCanEdit' ) == 1 ) {
						$l.text( mw.msg( 'articlefeedbackv5-abuse-saved', $l.attr( 'rel' ) ) );
					} else {
						$l.text( mw.msg( 'articlefeedbackv5-abuse-saved-masked', $l.attr( 'rel' ) ) );
					}
					$l.attr( 'id', 'articleFeedbackv5-unabuse-link-' + id )
						.removeClass( 'articleFeedbackv5-abuse-link' )
						.addClass( 'articleFeedbackv5-unabuse-link' );
				}
			}

			if ( $( this ).hasClass( 'articleFeedbackv5-feedback-emptymask' ) ) {
				$.articleFeedbackv5special.adjustMask( $( this ) );
			} else if ( $( this ).hasClass( 'articleFeedbackv5-feedback-deleted' ) ) {
				$.articleFeedbackv5special.maskPost( $( this ) );
			} else if ( $( this ).hasClass( 'articleFeedbackv5-feedback-hidden' ) ) {
				$.articleFeedbackv5special.maskPost( $( this ) );
			}
		} );

		$( '#articleFeedbackv5-feedback-count-total' ).text( count );
		$.articleFeedbackv5special.listControls.continue = continueInfo;
		if ( showMore ) {
			$( '#articleFeedbackv5-show-more').show();
		} else {
			$( '#articleFeedbackv5-show-more').hide();
		}
		$.articleFeedbackv5special.bindPanels();
	};

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
	};

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
	};

	// }}}
	// {{{ canBeFlagged

	/**
	 * Returns true if the post can be flagged
	 */
	$.articleFeedbackv5special.canBeFlagged = function( $post ) {
		return !$post.data( 'hidden' ) && !$post.data( 'deleted' );
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
	 * 		apiFlagType - flag type for api call
	 * 		apiFlagDir - flag direction for api call (+/-1)
	 * 		onSuccess - callback to execute after action success. Callback parameters:
	 * 			id - respective post id
	 * 			data - any data returned by the AJAX call
	 */
	$.articleFeedbackv5special.actions = {

		// Vote helpful
		'helpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				if ( $.articleFeedbackv5special.canBeFlagged( $link.closest( '.articleFeedbackv5-feedback' ) ) ) {
					var id = $link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
					var activity = $.articleFeedbackv5special.getActivity( id );
					$.articleFeedbackv5special.flagFeedback( id, 'helpful', '', activity['unhelpful'] ? { toggle: true } : { } );
				}
			},
			'apiFlagType': 'helpful',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				$( '#articleFeedbackv5-helpful-votes-' + id ).text( data['articlefeedbackv5-flag-feedback'].helpful );
				$( '#articleFeedbackv5-helpful-link-' + id )
					.addClass( 'helpful-active' )
					.removeClass( 'articleFeedbackv5-helpful-link' )
					.addClass( 'articleFeedbackv5-reversehelpful-link' )
					.attr( 'id', 'articleFeedbackv5-reversehelpful-link-' + id );
				if ( data['articlefeedbackv5-flag-feedback']['toggle'] ) {
					$( '#articleFeedbackv5-reverseunhelpful-link-' + id )
						.removeClass( 'helpful-active' )
						.removeClass( 'articleFeedbackv5-reverseunhelpful-link')
						.addClass( 'articleFeedbackv5-unhelpful-link' )
						.attr( 'id', 'articleFeedbackv5-unhelpful-link-' + id );
					$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', false )
				}
				$.articleFeedbackv5special.setActivityFlag( id, 'helpful', true );
			}
		},

		// Un-vote helpful
		'reversehelpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				if( $.articleFeedbackv5special.canBeFlagged( $link.closest( '.articleFeedbackv5-feedback' ) ) ) {
					$.articleFeedbackv5special.flagFeedback(
						$link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ), 'reversehelpful', '', { } );
				}
			},
			'apiFlagType': 'helpful',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				$( '#articleFeedbackv5-helpful-votes-' + id ).text( data['articlefeedbackv5-flag-feedback'].helpful );
				$( '#articleFeedbackv5-reversehelpful-link-' + id )
					.removeClass( 'helpful-active' )
					.removeClass( 'articleFeedbackv5-reversehelpful-link')
					.addClass( 'articleFeedbackv5-helpful-link' )
					.attr( 'id', 'articleFeedbackv5-helpful-link-' + id );
				$.articleFeedbackv5special.setActivityFlag( id, 'helpful', false );
			}
		},

		// Vote unhelpful
		'unhelpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				if( $.articleFeedbackv5special.canBeFlagged( $link.closest( '.articleFeedbackv5-feedback' ) ) ) {
					var id = $link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
					var activity = $.articleFeedbackv5special.getActivity( id );
					$.articleFeedbackv5special.flagFeedback( id, 'unhelpful', '', activity['helpful'] ? { toggle: true } : { } );
				}
			},
			'apiFlagType': 'unhelpful',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				$( '#articleFeedbackv5-helpful-votes-' + id ).text( data['articlefeedbackv5-flag-feedback'].helpful );
				$( '#articleFeedbackv5-unhelpful-link-' + id )
					.addClass( 'helpful-active' )
					.removeClass( 'articleFeedbackv5-unhelpful-link')
					.addClass( 'articleFeedbackv5-reverseunhelpful-link' )
					.attr( 'id', 'articleFeedbackv5-reverseunhelpful-link-' + id );
				if( data['articlefeedbackv5-flag-feedback']['toggle'] ) {
					$( '#articleFeedbackv5-reversehelpful-link-' + id )
						.removeClass( 'helpful-active' )
						.removeClass( 'articleFeedbackv5-reversehelpful-link')
						.addClass( 'articleFeedbackv5-helpful-link' )
						.attr( 'id', 'articleFeedbackv5-helpful-link-' + id );
					$.articleFeedbackv5special.setActivityFlag( id, 'helpful', false )
				}
				$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', true );
			}
		},

		// Un-vote unhelpful
		'reverseunhelpful': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				if( $.articleFeedbackv5special.canBeFlagged( $link.closest( '.articleFeedbackv5-feedback' ) ) ) {
					$.articleFeedbackv5special.flagFeedback(
						$link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ), 'reverseunhelpful', '', { } );
				}
			},
			'apiFlagType': 'unhelpful',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				$( '#articleFeedbackv5-helpful-votes-' + id ).text( data['articlefeedbackv5-flag-feedback'].helpful );
				$( '#articleFeedbackv5-reverseunhelpful-link-' + id )
					.removeClass( 'helpful-active' )
					.removeClass( 'articleFeedbackv5-reverseunhelpful-link')
					.addClass( 'articleFeedbackv5-unhelpful-link' )
					.attr( 'id', 'articleFeedbackv5-unhelpful-link-' + id );
				$.articleFeedbackv5special.setActivityFlag( id, 'unhelpful', false );
			}
		},

		// Flag post as abusive
		'abuse': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				if( $.articleFeedbackv5special.canBeFlagged( $link.closest( '.articleFeedbackv5-feedback' ) ) ) {
					var id = $link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
					$.articleFeedbackv5special.flagFeedback( $link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ), 'abuse', '', { } );
				}
			},
			'apiFlagType': 'abuse',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				$link = $( '#articleFeedbackv5-abuse-link-' + id );
				if( mw.config.get( 'afCanEdit' ) == 1 ) {
					$link.text( mw.msg( 'articlefeedbackv5-abuse-saved', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
				} else {
					$link.text( mw.msg( 'articlefeedbackv5-abuse-saved-masked', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
				}
				$link.attr( 'rel', data['articlefeedbackv5-flag-feedback'].abuse_count );
				$link.attr( 'href', '#' );
				if ( data['articlefeedbackv5-flag-feedback'].abusive ) {
					$link.addClass( 'abusive' );
				} else {
					$link.removeClass( 'abusive' );
				}
				if ( data['articlefeedbackv5-flag-feedback']['abuse-hidden'] ) {
					$.articleFeedbackv5special.markHidden(
						$link.closest( '.articleFeedbackv5-feedback' ),
						data['articlefeedbackv5-flag-feedback']['mask-line']
					);
				}
				$link.attr( 'id', 'articleFeedbackv5-unabuse-link-' + id )
					.removeClass( 'articleFeedbackv5-abuse-link' )
					.addClass( 'articleFeedbackv5-unabuse-link' );
				$.articleFeedbackv5special.setActivityFlag( id, 'abuse', true );
			}
		},

		// Unflag post as abusive
		'unabuse': {
			'hasTipsy': false,
			'click': function( e ) {
				e.preventDefault();
				var $link = $( e.target );
				if( $.articleFeedbackv5special.canBeFlagged( $link.closest( '.articleFeedbackv5-feedback' ) ) ) {
					var id = $link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' );
					$.articleFeedbackv5special.flagFeedback( $link.closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ), 'unabuse', '', { } );
				}
			},
			'apiFlagType': 'abuse',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				$link = $( '#articleFeedbackv5-unabuse-link-' + id );
				if( mw.config.get( 'afCanEdit' ) == 1 ) {
					$link.text( mw.msg( 'articlefeedbackv5-form-abuse', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
				} else {
					$link.text( mw.msg( 'articlefeedbackv5-form-abuse-masked', data['articlefeedbackv5-flag-feedback'].abuse_count ) );
				}
				$link.attr( 'rel', data['articlefeedbackv5-flag-feedback'].abuse_count );
				$link.attr( 'href', '#' );
				if ( data['articlefeedbackv5-flag-feedback'].abusive ) {
					$link.addClass( 'abusive' );
				} else {
					$link.removeClass( 'abusive' );
				}
				if ( data['articlefeedbackv5-flag-feedback']['abuse-hidden'] ) {
					$.articleFeedbackv5special.markHidden(
						$link.closest( '.articleFeedbackv5-feedback' ),
						data['articlefeedbackv5-flag-feedback']['mask-line']
					);
				}
				$link.attr( 'id', 'articleFeedbackv5-abuse-link-' + id )
					.removeClass( 'articleFeedbackv5-unabuse-link' )
					.addClass( 'articleFeedbackv5-abuse-link' );
				$.articleFeedbackv5special.setActivityFlag( id, 'abuse', false );
			}
		},

		// Feature post action
		'feature': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'feature',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-feature-link-' + id )
					.attr( 'action', 'unfeature' )
					.attr( 'id', 'articleFeedbackv5-unfeature-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-unfeature' ) )
					.removeClass( 'articleFeedbackv5-feature-link' )
					.addClass( 'articleFeedbackv5-unfeature-link' );

				$.articleFeedbackv5special.markFeatured( $link.closest( '.articleFeedbackv5-feedback' ) );
				$.articleFeedbackv5special.setActivityFlag( id, 'feature', true );
			}
		},

		// Un-feature post action
		'unfeature': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'feature',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-unfeature-link-' + id )
					.attr( 'action', 'feature' )
					.attr( 'id', 'articleFeedbackv5-feature-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-feature' ) )
					.removeClass( 'articleFeedbackv5-unfeature-link' )
					.addClass( 'articleFeedbackv5-feature-link' );

				var $row = $link.closest( '.articleFeedbackv5-feedback' );
				$.articleFeedbackv5special.unmarkFeatured( $row );
				$.articleFeedbackv5special.setActivityFlag( id, 'feature', false );
			}
		},

		// Mark resolved post action
		'resolve': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'resolve',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-resolve-link-' + id )
					.attr( 'action', 'unresolve' )
					.attr( 'id', 'articleFeedbackv5-unresolve-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-unresolve' ) )
					.removeClass( 'articleFeedbackv5-resolve-link' )
					.addClass( 'articleFeedbackv5-unresolve-link' );

				$.articleFeedbackv5special.markResolved( $link.closest( '.articleFeedbackv5-feedback' ) );
				$.articleFeedbackv5special.setActivityFlag( id, 'resolve', true );
			}
		},

		// Unmark as resolved post action
		'unresolve': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'resolve',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-unresolve-link-' + id )
					.attr( 'action', 'resolve' )
					.attr( 'id', 'articleFeedbackv5-resolve-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-resolve' ) )
					.removeClass( 'articleFeedbackv5-unresolve-link' )
					.addClass( 'articleFeedbackv5-resolve-link' );

				var $row = $link.closest( '.articleFeedbackv5-feedback' );
				$.articleFeedbackv5special.unmarkResolved( $row );
				$.articleFeedbackv5special.setActivityFlag( id, 'resolve', false );
			}
		},

		// Hide post action
		'hide': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'hide',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-hide-link-' + id )
					.attr( 'action', 'show' )
					.attr( 'id', 'articleFeedbackv5-show-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-unhide' ) )
					.removeClass( 'articleFeedbackv5-hide-link' )
					.addClass( 'articleFeedbackv5-show-link' );

				$.articleFeedbackv5special.markHidden(
					$link.closest( '.articleFeedbackv5-feedback' ),
					data['articlefeedbackv5-flag-feedback']['mask-line']
				);
				$.articleFeedbackv5special.setActivityFlag( id, 'hide', true );
			}
		},

		// Show post action
		'show': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'hide',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-show-link-' + id )
					.attr( 'action', 'hide' )
					.attr( 'id', 'articleFeedbackv5-hide-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-hide' ) )
					.removeClass( 'articleFeedbackv5-show-link' )
					.addClass( 'articleFeedbackv5-hide-link' );

				var $row = $link.closest( '.articleFeedbackv5-feedback' );
				$.articleFeedbackv5special.unmarkHidden( $row );
				$.articleFeedbackv5special.setActivityFlag( id, 'hide', false );
			}
		},

		// Request oversight action
		'requestoversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'oversight',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				var $link = $( '#articleFeedbackv5-requestoversight-link-' + id )
					.attr( 'action', 'unrequestoversight' )
					.attr( 'id', 'articleFeedbackv5-unrequestoversight-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-unoversight' ) )
					.removeClass( 'articleFeedbackv5-requestoversight-link' )
					.addClass( 'articleFeedbackv5-unrequestoversight-link');

				if ( data['articlefeedbackv5-flag-feedback']['autohidden'] ) {
					var $new_link = $( '#articleFeedbackv5-hide-link-' + id )
						.attr( 'action', 'show' )
						.attr( 'id', 'articleFeedbackv5-show-link-' + id )
						.text( mw.msg( 'articlefeedbackv5-form-unhide' ) )
						.removeClass( 'articleFeedbackv5-hide-link' )
						.addClass( 'articleFeedbackv5-show-link' );

					$.articleFeedbackv5special.markHidden(
						$link.closest( '.articleFeedbackv5-feedback' ),
						data['articlefeedbackv5-flag-feedback']['mask-line']
					);
					$.articleFeedbackv5special.setActivityFlag( id, 'hide', true );
				}
			}
		},

		// Cancel oversight request action
		'unrequestoversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'oversight',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				$( '#articleFeedbackv5-unrequestoversight-link-' + id )
					.attr( 'action', 'requestoversight' )
					.attr( 'id', 'articleFeedbackv5-requestoversight-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-oversight' ) )
					.removeClass( 'articleFeedbackv5-unrequestoversight-link' )
					.addClass( 'articleFeedbackv5-requestoversight-link');
			}
		},

		// Oversight post action
		'oversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'delete',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				// if there is a "decline oversight" just hide it
				var $link = $( '#articleFeedbackv5-declineoversight-link-' + id ).hide();

				// Oversight is going to hide this as well, do the unhide/hide toggle
				var $link = $( '#articleFeedbackv5-hide-link-' + id )
					.attr( 'action', 'show' )
					.attr( 'id', 'articleFeedbackv5-show-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-unhide' ) )
					.removeClass( 'articleFeedbackv5-hide-link' )
					.addClass( 'articleFeedbackv5-show-link' );

				var $link = $( '#articleFeedbackv5-oversight-link-' + id )
					.attr( 'action', 'unoversight' )
					.attr( 'id', 'articleFeedbackv5-unoversight-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-undelete' ) )
					.removeClass( 'articleFeedbackv5-oversight-link' )
					.addClass( 'articleFeedbackv5-unoversight-link' );

				$.articleFeedbackv5special.markDeleted(
					$link.closest( '.articleFeedbackv5-feedback' ),
					data['articlefeedbackv5-flag-feedback']['mask-line']
				);
				$.articleFeedbackv5special.setActivityFlag( id, 'delete', true );
			}
		},

		// Un-oversight action
		'unoversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'delete',
			'apiFlagDir': -1,
			'onSuccess': function( id, data ) {
				// if there is a "decline oversight" just show it
				var $link = $( '#articleFeedbackv5-declineoversight-link-' + id ).show();

				var $link = $( '#articleFeedbackv5-unoversight-link-' + id )
					.attr( 'action', 'oversight' )
					.attr( 'id', 'articleFeedbackv5-oversight-link-' + id )
					.text( mw.msg( 'articlefeedbackv5-form-delete' ) )
					.removeClass( 'articleFeedbackv5-unoversight-link' )
					.addClass( 'articleFeedbackv5-oversight-link' );

				var $row = $link.closest( '.articleFeedbackv5-feedback' );
				$.articleFeedbackv5special.unmarkDeleted( $row );
				$.articleFeedbackv5special.setActivityFlag( id, 'delete', false );
			}
		},

		// Decline oversight action
		'declineoversight': {
			'hasTipsy': true,
			'tipsyHtml': undefined,
			'click': $.articleFeedbackv5special.toggleTipsy,
			'apiFlagType': 'resetoversight',
			'apiFlagDir': 1,
			'onSuccess': function( id, data ) {
				var $row = $( '#articleFeedbackv5-declineoversight-link-' + id )
					.closest( '.articleFeedbackv5-feedback' );
			}
		},

		// View activity log action
		'activity': {
			'hasTipsy': true,
			'tipsyHtml': '\
				<div>\
					<div class="articlefeedbackv5-flyover-header">\
						<h3 id="articlefeedbackv5-noteflyover-caption">Activity log</h3>\
						<a id="articlefeedbackv5-noteflyover-helpbutton" href="#"></a>\
						<a id="articlefeedbackv5-noteflyover-close" href="#"></a>\
					</div>\
					<div id="articlefeedbackv5-activity-log"></div>\
				</div>',
			'click': function( e ) {
				if( $.articleFeedbackv5special.toggleTipsy( e ) ) {
					$.articleFeedbackv5special.loadActivityLog( $( e.target ).closest( '.articleFeedbackv5-feedback' ).attr( 'rel' ), 0 );
				}
			}
		}

	};

	// }}}

// }}}

} )( jQuery );

