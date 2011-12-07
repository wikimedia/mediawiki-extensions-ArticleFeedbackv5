( function ( $ ) {
	$.articleFeedbackv5special = {};

	// TODO: Pass this in better from the PHP side.
	$.articleFeedbackv5special.page    = hackPageId;
	$.articleFeedbackv5special.filter  = 'all';
	$.articleFeedbackv5special.sort    = 'newest';
	$.articleFeedbackv5special.limit   = 25;
	$.articleFeedbackv5special.offset  = 0;
	$.articleFeedbackv5special.showing = 0;
	$.articleFeedbackv5special.apiUrl  = undefined; 

	$.articleFeedbackv5special.setBinds = function() {
		$( '#aft5-filter' ).bind( 'change', function(e) {
			$.articleFeedbackv5special.filter = $(this).val();
			$.articleFeedbackv5special.loadFeedback();
			return false;
		} );
		$( '#aft5-sort' ).bind( 'change', function(e) {
			$.articleFeedbackv5special.sort = $(this).val();
			$.articleFeedbackv5special.loadFeedback();
			return false;
		} );
		$( '#aft5-show-more' ).bind( 'click', function(e) {
			$.articleFeedbackv5special.offset += 
			 $.articleFeedbackv5special.limit;
			$.articleFeedbackv5special.loadFeedback();
			return false;
		} );
		$( '.aft5-abuse-link' ).live( 'click', function(e) {
			var id = $( this ).attr( 'id' ).replace( 'aft5-abuse-link-', '' );
			$.articleFeedbackv5special.abuseFeedback( id );
			return false;
		} );
		$( '.aft5-hide-link' ).live( 'click', function(e) {
			var id = $( this ).attr( 'id' ).replace( 'aft5-hide-link-', '' );
			$.articleFeedbackv5special.hideFeedback( id );
			return false;
		} );
	}

	$.articleFeedbackv5special.hideFeedback = function ( id ) {
		$.articleFeedbackv5special.flagFeedback( id, 'hide' );
	}

	$.articleFeedbackv5special.abuseFeedback = function ( id ) {
		$.articleFeedbackv5special.flagFeedback( id, 'abuse' );
	}

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
			'failure': function ( data ) {
				$( '#aft5-' + type + '-link-' + id ).text( mw.msg( 'articlefeedbackv5-error-flagging' ) );
			}
		} );
		return false;
	}

	$.articleFeedbackv5special.loadFeedback = function () {
		$.ajax( {
			'url'     : $.articleFeedbackv5special.apiUrl,
			'type'    : 'GET',
			'dataType': 'json',
			'data'    : {
				'afpageid': $.articleFeedbackv5special.page,
				'affilter': $.articleFeedbackv5special.filter,
				'afsort'  : $.articleFeedbackv5special.sort,
				'aflimit' : $.articleFeedbackv5special.limit,
				'afoffset': $.articleFeedbackv5special.offset,
				'action'  : 'query',
				'format'  : 'json',
				'list'    : 'articlefeedbackv5-view-feedback',
				'maxage'  : 0
			},
			'success': function ( data ) {
				if ( 'data' in data ) {
					$( '#aft5-show-feedback' ).append( data.data.feedback);
					$.articleFeedbackv5special.showing += data.data.length;
					$( '#aft5-feedback-count-shown' ).text( $.articleFeedbackv5special.showing );
					$( '#aft5-feedback-count-total' ).text( data.data.count );
					if ( $.articleFeedbackv5special.showing >= data.data.count ) {
						$( '#aft5-show-more' ).hide();
					}

				} else {
					$( '#aft5-show-feedback' ).text( mw.msg( 'articlefeedbackv5-error-loading-feedback' ) );
				}
			}
			// TODO: have a callback for failures.
		} );

		return false;
	}
} )( jQuery );

$( document ).ready( function() {
	// This was failing sometimes when it was in the function above.
	// I think it maky have been a race condition.
	$.articleFeedbackv5special.apiUrl  = mw.util.wikiScript('api');

	// Blank out the 'loading' text
	$( '#aft5-show-feedback' ).text( ' ' );

	// Set up event binds and do initial data fetch.
	$.articleFeedbackv5special.setBinds();
	$.articleFeedbackv5special.loadFeedback();
} );
