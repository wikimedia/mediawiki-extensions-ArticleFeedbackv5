/**
 * ArticleFeedback verification plugin
 *
 * This file checks to make sure that AFT is allowed on the current page.
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthas Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

( function ( $ ) {

// {{{ aftVerify definition

	$.aftVerify = {};

	// {{{ legacyCorrection

	/**
	 * During cleanup, some javascript variables have been tossed around.
	 * It may occur that page output will still be cached, but new (= this)
	 * javascript will already be served. In these cached cases, this
	 * javascript will lack the variables it assumes.
	 *
	 * This function will pre-fill this data with what we used to be able
	 * to fetch (and might still be hit with due to cache)
	 */
	$.aftVerify.legacyCorrection = function () {
		// check if data is already present
		if ( mw.config.get( 'aftv5Article' ) ) {
			return;
		}

		var article = {};

		// these had an older equivalent
		article.id = mw.config.get( 'aftv5PageId', -1 );
		article.title = mw.config.get( 'aftv5PageTitle', '' );

		// article were only supported on NS_MAIN, so assume the default
		article.namespace = 0;

		// we have no idea about the categories, but we did know if an article
		// was whitelisted, so in that case: fill it with whitelisted categories
		if ( mw.config.get( 'aftv5Whitelist' ) ) {
			article.categories = mw.config.get( 'wgArticleFeedbackv5Categories', [] );
		}

		// permission levels had not yet been introduced, so assume the default
		article.permissionLevel = 'aft-reader';

		mw.config.set( 'aftv5Article', article );
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
		// make sure we have all data - even on old cached pages
		$.aftVerify.legacyCorrection();

		var article = mw.config.get( 'aftv5Article' );

		// fetch data, on article level, we can fetch these from other sources as well
		if ( location == 'article' ) {
			article.id = mw.config.get( 'wgArticleId', -1 );
			article.namespace = mw.config.get( 'wgNamespaceNumber' );
			article.categories = mw.config.get( 'wgCategories', [] );
		}


		var enable = true;

		// supported browser
		enable &= $.aftVerify.useragent();

		// not disabled via preferences
		enable &= !mw.user.options.get( 'articlefeedback-disable' );

		// page permission check is not applicable for central feedback page
		if ( location != 'special' || article.id != 0 ) {
			// only on pages in namespaces where it is enabled
			enable &= $.inArray( article.namespace, mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ) ) > -1;

			// check if user has the required permissions
			enable &= $.aftVerify.permissions( article );
		}

		// for special page, it doesn't matter if the article has AFT applied
		if ( location != 'special' ) {
			// category is not blacklisted
			enable &= !$.aftVerify.blacklist( article );

			// category is whitelisted or article is in lottery
			enable &= ( $.aftVerify.whitelist( article ) || $.aftVerify.lottery( article ) );
		}

		// stricter validation for article: make sure we're at the right article view
		if ( location == 'article' ) {
			// view pages
			enable &= ( mw.config.get( 'wgAction' ) == 'view' || mw.config.get( 'wgAction' ) == 'purge' );

			// if user is logged in, showing on action=purge is OK,
			// but if user is logged out, action=purge shows a form instead of the article,
			// so return false in that case.
			enable &= !( mw.config.get( 'wgAction' ) == 'purge' && mw.user.anonymous() );

			// current revision
			enable &= mw.util.getParamValue( 'diff' ) == null;
			enable &= mw.util.getParamValue( 'oldid' ) == null;

			// not viewing a redirect
			enable &= mw.util.getParamValue( 'redirect' ) != 'no';

			// not viewing the printable version
			enable &= mw.util.getParamValue( 'printable' ) != 'yes';
		}

		return enable;
	};

	// }}}
	// {{{ permissions

	/**
	 * Check if the user is permitted to see the AFT feedback form
	 * on this particular page, as defined by its protection level
	 *
	 * @param object article
	 * @return bool
	 */
	$.aftVerify.permissions = function ( article ) {
		var permissions = mw.config.get( 'wgArticleFeedbackv5Permissions' );
		return article.permissionLevel in permissions && permissions[article.permissionLevel];
	};

	// }}}
	// {{{ blacklist

	/**
	 * Check if the article is blacklisted by intersecting the
	 * article's categories with the blacklisted categories
	 *
	 * Note: the .replace() makes sure that when blacklist category
	 * names are underscored, those are converted to spaces (cfr. category)
	 *
	 * @param object article
	 * @return bool
	 */
	$.aftVerify.blacklist = function ( article ) {
		var blacklistCategories = mw.config.get( 'wgArticleFeedbackv5BlacklistCategories', [] );
		var intersect = $.map( blacklistCategories, function( category ) {
			return $.inArray( category.replace(/_/g, ' '), article.categories ) < 0 ? null : category;
		} );
		return intersect.length > 0;
	};

	// }}}
	// {{{ whitelist

	/**
	 * Check if the article is whitelisted by intersecting the
	 * article's categories with the whitelisted categories
	 *
	 * Note: the .replace() makes sure that when whitelist category
	 * names are underscored, those are converted to spaces (cfr. category)
	 *
	 * @param object article
	 * @return bool
	 */
	$.aftVerify.whitelist = function ( article ) {
		var whitelistCategories = mw.config.get( 'wgArticleFeedbackv5Categories', [] );
		var intersect = $.map( whitelistCategories, function( category ) {
			return $.inArray( category.replace(/_/g, ' '), article.categories ) < 0 ? null : category;
		} );
		return intersect.length > 0;
	};

	// }}}
	// {{{ lottery

	/**
	 * Check if an article is eligible for AFT through the lottery
	 *
	 * Note: odds can either be a plain integer (0-100), or be defined per namespace
	 * (0-100 per namespace key)
	 *
	 * @param object article
	 * @return bool
	 */
	$.aftVerify.lottery = function ( article ) {
		var odds = mw.config.get( 'wgArticleFeedbackv5LotteryOdds', 0 );
		odds = article.namespace in odds ? odds[article.namespace] : parseInt( odds );
		return ( Number( article.id ) % 1000 ) > ( 1000 - ( Number( odds ) * 10 ) );
	};

	// }}}
	// {{{ useragent

	/**
	 * Check if the browser is supported
	 *
	 * @return bool
	 */
	$.aftVerify.useragent = function () {
		var ua = navigator.userAgent.toLowerCase();

		// Rule out MSIE 6, FF2, Android
		return !(
			ua.indexOf( 'msie 6' ) != -1 ||
			ua.indexOf( 'firefox/2') != -1 ||
			ua.indexOf( 'firefox 2') != -1 ||
			ua.indexOf( 'android' ) != -1
		);
	};

	// }}}

// }}}

} )( jQuery );
