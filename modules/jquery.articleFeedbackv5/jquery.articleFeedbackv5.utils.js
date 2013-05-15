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
		// remove obsolete cookies
		$.aftUtils.removeLegacyCookies();


		var article = $.aftUtils.article();

		var enable = true;

		// supported browser
		enable &= $.aftUtils.useragent();

		// if AFTv5 is not enabled on any namespace, it does not make sense to display it at all
		enable &= mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ).length > 0;

		if ( location != 'special' || article.id != 0 ) {
			// only on pages in namespaces where it is enabled
			enable &= $.inArray( article.namespace, mw.config.get( 'wgArticleFeedbackv5Namespaces', [] ) ) > -1;

			// it does not make sense to display AFT when a page is being edited ...
			enable &= mw.config.get( 'wgAction' ) != 'edit';

			// ... or has just been edited
			enable &= !mw.config.get( 'wgPostEdit', false );
		}

		// for special page, it doesn't matter if the article has AFT applied
		if ( location != 'special' ) {
			// check if a, to this user sufficient, permission level is defined
			if ( article.permissionLevel !== false ) {
				enable &= $.aftUtils.permissions( article );

			// if not defined through permissions (which includes lottery), check whitelist
			} else {
				enable &= $.aftUtils.whitelist( article );
			}

			// category is not blacklisted
			enable &= !$.aftUtils.blacklist( article );
		}

		// stricter validation for article: make sure we're at the right article view
		if ( location == 'article' ) {
			// not disabled via preferences
			enable &= !mw.user.options.get( 'articlefeedback-disable' );

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
	$.aftUtils.whitelist = function ( article ) {
		var whitelistCategories = mw.config.get( 'wgArticleFeedbackv5Categories', [] );
		var intersect = $.map( whitelistCategories, function( category ) {
			return $.inArray( category.replace(/_/g, ' '), article.categories ) < 0 ? null : category;
		} );
		return intersect.length > 0;
	};

	// }}}
	// {{{ useragent

	/**
	 * Check if the browser is supported
	 *
	 * @return bool
	 */
	$.aftUtils.useragent = function () {
		var ua = navigator.userAgent.toLowerCase();

		// Rule out MSIE 6, FF2, Android
		return !(
			ua.indexOf( 'msie 6' ) != -1 ||
			ua.indexOf( 'firefox/2.') != -1 ||
			ua.indexOf( 'firefox 2.') != -1 ||
			ua.indexOf( 'android' ) != -1
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
	// {{{ removeLegacyCookies

	/**
	 * Before the current getCookieName() function, cookie names were:
	 * * really long
	 * * incorrect using the tracking version number to differentiate JS/cookie versions
	 * * not being prefixed by wgCookiePrefix
	 *
	 * These issues have since been fixed, but this will make sure that lingering old
	 * cookie are cleaned up. This function will not merge the old cookies to the new
	 * cookie name though.
	 *
	 * @deprecated Function is only intended to bridge a temporary "gap" while old
	 *             data persists in cookie. After awhile, cookies have either expired
	 *             by themselves or this will have cleaned them up, so this function
	 *             (and where it's being called) can be cleaned up at will.
	 */
	$.aftUtils.removeLegacyCookies = function() {
		// old cookie names
		var legacyCookieName = function( suffix ) {
			return 'ext.articleFeedbackv5@11-' + suffix;
		}

		// remove old cookie names
		$.cookie( legacyCookieName( 'activity' ), null, { expires: -1, path: '/' } );
		$.cookie( legacyCookieName( 'last-filter' ), null, { expires: -1, path: '/' } );
		$.cookie( legacyCookieName( 'submission_timestamps' ), null, { expires: -1, path: '/' } );
		$.cookie( legacyCookieName( 'feedback-ids' ), null, { expires: -1, path: '/' } );
	};

	// }}}
	// {{{ canSetStatus

	/**
	 * Check if the current user can set a certain status (enable/disable) for the current page
	 *
	 * @param bool enable true to check if can be enabled, false to check disabled
	 */
	$.aftUtils.canSetStatus = function( enable ) {
		var permissionLevel = $.aftUtils.article().permissionLevel || $.aftUtils.article().defaultPermissionLevel;

		// check AFT status for readers
		var enabled = ( permissionLevel === 'aft-reader' );

		/*
		 * If status was specifically set (= not default), "disabled" only needs
		 * aft-editor permissions, not the default aft-noone (which is to make
		 * sure that AFTv5 stays completely hidden for all user types unless
		 * consciously activated)
		 */
		if ( $.aftUtils.article().permissionLevel === false && !enabled ) {
			permissionLevel = 'aft-editor';
		}

		// check user has sufficient permissions to enable/disable AFTv5
		var userPermissions = mw.config.get( 'wgArticleFeedbackv5Permissions' );
		if ( ! (permissionLevel in userPermissions ) || !userPermissions[permissionLevel] ) {
			return false;
		}

		// check if desired status != current status
		return enable != enabled;
	};

	// }}}
	// {{{ setStatus

	/**
	 * Enable/disable feedback on a certain page
	 *
	 * @param int pageId the page id
	 * @param bool enable true to enable, false to disable
	 * @param function callback function to execute after setting status
	 */
	$.aftUtils.setStatus = function( pageId, enable, callback ) {
		var result = [];
		result['result'] = 'Error';
		result['reason'] = 'articlefeedbackv5-error-unknown';

		$.ajax( {
			'url': mw.util.wikiScript( 'api' ),
			'type': 'POST',
			'dataType': 'json',
			'data': {
				'pageid': pageId,
				'enable': parseInt( enable ),
				'format': 'json',
				'action': 'articlefeedbackv5-set-status'
			},
			'success': function ( data ) {
				if ( 'articlefeedbackv5-set-status' in data ) {
					result = data['articlefeedbackv5-set-status'];
				}

				// invoke callback function
				if ( typeof callback == 'function' ) {
					callback( result );
				}
			},
			'error': function ( data ) {
				// invoke callback function
				if ( typeof callback == 'function' ) {
					callback( result );
				}
			}
		});
	};

	// }}}

// }}}

// }}}

} )( jQuery );
