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

// {{{ articleFeedbackv5 definition

	// TODO: jam sort/filter options into URL anchors, and use them as defaults if present.

	$.articleFeedbackv5special = {};

	// {{{ Properties

	/**
	 * What page is this?
	 */
	$.articleFeedbackv5special.page = undefined;

	/**
	 * The name of the filter used to select feedback
	 */
	$.articleFeedbackv5special.filter = 'visible';

	/**
	 * Some fitlers have values they need tobe passed (eg, permalinks)
	 */
	$.articleFeedbackv5special.filterValue = undefined;

	/**
	 * The name of the sorting method used
	 */
	$.articleFeedbackv5special.sort = 'age';

	/**
	 * The dorection of the sorting method used
	 */
	$.articleFeedbackv5special.sortDirection = 'desc';

	/**
	 * The number of responses to display per data pull
	 */
	$.articleFeedbackv5special.limit = 25;

	/**
	 * The index at which to start the pull
	 */
	$.articleFeedbackv5special.continue = null;

	/**
	 * The url to which to send the request
	 */
	$.articleFeedbackv5special.apiUrl = undefined;

	// }}}
	// {{{ Init methods

	/**
	 * Binds events for each of the controls
	 */
	$.articleFeedbackv5special.setBinds = function() {
		$( '#articleFeedbackv5-filter' ).bind( 'change', function( e ) {
			$.articleFeedbackv5special.filter   = $(this).val();
			$.articleFeedbackv5special.continue = null;
			$.articleFeedbackv5special.loadFeedback( true );
			return false;
		} );
		$( '.articleFeedbackv5-sort-link' ).bind( 'click', function( e ) {
			id    = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-special-sort-' );
			oldId = $.articleFeedbackv5special.sort;

			// set direction = desc...
			$.articleFeedbackv5special.sortDirection = 'desc';
			$.articleFeedbackv5special.sort          = id;
			$.articleFeedbackv5special.continue      = null;
			$.articleFeedbackv5special.loadFeedback( true );

			// unless we're flipping the direction on the current sort.
console.log('id is ' + id + ', old id is ' + oldId);
			if( id == oldId 
			 && $.articleFeedbackv5special.sortDirection == 'desc') {
				$.articleFeedbackv5special.sortDirection = 'asc';
			} 
			// draw arrow
			$.articleFeedbackv5special.drawSortArrow();

			return false;
		} );
		$( '#articleFeedbackv5-show-more' ).bind( 'click', function( e ) {
			$.articleFeedbackv5special.loadFeedback( false );
			return false;
		} );

		$( '.articleFeedbackv5-abuse-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.flagFeedback( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-abuse-link-' ), 'abuse' );
			return false;
		} );
		$( '.articleFeedbackv5-hide-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.flagFeedback( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-hide-link-' ), 'hide' );
			return false;
		} );
		$( '.articleFeedbackv5-delete-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.flagFeedback( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-delete-link-' ), 'delete' );
			return false;
		} );
		$( '.articleFeedbackv5-helpful-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.flagFeedback( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-helpful-link-' ), 'helpful' );
			return false;
		} );
		$( '.articleFeedbackv5-unhelpful-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.flagFeedback( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-unhelpful-link-' ), 'unhelpful' );
			return false;
		} );
	}

	$.articleFeedbackv5special.drawSortArrow = function() { 
		id  = $.articleFeedbackv5special.sort;
		dir = $.articleFeedbackv5special.sortDirection;

		$( '.articleFeedbackv5-sort-arrow' ).hide();

		$( '#articleFeedbackv5-sort-arrow-' + id ).text(
			mw.msg( 'articlefeedbackv5-special-sort-' + dir )
		);
		$( '#articleFeedbackv5-sort-arrow-' + id ).show();
	}

	// Utility method for stripping long IDs down to the specific bits we care about.
	$.articleFeedbackv5special.stripID = function( object, toRemove ) {
		return $( object ).attr( 'id' ).replace( toRemove, '' );
	}
	
	// Display/hide the toolbox
	$.articleFeedbackv5special.toggleToolbox = function( container ) {
		var id = $.articleFeedbackv5special.stripID(container, 'articleFeedbackv5-feedback-tools-');
		$( '#articleFeedbackv5-feedback-tools-list-' + id ).slideToggle( 300 );
	}

	// }}}

	// {{{ flagFeedback

	/**
	 * Sends the request to mark a response
	 *
	 * @param id   int    the feedback id
	 * @param type string the type of mark (valid values: hide, abuse, delete, helpful, unhelpful
	 */
	$.articleFeedbackv5special.flagFeedback = function ( id, type ) {
		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'POST',
			'dataType': 'json',
			'data'    : {
				'pageid'    : $.articleFeedbackv5special.page,
				'feedbackid': id,
				'flagtype'  : type,
				'format'    : 'json',
				'action'    : 'articlefeedbackv5-flag-feedback'
			},
			'success': function ( data ) {
				var msg = 'articlefeedbackv5-error-flagging';
				if ( 'articlefeedbackv5-flag-feedback' in data ) {
					if ( 'result' in data['articlefeedbackv5-flag-feedback'] ) {
						if( data['articlefeedbackv5-flag-feedback'].result == 'Success' ) {
							msg = 'articlefeedbackv5-' + type + '-saved';
						} else if (data['articlefeedbackv5-flag-feedback'].result == 'Error' ) {
							msg = data['articlefeedbackv5-flag-feedback'].reason;
						}
					}
				}
				$( '#articleFeedbackv5-' + type + '-link-' + id ).text( mw.msg( msg ) );
			},
			'error': function ( data ) {
				$( '#articleFeedbackv5-' + type + '-link-' + id ).text( mw.msg( 'articlefeedbackv5-error-flagging' ) );
			}
		} );
		return false;
	}

	// }}}

	// }}}
	// {{{ Process methods

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
				'afvffilter'        : $.articleFeedbackv5special.filter,
				'afvffiltervalue'   : $.articleFeedbackv5special.filterValue,
				'afvfsort'          : $.articleFeedbackv5special.sort,
				'afvfsortdirection' : $.articleFeedbackv5special.sortDirection,
				'afvflimit'         : $.articleFeedbackv5special.limit,
				'afvfcontinue'      : $.articleFeedbackv5special.continue,
				'action'  : 'query',
				'format'  : 'json',
				'list'    : 'articlefeedbackv5-view-feedback',
				'maxage'  : 0
			},
			'success': function ( data ) {
				if ( 'articlefeedbackv5-view-feedback' in data ) {
					if ( resetContents ) {
						$( '#articleFeedbackv5-show-feedback' ).html( data['articlefeedbackv5-view-feedback'].feedback);
					} else {
						$( '#articleFeedbackv5-show-feedback' ).append( data['articlefeedbackv5-view-feedback'].feedback);
					}
					$( '#articleFeedbackv5-feedback-count-total' ).text( data['articlefeedbackv5-view-feedback'].count );
					$.articleFeedbackv5special.continue = data['articlefeedbackv5-view-feedback'].continue;
					// set effects on toolboxes
					$( '.articleFeedbackv5-feedback-tools > ul' ).hide();
					$( '.articleFeedbackv5-feedback-tools' ).hover( 
						function( eventObj ) { $.articleFeedbackv5special.toggleToolbox( this ); },
						function( eventObj ) { $.articleFeedbackv5special.toggleToolbox( this ); }
					);
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

	// }}}

// }}}

} )( jQuery );

$( document ).ready( function() {

// {{{ Kick off when ready

	// Set up config vars, event binds, and do initial fetch.
	$.articleFeedbackv5special.apiUrl  = mw.util.wikiScript('api');
	$.articleFeedbackv5special.page = mw.config.get( 'afPageId' );
	$.articleFeedbackv5special.setBinds();

	// Process anything we found in the URL hash
	// Permalinks.
	var id = window.location.hash.match(/id=(\d+)/)
	if( id ) {
		$.articleFeedbackv5special.filter      = 'id';
		$.articleFeedbackv5special.filterValue = id[1];
	}

	// Initial load
	$.articleFeedbackv5special.loadFeedback( true );
	$.articleFeedbackv5special.drawSortArrow();

// }}}

} );

