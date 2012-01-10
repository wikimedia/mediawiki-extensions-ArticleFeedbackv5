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
	 * The name of the sorting method used
	 */
	$.articleFeedbackv5special.sort = 'newest';

	/**
	 * The number of responses to display per data pull
	 */
	$.articleFeedbackv5special.limit = 25;

	/**
	 * The index at which to start the pull
	 */
	$.articleFeedbackv5special.offset = 0;

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
		$( '#aft5-filter' ).bind( 'change', function( e ) {
			$.articleFeedbackv5special.filter = $(this).val();
			$.articleFeedbackv5special.loadFeedback( true );
			return false;
		} );
		$( '.aft5-sort-link' ).bind( 'click', function( e ) {
			$.articleFeedbackv5special.sort = $.articleFeedbackv5special.stripID( this, 'articlefeedbackv5-special-sort-' );
			$.articleFeedbackv5special.loadFeedback( true );
			return false;
		} );
		$( '#aft5-show-more' ).bind( 'click', function( e ) {
			$.articleFeedbackv5special.offset += $.articleFeedbackv5special.limit;
			$.articleFeedbackv5special.loadFeedback( false );
			return false;
		} );
		$( '.aft5-abuse-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.abuseFeedback( $.articleFeedbackv5special.stripID( this, 'aft5-abuse-link-' ) );
			return false;
		} );
		$( '.aft5-hide-link' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.hideFeedback( $.articleFeedbackv5special.stripID( this, 'aft5-hide-link-' ) );
			return false;
		} );
	}

	// Utility method for stripping long IDs down to the specific bits we care about.
	$.articleFeedbackv5special.stripID = function ( object, toRemove ) {
		return $( object ).attr( 'id' ).replace( toRemove, '' );
	}

	// }}}
	// {{{ Moderation methods

	// {{{ hideFeedback

	/**
	 * Hides a response
	 *
	 * @param id int the feedback id
	 */
	$.articleFeedbackv5special.hideFeedback = function ( id ) {
		$.articleFeedbackv5special.flagFeedback( id, 'hide' );
	}

	// }}}
	// {{{ abuseFeedback

	/**
	 * Flags a response as abuse
	 *
	 * @param id int the feedback id
	 */
	$.articleFeedbackv5special.abuseFeedback = function ( id ) {
		$.articleFeedbackv5special.flagFeedback( id, 'abuse' );
	}

	// }}}
	// {{{ flagFeedback

	/**
	 * Sends the request to mark a response
	 *
	 * @param id   int    the feedback id
	 * @param type string the type of mark ('hide' or 'abuse')
	 */
	$.articleFeedbackv5special.flagFeedback = function ( id, type ) {
		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'POST',
			'dataType': 'json',
			'data'    : {
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
				$( '#aft5-' + type + '-link-' + id ).text( mw.msg( msg ) );
			},
			'error': function ( data ) {
				$( '#aft5-' + type + '-link-' + id ).text( mw.msg( 'articlefeedbackv5-error-flagging' ) );
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
				'afvfpageid': $.articleFeedbackv5special.page,
				'afvffilter': $.articleFeedbackv5special.filter,
				'afvfsort'  : $.articleFeedbackv5special.sort,
				'afvflimit' : $.articleFeedbackv5special.limit,
				'afvfoffset': $.articleFeedbackv5special.offset,
				'action'  : 'query',
				'format'  : 'json',
				'list'    : 'articlefeedbackv5-view-feedback',
				'maxage'  : 0
			},
			'success': function ( data ) {
				if ( 'articlefeedbackv5-view-feedback' in data ) {
					if ( resetContents ) {
						$( '#aft5-show-feedback' ).html( data['articlefeedbackv5-view-feedback'].feedback);
					} else {
						$( '#aft5-show-feedback' ).append( data['articlefeedbackv5-view-feedback'].feedback);
					}
					$( '#aft5-feedback-count-total' ).text( data['articlefeedbackv5-view-feedback'].count );
				} else {
					$( '#aft5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				}
			},
			'error': function ( data ) {
				$( '#aft5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
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
	$.articleFeedbackv5special.loadFeedback( true );

// }}}

} );

