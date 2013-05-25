/**
 * Script for Article Feedback Extension: Article pages
 */
( function( mw, $ ) {

// Is AFT enabled here?
if ( $.aftUtils.verify( 'article' ) ) {
	var load, enable, userPermissions, $link, url, displayForm, loadInterval;

	// start AFTv5
	load = function() {
		var removeAft, removeAftInterval;
		// remove (if any) occurrences of older AFT variations
		removeAft = function() {
			var $aft = $( '#mw-articlefeedback' );

			if ( $aft.length > 0 ) {
				$aft.remove();
			} else {
				clearInterval( removeAftInterval );
			}
		};
		removeAftInterval = setInterval( removeAft, 100 );

		// load AFTv5
		mw.loader.load( 'ext.articleFeedbackv5' );
		// Load the IE-specific module
		if ( navigator.appVersion.indexOf( 'MSIE 7' ) !== -1 ) {
			mw.loader.load( 'ext.articleFeedbackv5.ie' );
		}
	};

	// check if AFT is enabled (for this user)
	enable = $.aftUtils.verify( 'article' );
	if ( enable ) {
		load();
	}

	// check if user can enable AFTv5
	if ( $.aftUtils.canSetStatus( true ) ) {
		userPermissions = mw.config.get( 'wgArticleFeedbackv5Permissions' );

		// build link to enable feedback form
		$link = $( '<li id="t-articlefeedbackv5-enable"><a href="#"></a></li>' );
		$link.find( 'a' ).text( mw.msg( 'articlefeedbackv5-toolbox-enable' ) );

		// administrators can change detailed visibility in ?action=protect
		if ( 'aft-administrator' in userPermissions && userPermissions['aft-administrator'] ) {
			url = mw.config.get( 'wgScript' ) + '?title=' +
				encodeURIComponent( mw.config.get( 'wgPageName' ) ) +
				'&' + $.param( { action: 'protect' } );

			$link.find( 'a' ).attr( 'href', url );

		// editors can enable/disable for readers via API
		} else {
			$link.find( 'a' ).on( 'click', function( e ) {
				e.preventDefault();

				$.aftUtils.setStatus( $.aftUtils.article().id, 1, function( data, error ) {
					if ( data !== false ) {
						$link.remove();

						// display AFTv5 tool (unless this user already had access, in which case it's already visible)
						if ( !enable ) {
							// not async; we want to make sure AFTv5 is loaded before code below is executed
							load( false );
						}

						displayForm = function() {
							var $form = $( '#mw-articlefeedbackv5' );

							if ( $form.length > 0 ) {
								// scroll to/highlight AFTv5 form
								$.articleFeedbackv5.highlightForm();

								// add message to confirm AFTv5 has just been enabled
								url = mw.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' + mw.config.get( 'wgPageName' );
									$( '<p id="articleFeedbackv5-added"></p>' )
										.msg( 'articlefeedbackv5-enabled-form-message', url )
										.appendTo( $form );

								// remove timer
								clearTimeout( loadInterval );
							}
						};

						// AFT is loaded async; keep polling until it's ready
						loadInterval = setInterval( displayForm, 100 );

					} else if ( error ) {
						/* jshint devel: true */
						alert( error );
					}
				} );
			});
		}

		$( '#p-tb' ).find( 'ul' ).append( $link );
	}
}

} )( mediaWiki, jQuery );
