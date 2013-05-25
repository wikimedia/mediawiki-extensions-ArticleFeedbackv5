/**
 * ArticleFeedback verification plugin
 *
 * This file checks to make sure that AFT is allowed on the current page.
 *
 * @package    ArticleFeedback
 * @subpackage Resources
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
( function( mw, $ ) {

// {{{ aftUtils definition

$.aftUtils = {};

// }}}
// {{{ article

/**
 * Get article info
 *
 * @return object
 */
$.aftUtils.article = function () {
	// clone object
	var article = jQuery.extend( {}, mw.config.get( 'aftv5Article' ) );

	// fetch data, on article level, we can fetch these from other sources as well
	if ( $.inArray( mw.config.get( 'wgNamespaceNumber' ), mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ) ) > -1 ) {
		article.id = mw.config.get( 'wgArticleId', -1 );
		article.namespace = mw.config.get( 'wgNamespaceNumber' );
		article.categories = mw.config.get( 'wgCategories', [] );
	}

	return article;
};

// }}}
// {{{ verify

/**
 * Runs verification
 *
 * @param  location string  the place from which this is being called
 * @return bool     whether AFTv5 is enabled for this page
 */
$.aftUtils.verify = function ( location ) {
	/* jshint bitwise: false */

	var article, enable;

	article = $.aftUtils.article();
	enable = true;

	// supported browser
	enable &= $.aftUtils.useragent();

	// if AFTv5 is not enabled on any namespace, it does not make sense to display it at all
	enable &= mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ).length > 0;

	if ( location !== 'special' || article.id !== 0 ) {
		// only on pages in namespaces where it is enabled
		enable &= $.inArray( article.namespace, mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ) ) > -1;

		// it does not make sense to display AFT when a page is being edited ...
		enable &= mw.config.get( 'wgAction' ) !== 'edit';

		// ... or has just been edited
		enable &= !mw.config.get( 'wgPostEdit', false );
	}

	// for special page, it doesn't matter if the article has AFT applied
	if ( location !== 'special' ) {
		// check if user has the required permissions
		enable &= $.aftUtils.permissions( article );

		// category is not blacklisted
		enable &= !$.aftUtils.blacklist( article );

		// category is whitelisted or article is in lottery
		enable &= ( $.aftUtils.whitelist( article ) || $.aftUtils.lottery( article ) );
	}

	// stricter validation for article: make sure we're at the right article view
	if ( location === 'article' ) {
		// not disabled via preferences
		enable &= !mw.user.options.get( 'articlefeedback-disable' );

		// view pages
		enable &= ( mw.config.get( 'wgAction' ) === 'view' || mw.config.get( 'wgAction' ) === 'purge' );

		// if user is logged in, showing on action=purge is OK,
		// but if user is logged out, action=purge shows a form instead of the article,
		// so return false in that case.
		enable &= !( mw.config.get( 'wgAction' ) === 'purge' && mw.user.anonymous() );

		// current revision
		enable &= mw.util.getParamValue( 'diff' ) === null;
		enable &= mw.util.getParamValue( 'oldid' ) === null;

		// not viewing a redirect
		enable &= mw.util.getParamValue( 'redirect' ) !== 'no';

		// not viewing the printable version
		enable &= mw.util.getParamValue( 'printable' ) !== 'yes';
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
$.aftUtils.permissions = function ( article ) {
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
$.aftUtils.blacklist = function ( article ) {
	var blacklistCategories, intersect;

	blacklistCategories= mw.config.get( 'wgArticleFeedbackv5BlacklistCategories', [] );
	intersect = $.map( blacklistCategories, function( category ) {
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
$.aftUtils.whitelist = function ( article ) {
	var whitelistCategories, intersect;

	whitelistCategories = mw.config.get( 'wgArticleFeedbackv5Categories', [] );
	intersect = $.map( whitelistCategories, function( category ) {
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
$.aftUtils.lottery = function ( article ) {
	var odds = mw.config.get( 'wgArticleFeedbackv5LotteryOdds', 0 );

	if ( typeof odds === 'object' ) {
		if ( article.namespace in odds ) {
			odds = odds[article.namespace];
		} else {
			odds = 0;
		}
	}

	return ( Number( article.id ) % 1000 ) >= ( 1000 - ( Number( odds ) * 10 ) );
};

// }}}
// {{{ useragent

/**
 * Check if the browser is supported
 *
 * @return bool
 */
$.aftUtils.useragent = function () {
	var useragent = navigator.userAgent.toLowerCase();

	// Rule out MSIE 6, FF2, Android
	return !(
		useragent.indexOf( 'msie 6' ) !== -1 ||
		useragent.indexOf( 'firefox/2.') !== -1 ||
		useragent.indexOf( 'firefox 2.') !== -1 ||
		useragent.indexOf( 'android' ) !== -1
	);
};

// }}}
// {{{ getCookieName

/**
 * Get the full, prefixed, name that data is saved at in cookie.
 * The cookie name is prefixed by the extension name and a version number,
 * to avoid collisions with other extensions or code versions.
 *
 * @param string $suffix
 * @return string
 */
$.aftUtils.getCookieName = function ( suffix ) {
	return 'AFTv5-' + suffix;
};

// }}}

// }}}

} )( mediaWiki, jQuery );
