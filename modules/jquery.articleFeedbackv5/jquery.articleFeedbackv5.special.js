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
		continue: null
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
		var id = window.location.hash.match(/id=(\d+)/)
		if( id ) {
			$.articleFeedbackv5special.listControls.filter      = 'id';
			$.articleFeedbackv5special.listControls.filterValue = id[1];
		}

		// Grab the user's activity out of the cookie
		$.articleFeedbackv5special.activityCookieName += $.articleFeedbackv5special.page;
		$.articleFeedbackv5special.loadActivity();

		// Initial load
		$.articleFeedbackv5special.loadFeedback( true );
	};

	// }}}
	// {{{ setBinds

	/**
	 * Binds events for each of the controls
	 */
	$.articleFeedbackv5special.setBinds = function() {
		$( '#articleFeedbackv5-filter-select' ).bind( 'change', function( e ) {
			$.articleFeedbackv5special.listControls.filter   = $(this).val();
			$.articleFeedbackv5special.listControls.continue = null;
			$.articleFeedbackv5special.loadFeedback( true );
			return false;
		} );

		$( '.articleFeedbackv5-sort-link' ).bind( 'click', function( e ) {
			id     = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-special-sort-' );
			oldId  = $.articleFeedbackv5special.listControls.sort;

			// set direction = desc...
			$.articleFeedbackv5special.listControls.sort     = id;
			$.articleFeedbackv5special.listControls.continue = null;

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
			id = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-permalink-' );
			$.articleFeedbackv5special.listControls.filter      = 'id';
			$.articleFeedbackv5special.listControls.filterValue = id;
			$.articleFeedbackv5special.listControls.continue    = null;
			$.articleFeedbackv5special.loadFeedback( true );
		} );

		$( '.articleFeedbackv5-comment-toggle' ).live( 'click', function( e ) {
			$.articleFeedbackv5special.toggleComment( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-comment-toggle-' ) );
			return false;
		} );

		// Helpful and unhelpful have their own special logic, so break those out.
		$.each( ['helpful', 'unhelpful' ], function ( index, value ) { 
			$( '.articleFeedbackv5-' + value + '-link' ).live( 'click', function( e ) {
				e.preventDefault();
				var $l = $( e.target );
				var id = $l.parents( '.articleFeedbackv5-feedback' ).attr( 'rel' );
				var activity = $.articleFeedbackv5special.getActivity( id );
				if ( activity[value] ) {
					return false;
				}
				if ( 'helpful' == value && activity.unhelpful ) {
					$.articleFeedbackv5special.flagFeedback( id, 'unhelpful', -1 );
					$.articleFeedbackv5special.flagFeedback( id, 'helpful', 1 );
				} else if ( 'unhelpful' == value && activity.helpful ) {
					$.articleFeedbackv5special.flagFeedback( id, 'helpful', -1 );
					$.articleFeedbackv5special.flagFeedback( id, 'unhelpful', 1 );
				} else {
					$.articleFeedbackv5special.flagFeedback( id, value, 1 );
				}
			} )
		} );

		$.each( ['unhide', 'undelete', 'oversight', 'hide', 'abuse', 'delete', 'unoversight'], function ( index, value ) { 
			$( '.articleFeedbackv5-' + value + '-link' ).live( 'click', function( e ) {
				$.articleFeedbackv5special.flagFeedback( $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-' + value + '-link-' ), value, 1 );
			} )
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
		id  = $.articleFeedbackv5special.listControls.sort;
		dir = $.articleFeedbackv5special.listControls.sortDirection;

		$( '.articleFeedbackv5-sort-arrow' ).hide();
		$( '.articleFeedbackv5-sort-link' ).removeClass( 'sort-active' );

		$( '#articleFeedbackv5-sort-arrow-' + id ).show();
		$( '#articleFeedbackv5-sort-arrow-' + id ).attr(
			'src', '/extensions/ArticleFeedbackv5/modules/jquery.articleFeedbackv5/images/sort-' + dir + 'ending.png'
		);
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

	// }}}
	// {{{ Process methods

	// {{{ flagFeedback

	/**
	 * Sends the request to mark a response
	 *
	 * @param id   int    the feedback id
	 * @param type string the type of mark (valid values: hide, abuse, delete, helpful, unhelpful)
	 * @param dir  int    the direction of the mark (-1 = tick down; 1 = tick up)
	 */
	$.articleFeedbackv5special.flagFeedback = function ( id, type, dir ) {
		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'POST',
			'dataType': 'json',
			'data'    : {
				'pageid'    : $.articleFeedbackv5special.page,
				'feedbackid': id,
				'flagtype'  : type,
				'direction' : dir > 0 ? 'increase' : 'decrease',
				'format'    : 'json',
				'action'    : 'articlefeedbackv5-flag-feedback'
			},
			'success': function ( data ) {
				var msg = 'articlefeedbackv5-error-flagging';
				if ( 'articlefeedbackv5-flag-feedback' in data ) {
					if ( 'result' in data['articlefeedbackv5-flag-feedback'] ) {
						if ( data['articlefeedbackv5-flag-feedback'].result == 'Success' ) {
							msg = 'articlefeedbackv5-' + type + '-saved';
							if ( 'helpful' in data['articlefeedbackv5-flag-feedback'] ) {
								$( '#articleFeedbackv5-helpful-votes-' + id ).text( data['articlefeedbackv5-flag-feedback'].helpful );
							}
							if ( 'helpful' == type || 'unhelpful' == type ) {
								var $l = $( '#articleFeedbackv5-' + type + '-link-' + id );
								if ( dir > 0 ) {
									$l.addClass( 'helpful-active' );
								} else {
									$l.removeClass( 'helpful-active' );
								}
							}
							// Save activity
							if ( !( id in $.articleFeedbackv5special.activity ) ) {
								$.articleFeedbackv5special.activity[id] = { helpful: false, unhelpful: false, abuse: false, hide: false, delete: false };
							}
							$.articleFeedbackv5special.activity[id][type] = dir > 0 ? true : false;
							$.articleFeedbackv5special.storeActivity();
						} else if ( data['articlefeedbackv5-flag-feedback'].result == 'Error' ) {
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
					var $newList = $( '<div></div>' ).html( data['articlefeedbackv5-view-feedback'].feedback );
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
						}
					} );
					$( '#articleFeedbackv5-show-feedback' ).append( $newList );
					$( '#articleFeedbackv5-feedback-count-total' ).text( data['articlefeedbackv5-view-feedback'].count );
					$.articleFeedbackv5special.listControls.continue = data['articlefeedbackv5-view-feedback'].continue;
					// set effects on toolboxes
					$( '.articleFeedbackv5-feedback-tools > ul' ).hide();
					$( '.articleFeedbackv5-feedback-tools' ).hover( 
						function( eventObj ) {
							//alert(this);
							var id = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-feedback-tools-' );
							$( '#articleFeedbackv5-feedback-tools-list-' + id ).slideDown( 200 );
						},
						function( eventObj ) {
							var id = $.articleFeedbackv5special.stripID( this, 'articleFeedbackv5-feedback-tools-' );
							$( '#articleFeedbackv5-feedback-tools-list-' + id ).slideUp( 200 );
						}
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

