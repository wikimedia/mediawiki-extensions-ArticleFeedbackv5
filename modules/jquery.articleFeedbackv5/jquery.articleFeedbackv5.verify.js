/**
 * ArticleFeedback verification plugin
 *
 * This file checks to make sure that AFT is allowed on the current page.
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

( function ( $ ) {

// {{{ aftVerify definition

	$.aftVerify = {};

	// {{{ Properties

	/**
	 * The current namespace
	 */
	$.aftVerify.namespace = -2;

	/**
	 * The article's whitelist status
	 */
	$.aftVerify.whitelist = -1;

	/**
	 * The page ID
	 */
	$.aftVerify.pageId = -1;

	/**
	 * The current location
	 *
	 * Can be: article, talk, special, or unknown
	 */
	$.aftVerify.location = 'unknown';

	/**
	 * Whether AFT should be enabled on this page
	 */
	$.aftVerify.enabled = undefined;

	/**
	 * The results of individual checks
	 */
	$.aftVerify.checks = {
		whitelist: undefined,
		lottery:   undefined,
		useragent: undefined,
		article:   undefined
	};

	// }}}
	// {{{ verify

	/**
	 * Runs verification
	 *
	 * @param  location string  the place from which this is being called
	 * @return bool     whether AFTv5 is enabled for this page
	 */
	$.aftVerify.verify = function ( location ) {
		// Initialize
		$.aftVerify.enabled = undefined;
		$.aftVerify.checks.whitelist = undefined;
		$.aftVerify.checks.lottery = undefined;
		$.aftVerify.checks.useragent = undefined;
		$.aftVerify.checks.article = undefined;

		// Pull info passed in
		$.aftVerify.location = location;

		// Regardless of black-/whitelisting or lottery, always display the special page
		if ( $.aftVerify.location == 'special' ) {
			$.aftVerify.enabled = true;
		} else {
			$.aftVerify.namespace = mw.config.get( 'wgNamespaceNumber', -2 );
			$.aftVerify.whitelist = mw.config.get( 'aftv5Whitelist', -1 );

			if ( $.aftVerify.location == 'article' ) {
				// Articles use the built-in page ID
				$.aftVerify.pageId = mw.config.get( 'wgArticleId', -1 );
			} else {
				// Talk and special pages have one passed in
				$.aftVerify.pageId = mw.config.get( 'aftv5PageId', -1 );
			}

			// Case 1: the html is cached and we don't know if it's whitelisted
			if ( $.aftVerify.whitelist == -1 ) {
				if ( $.aftVerify.location == 'article' ) {
					// We can double-check, so do that
					$.aftVerify.enabled = $.aftVerify.checkFull();
				} else {
					// Everywhere else: don't show it
					$.aftVerify.enabled = false;
				}

			// Case 2: the article is whitelisted
			} else if ( $.aftVerify.whitelist ) {
				$.aftVerify.checks.whitelist = true;
				$.aftVerify.enabled = true;

			// Case 3: the article is not whitelisted
			} else {
				$.aftVerify.checks.whitelist = false;
				$.aftVerify.enabled = $.aftVerify.checkLottery();
			}
		}

		// Check the user agent
		if ( $.aftVerify.enabled ) {
			$.aftVerify.enabled = $.aftVerify.checkUserAgent();
		}

		return $.aftVerify.enabled;
	};

	// }}}
	// {{{ checkFull

	/**
	 * If this is a main article, we can check everything at once
	 *
	 * @return bool whether AFTv5 is enabled
	 */
	$.aftVerify.checkFull = function () {
		if (
			// Only on pages in namespaces where it is enabled
			$.inArray( mw.config.get( 'wgNamespaceNumber' ), mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ) ) > -1
			// Existing pages
			&& mw.config.get( 'wgArticleId' ) > 0
			// View pages
			&& ( mw.config.get( 'wgAction' ) == 'view' || mw.config.get( 'wgAction' ) == 'purge' )
			// If user is logged in, showing on action=purge is OK,
			// but if user is logged out, action=purge shows a form instead of the article,
			// so return false in that case.
			&& !( mw.config.get( 'wgAction' ) == 'purge' && mw.user.anonymous() )
			// Current revision
			&& mw.util.getParamValue( 'diff' ) == null
			&& mw.util.getParamValue( 'oldid' ) == null
			// Not disabled via preferences
			&& !mw.user.options.get( 'articlefeedback-disable' )
			// Not viewing a redirect
			&& mw.util.getParamValue( 'redirect' ) != 'no'
			// Not viewing the printable version
			&& mw.util.getParamValue( 'printable' ) != 'yes'
		) {
			$.aftVerify.checks.article = true;

			// Collect categories for intersection tests
			// Clone the arrays so we can safely modify them
			var whitelist = false;
			var categories = {
				'include': [].concat( mw.config.get( 'wgArticleFeedbackv5Categories', [] ) ),
				'exclude': [].concat( mw.config.get( 'wgArticleFeedbackv5BlacklistCategories', [] ) ),
				'current': [].concat( mw.config.get( 'wgCategories', [] ) )
			};
			for ( var i = 0; i < categories['current'].length; i++ ) {
				// Categories are configured with underscores, but article's categories are returned with
				// spaces instead. Revert to underscores here for sane comparison.
				categories['current'][i] = categories['current'][i].replace(/\s/gi, '_');
				// Check exclusion - exclusion overrides everything else
				if ( $.inArray( categories['current'][i], categories.exclude ) > -1 ) {
					// Blacklist overrides everything else
					return false;
				}
				if ( $.inArray( categories['current'][i], categories.include ) > -1 ) {
					// One match is enough for include, however we are iterating on the 'current'
					// categories, and others might be blacklisted - so continue iterating
					whitelist = true;
				}
			}
			if ( whitelist ) {
				$.aftVerify.checks.whitelist = true;
				return true;
			} else {
				return $.aftVerify.checkLottery();
			}

		} else {
			$.aftVerify.checks.article = false;
			return false;
		}
	};

	// }}}
	// {{{ checkLottery

	/**
	 * Check the lottery
	 *
	 * AFT5's lottery is the inverse of AFT4's.
	 *
	 * @return bool whether AFTv5 is enabled
	 */
	$.aftVerify.checkLottery = function () {
		// No page id, no check
		if ( $.aftVerify.pageId < 1 ) {
			// Special pages: zero = central feedback page
			if ( $.aftVerify.location == 'special' && $.aftVerify.pageId == 0 ) {
				$.aftVerify.checks.lottery = true;
				return true;
			} else {
				$.aftVerify.checks.lottery = false;
				return false;
			}
		}
		// Lottery
		var v4odds = mw.config.get( 'wgArticleFeedbackLotteryOdds', 0 );
		if ( !( ( Number( $.aftVerify.pageId ) % 1000 ) < Number( v4odds ) * 10 ) ) {
			$.aftVerify.checks.lottery = true;
			return true;
		} else {
			$.aftVerify.checks.lottery = false;
			return false;
		}
	};

	// }}}
	// {{{ checkUserAgent

	/**
	 * Check the user agent
	 */
	$.aftVerify.checkUserAgent = function () {
		var ua = navigator.userAgent.toLowerCase();
		// Rule out MSIE 6/7, FF2, iPhone, iPod, iPad, Android
		if (
			(ua.indexOf( 'msie 6' ) != -1) ||
			(ua.indexOf( 'firefox/2') != -1) ||
			(ua.indexOf( 'firefox 2') != -1) ||
			(ua.indexOf( 'android' ) != -1) ||
			(ua.indexOf( 'iphone' ) != -1) ||
			(ua.indexOf( 'ipod' ) != -1 ) ||
			(ua.indexOf( 'ipad' ) != -1)
		) {
			$.aftVerify.checks.useragent = false;
			return false;
		} else {
			$.aftVerify.checks.useragent = true;
			return true;
		}
	};

	// }}}

// }}}

} )( jQuery );

