/**
 * Script for Article Feedback Extension: Article pages
 */

/*** Main entry point ***/
jQuery( function( $ ) {
	var removeOld, load, statusChangeSuccess, statusCallback,
		enable = $.aftUtils.verify( 'article' );

	/**
	 * Remove (if any) occurrences of older AFT variations.
	 *
	 * Since we have no control over when (if ever) it'll pop into DOM, poll
	 * until it arrives (but give up after a couple of seconds).
	 */
	removeOld = function() {
		var remove, interval,
			initTime = new Date();

		remove = function() {
			var $aft = $( '#mw-articlefeedback' ),
				timeDiff = ( ( new Date() ).getTime() - initTime.getTime() ) / 1000;

			if ( $aft.length > 0 ) {
				$aft.remove();
				clearInterval( interval );

			// after unsuccessfully polling for 5 seconds, give up
			} else if ( timeDiff > 5 ) {
				clearInterval( interval );
			}
		};
		interval = setInterval( remove, 100 );
	};

	/**
	 * Load AFTv5 modules.
	 */
	load = function() {
		removeOld();

		// load AFTv5
		mediaWiki.loader.load( 'ext.articleFeedbackv5' );
		// Load the IE-specific module
		if ( navigator.appVersion.indexOf( 'MSIE 7' ) !== -1 ) {
			mediaWiki.loader.load( 'ext.articleFeedbackv5.ie' );
		}
	};

	/**
	 * After successfully enabling AFTv5, scroll down to and display AFTv5 form,
	 * along with a confirmation message.
	 *
	 * Since we have no control over when the form will pop into DOM, poll
	 * until it arrives.
	 */
	statusChangeSuccess = function() {
		var display, interval;

		display = function() {
			var $form = $( '#mw-articlefeedbackv5' ), link;

			if ( $form.length === 0 ) {
				return;
			}

			// scroll to/highlight AFTv5 form
			$.articleFeedbackv5.highlightForm();

			// add message to confirm AFTv5 has just been enabled
			link = mediaWiki.config.get( 'wgArticleFeedbackv5SpecialUrl' ) + '/' + mediaWiki.config.get( 'wgPageName' );
				$( '<p id="articleFeedbackv5-added"></p>' )
					.msg( 'articlefeedbackv5-enabled-form-message', link )
					.appendTo( $form );

			// we're done; stop polling
			clearTimeout( interval );
		};
		interval = setInterval( display, 100 );
	};
	
	var $link;

	/**
	 * Load AFTv5 after it has been enabled.
	 *
	 * @param object|false data API JSON response or false on failure
	 * @param object|null error API JSON error response or null if API call was successful
	 */
	statusCallback = function( data, error ) {
		if ( data !== false ) {
			$link.remove();

			// display AFTv5 tool (unless this user already had access, in which case it's already visible)
			if ( !enable ) {
				load();
			}

			statusChangeSuccess();

		} else if ( error ) {
			alert( error );
		}
	};

	// check if AFT is enabled (for this user)
	if ( enable ) {
		load();
	}

	var article = $.aftUtils.article();

	/*
	 * Check if a user can enable AFTv5.
	 *
	 * Don't show the link if page is enabled/disabled via the categories.
	 * To change that, one would have to edit the page and remove that
	 * category, not change it via the link for page protection that we'll
	 * be displaying here.
	 */
	if (
		mediaWiki.config.get( 'wgArticleFeedbackv5EnableProtection', 1 ) &&
		!$.aftUtils.whitelist( article ) &&
		!$.aftUtils.blacklist( article ) &&
		$.aftUtils.canSetStatus( true )
	) {
		var userPermissions = mediaWiki.config.get( 'wgArticleFeedbackv5Permissions' );

		// build link to enable feedback form
		$link = $( '<li id="t-articlefeedbackv5-enable"><a href="#"></a></li>' );
		$link.find( 'a' ).text( mediaWiki.msg( 'articlefeedbackv5-toolbox-enable' ) );

		// administrators can change detailed visibility in ?action=protect
		if ( 'aft-administrator' in userPermissions && userPermissions['aft-administrator'] ) {
			var link = mediaWiki.config.get( 'wgScript' ) + '?title=' +
				encodeURIComponent( mediaWiki.config.get( 'wgPageName' ) ) +
				'&' + $.param( { action: 'protect' } );

			$link.find( 'a' ).attr( 'href', link );

		// editors can enable/disable for readers via API
		} else {
			$link.find( 'a' ).on( 'click', function( e ) {
				e.preventDefault();

				$.aftUtils.setStatus( article.id, 1, statusCallback );
			});
		}

		$( '#p-tb' ).find( 'ul' ).append( $link );
	}

} );
