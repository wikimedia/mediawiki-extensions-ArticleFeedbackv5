<?php
/**
 * Hooks for ArticleFeedback
 *
 * @file
 * @ingroup Extensions
 */

class ArticleFeedbackv5Hooks {

	/**
	 * Resource loader modules
	 *
	 * @var array
	 */
	protected static $modules = array(
		'ext.articleFeedbackv5.startup' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.startup.js',
			'dependencies' => array(
				'mediawiki.util',
				'mediawiki.user',
			),
		),
		'ext.articleFeedbackv5' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.js',
			'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.css',
			'messages' => array(
				'articlefeedbackv5-sitesub-linktext',
				'articlefeedbackv5-titlebar-linktext',
				'articlefeedbackv5-fixedtab-linktext',
				'articlefeedbackv5-bottomrighttab-linktext',
				'articlefeedbackv5-section-linktext',
				'articlefeedbackv5-toolbox-linktext',
				'articlefeedbackv5-bucket5-toolbox-linktext',
			),
			'dependencies' => array(
				'jquery.ui.dialog',
				'jquery.ui.button',
				'jquery.articleFeedbackv5',
				'jquery.cookie',
				'ext.articleFeedbackv5.ratingi18n',
				'jquery.articleFeedbackv5.track',
			),
		),
		'ext.articleFeedbackv5.ie' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.ie.js',
			'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.ie.css'
		),
		'ext.articleFeedbackv5.ratingi18n' => array(
			'messages' => null, // Filled in by the resourceLoaderRegisterModules() hook function later
		),
		'ext.articleFeedbackv5.dashboard' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.dashboard.js',
			'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.dashboard.css',
			'messages' => array(
				'articlefeedbackv5-unsupported-message',
				'articlefeedbackv5-page-disabled',
			),
			'dependencies' => array(
				'mediawiki.util',
				'mediawiki.user',
				'jquery.articleFeedbackv5.special',
			),
		),
		'jquery.articleFeedbackv5.track' => array(
			'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.track.js',
			'dependencies' => array(
				'mediawiki.util',
				'mediawiki.user',
				'jquery.clickTracking',
			),
		),
		'ext.articleFeedbackv5.talk' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.talk.js',
			'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.talk.css',
			'messages' => array(
				'articlefeedbackv5-talk-view-feedback',
			),
			'dependencies' => array(
				'mediawiki.util',
				'jquery.articleFeedbackv5.track',
			),
		),
		'jquery.articleFeedbackv5' => array(
			'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.js',
			'styles' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.css',
			'messages' => array(
				'articlefeedbackv5-error-email',
				'articlefeedbackv5-error-validation',
				'articlefeedbackv5-error-nofeedback',
				'articlefeedbackv5-error-unknown',
				'articlefeedbackv5-error-submit',
				'articlefeedbackv5-cta-thanks',
				'articlefeedbackv5-error-abuse',
				'articlefeedbackv5-error-abuse-link',
				'articlefeedbackv5-error-abuse-linktext',
				'articlefeedbackv5-error-throttled',
				'articlefeedbackv5-cta-confirmation-followup',
				'articlefeedbackv5-cta1-confirmation-title',
				'articlefeedbackv5-cta1-confirmation-call',
				'articlefeedbackv5-cta1-learn-how',
				'articlefeedbackv5-cta1-learn-how-url',
				'articlefeedbackv5-cta1-edit-linktext',
				'articlefeedbackv5-cta2-confirmation-title',
				'articlefeedbackv5-cta2-confirmation-call',
				'articlefeedbackv5-cta2-button-text',
				'articlefeedbackv5-cta3-confirmation-title',
				'articlefeedbackv5-cta3-confirmation-call',
				'articlefeedbackv5-cta3-button-text',
				'articlefeedbackv5-cta4-confirmation-title',
				'articlefeedbackv5-cta4-confirmation-call-line1',
				'articlefeedbackv5-cta4-confirmation-call-line2',
				'articlefeedbackv5-cta4-button-text-signup',
				'articlefeedbackv5-cta4-button-text-login',
				'articlefeedbackv5-cta4-button-text-later',
				'articlefeedbackv5-cta4-button-text-or',
				'articlefeedbackv5-cta5-confirmation-title',
				'articlefeedbackv5-cta5-confirmation-call',
				'articlefeedbackv5-cta5-button-text',
				'articlefeedbackv5-cta5-confirmation-followup',
				'articlefeedbackv5-cta5-confirmation-followup-linktext',
				'articlefeedbackv5-bucket1-title',
				'articlefeedbackv5-bucket1-question-toggle',
				'articlefeedbackv5-bucket1-toggle-found-yes',
				'articlefeedbackv5-bucket1-toggle-found-yes-full',
				'articlefeedbackv5-bucket1-toggle-found-no',
				'articlefeedbackv5-bucket1-toggle-found-no-full',
				'articlefeedbackv5-bucket1-question-placeholder-yes',
				'articlefeedbackv5-bucket1-question-placeholder-no',
				'articlefeedbackv5-bucket1-form-pending',
				'articlefeedbackv5-bucket1-form-success',
				'articlefeedbackv5-bucket1-form-submit',
				'articlefeedbackv5-bucket2-title',
				'articlefeedbackv5-bucket2-form-submit',
				'articlefeedbackv5-bucket3-title',
				'articlefeedbackv5-bucket3-rating-question',
				'articlefeedbackv5-bucket3-clear-rating',
				'articlefeedbackv5-bucket3-rating-tooltip-1',
				'articlefeedbackv5-bucket3-rating-tooltip-2',
				'articlefeedbackv5-bucket3-rating-tooltip-3',
				'articlefeedbackv5-bucket3-rating-tooltip-4',
				'articlefeedbackv5-bucket3-rating-tooltip-5',
				'articlefeedbackv5-bucket3-comment-default',
				'articlefeedbackv5-bucket3-form-submit',
				'articlefeedbackv5-bucket4-title',
				'articlefeedbackv5-bucket4-subhead',
				'articlefeedbackv5-bucket4-teaser-line1',
				'articlefeedbackv5-bucket4-teaser-line2',
				'articlefeedbackv5-bucket4-learn-to-edit',
				'articlefeedbackv5-bucket4-form-submit',
				'articlefeedbackv5-bucket4-help-tooltip-info',
				'articlefeedbackv5-bucket4-noedit-title',
				'articlefeedbackv5-bucket4-noedit-teaser-line1',
				'articlefeedbackv5-bucket4-noedit-teaser-line2',
				'articlefeedbackv5-bucket4-noedit-form-submit',
				'articlefeedbackv5-bucket5-form-switch-label',
				'articlefeedbackv5-bucket5-form-panel-title',
				'articlefeedbackv5-bucket5-form-panel-explanation',
				'articlefeedbackv5-bucket5-form-panel-clear',
				'articlefeedbackv5-bucket5-form-panel-expertise',
				'articlefeedbackv5-bucket5-form-panel-expertise-studies',
				'articlefeedbackv5-bucket5-form-panel-expertise-profession',
				'articlefeedbackv5-bucket5-form-panel-expertise-hobby',
				'articlefeedbackv5-bucket5-form-panel-expertise-other',
				'articlefeedbackv5-bucket5-form-panel-helpimprove',
				'articlefeedbackv5-bucket5-form-panel-helpimprove-note',
				'articlefeedbackv5-bucket5-form-panel-helpimprove-email-placeholder',
				'articlefeedbackv5-bucket5-form-panel-helpimprove-privacy',
				'articlefeedbackv5-bucket5-form-panel-submit',
				'articlefeedbackv5-bucket5-form-panel-pending',
				'articlefeedbackv5-bucket5-form-panel-success',
				'articlefeedbackv5-bucket5-form-panel-expiry-title',
				'articlefeedbackv5-bucket5-form-panel-expiry-message',
				'articlefeedbackv5-bucket5-report-switch-label',
				'articlefeedbackv5-bucket5-report-panel-title',
				'articlefeedbackv5-bucket5-report-panel-description',
				'articlefeedbackv5-bucket5-report-empty',
				'articlefeedbackv5-bucket5-report-ratings',
				'articlefeedbackv5-bucket6-title',
				'articlefeedbackv5-bucket6-question-toggle',
				'articlefeedbackv5-bucket6-toggle-found-yes',
				'articlefeedbackv5-bucket6-toggle-found-yes-full',
				'articlefeedbackv5-bucket6-toggle-found-no',
				'articlefeedbackv5-bucket6-toggle-found-no-full',
				'articlefeedbackv5-bucket6-feedback-countdown',
				'articlefeedbackv5-bucket6-question-instructions-yes',
				'articlefeedbackv5-bucket6-question-placeholder-yes',
				'articlefeedbackv5-bucket6-question-instructions-no',
				'articlefeedbackv5-bucket6-question-placeholder-no',
				'articlefeedbackv5-bucket6-form-pending',
				'articlefeedbackv5-bucket6-form-success',
				'articlefeedbackv5-bucket6-form-submit',
				'articlefeedbackv5-bucket6-backlink-text',
				'articlefeedbackv5-error',
				'articlefeedbackv5-shared-on-feedback',
				'articlefeedbackv5-shared-on-feedback-linktext',
				'articlefeedbackv5-help-tooltip-title',
				'articlefeedbackv5-help-tooltip-info',
				'articlefeedbackv5-help-tooltip-linktext',
				'articlefeedbackv5-help-tooltip-linkurl',
				'articlefeedbackv5-help-tooltip-linkurl-editors',
				'articlefeedbackv5-help-tooltip-linkurl-monitors',
				'articlefeedbackv5-help-tooltip-linkurl-oversighters',
				'articlefeedbackv5-help-transparency-terms',
				'parentheses',
				'articlefeedbackv5-disable-flyover-title',
				'articlefeedbackv5-disable-flyover-help',
				'articlefeedbackv5-disable-flyover-help-emphasis-text',
				'articlefeedbackv5-disable-flyover-help-location',
				'articlefeedbackv5-disable-flyover-help-direction',
				'articlefeedbackv5-disable-flyover-prefbutton',
				'articlefeedbackv5-disable-preference',
				'pipe-separator',
			),
			'dependencies' => array(
				'jquery.appear',
				'jquery.tipsy',
				'jquery.json',
				'jquery.localize',
				'jquery.ui.dialog',
				'jquery.ui.button',
				'jquery.cookie',
				'jquery.placeholder',
				'mediawiki.jqueryMsg',
				'jquery.articleFeedbackv5.track',
			),
		),
		'jquery.articleFeedbackv5.special' => array(
			'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.js',
			'styles'   => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.css',
			'messages' => array(
				'articlefeedbackv5-error-flagging',
				'articlefeedbackv5-invalid-feedback-id',
				'articlefeedbackv5-invalid-feedback-flag',
				'articlefeedbackv5-form-abuse',
				'articlefeedbackv5-form-abuse-count',
				'articlefeedbackv5-abuse-saved',
				'articlefeedbackv5-abuse-saved-tooltip',
				'articlefeedbackv5-form-hide',
				'articlefeedbackv5-form-unhide',
				'articlefeedbackv5-form-delete',
				'articlefeedbackv5-form-undelete',
				'articlefeedbackv5-form-oversight',
				'articlefeedbackv5-form-unoversight',
				'articlefeedbackv5-comment-more',
				'articlefeedbackv5-comment-less',
				'articlefeedbackv5-error-loading-feedback',
				'articlefeedbackv5-loading-tag',
				'articlefeedbackv5-permalink-activity-more',
				'articlefeedbackv5-permalink-activity-fewer',

				'articlefeedbackv5-new-marker',
				'articlefeedbackv5-deleted-marker',
				'articlefeedbackv5-hidden-marker',
				'articlefeedbackv5-featured-marker',
				'articlefeedbackv5-resolved-marker',

				'articlefeedbackv5-help-tooltip-linkurl',
				'articlefeedbackv5-help-tooltip-linkurl-editors',
				'articlefeedbackv5-help-tooltip-linkurl-monitors',
				'articlefeedbackv5-help-tooltip-linkurl-oversighters',

				'articlefeedbackv5-noteflyover-hide-caption',
				'articlefeedbackv5-noteflyover-hide-label',
				'articlefeedbackv5-noteflyover-hide-placeholder',
				'articlefeedbackv5-noteflyover-hide-submit',
				'articlefeedbackv5-noteflyover-hide-help',
				'articlefeedbackv5-noteflyover-hide-help-link',

				'articlefeedbackv5-noteflyover-show-caption',
				'articlefeedbackv5-noteflyover-show-label',
				'articlefeedbackv5-noteflyover-show-placeholder',
				'articlefeedbackv5-noteflyover-show-submit',
				'articlefeedbackv5-noteflyover-show-help',
				'articlefeedbackv5-noteflyover-show-help-link',

				'articlefeedbackv5-noteflyover-requestoversight-caption',
				'articlefeedbackv5-noteflyover-requestoversight-label',
				'articlefeedbackv5-noteflyover-requestoversight-placeholder',
				'articlefeedbackv5-noteflyover-requestoversight-submit',
				'articlefeedbackv5-noteflyover-requestoversight-help',
				'articlefeedbackv5-noteflyover-requestoversight-help-link',

				'articlefeedbackv5-noteflyover-unrequestoversight-caption',
				'articlefeedbackv5-noteflyover-unrequestoversight-label',
				'articlefeedbackv5-noteflyover-unrequestoversight-placeholder',
				'articlefeedbackv5-noteflyover-unrequestoversight-submit',
				'articlefeedbackv5-noteflyover-unrequestoversight-help',
				'articlefeedbackv5-noteflyover-unrequestoversight-help-link',

				'articlefeedbackv5-noteflyover-oversight-caption',
				'articlefeedbackv5-noteflyover-oversight-label',
				'articlefeedbackv5-noteflyover-oversight-placeholder',
				'articlefeedbackv5-noteflyover-oversight-submit',
				'articlefeedbackv5-noteflyover-oversight-help',
				'articlefeedbackv5-noteflyover-oversight-help-link',

				'articlefeedbackv5-noteflyover-unoversight-caption',
				'articlefeedbackv5-noteflyover-unoversight-label',
				'articlefeedbackv5-noteflyover-unoversight-placeholder',
				'articlefeedbackv5-noteflyover-unoversight-submit',
				'articlefeedbackv5-noteflyover-unoversight-help',
				'articlefeedbackv5-noteflyover-unoversight-help-link',

				'articlefeedbackv5-noteflyover-declineoversight-caption',
				'articlefeedbackv5-noteflyover-declineoversight-label',
				'articlefeedbackv5-noteflyover-declineoversight-placeholder',
				'articlefeedbackv5-noteflyover-declineoversight-submit',
				'articlefeedbackv5-noteflyover-declineoversight-help',
				'articlefeedbackv5-noteflyover-declineoversight-help-link',

				'articlefeedbackv5-mask-view-contents',
				'articlefeedbackv5-mask-text-hidden',
				'articlefeedbackv5-mask-text-oversight',
				'articlefeedbackv5-mask-postnumber',

				'articlefeedbackv5-form-feature',
				'articlefeedbackv5-form-unfeature',
				'articlefeedbackv5-noteflyover-feature-caption',
				'articlefeedbackv5-noteflyover-feature-label',
				'articlefeedbackv5-noteflyover-feature-placeholder',
				'articlefeedbackv5-noteflyover-feature-submit',
				'articlefeedbackv5-noteflyover-feature-help',
				'articlefeedbackv5-noteflyover-feature-help-link',
				'articlefeedbackv5-noteflyover-unfeature-caption',
				'articlefeedbackv5-noteflyover-unfeature-label',
				'articlefeedbackv5-noteflyover-unfeature-placeholder',
				'articlefeedbackv5-noteflyover-unfeature-submit',
				'articlefeedbackv5-noteflyover-unfeature-help',
				'articlefeedbackv5-noteflyover-unfeature-help-link',

				'articlefeedbackv5-form-resolve',
				'articlefeedbackv5-form-unresolve',
				'articlefeedbackv5-noteflyover-resolve-caption',
				'articlefeedbackv5-noteflyover-resolve-label',
				'articlefeedbackv5-noteflyover-resolve-placeholder',
				'articlefeedbackv5-noteflyover-resolve-submit',
				'articlefeedbackv5-noteflyover-resolve-help',
				'articlefeedbackv5-noteflyover-resolve-help-link',
				'articlefeedbackv5-noteflyover-unresolve-caption',
				'articlefeedbackv5-noteflyover-unresolve-label',
				'articlefeedbackv5-noteflyover-unresolve-placeholder',
				'articlefeedbackv5-noteflyover-unresolve-submit',
				'articlefeedbackv5-noteflyover-unresolve-help',
				'articlefeedbackv5-noteflyover-unresolve-help-link',

				'articleFeedbackv5-beta-label',
			),
			'dependencies' => array(
				'mediawiki.util',
				'jquery.tipsy',
				'jquery.localize',
				'jquery.articleFeedbackv5.track',
				'jquery.json',
			),
		),
	);

	/* Static Methods */

	/**
	 * LoadExtensionSchemaUpdates hook
	 *
	 * @param $updater DatabaseUpdater
	 *
	 * @return bool
	 */
	public static function loadExtensionSchemaUpdates( $updater = null ) {
		$updater->addExtensionUpdate( array(
			'addTable',
			'article_feedback',
			dirname( __FILE__ ) . '/sql/ArticleFeedbackv5.sql',
			true
		) );

		$updater->addExtensionUpdate( array(
			'addTable',
			'aft_article_answer_text',
			dirname( __FILE__ ) . '/sql/offload_large_feedback.sql',
			true
		) );

		return true;
	}

	/**
	 * BeforePageDisplay hook - this hook will determine if and what javascript will be loaded
	 *
	 * @param $out OutputPage
	 * @return bool
	 */
	public static function beforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$title = $out->getTitle();
		$action = Action::getActionName( $out->getContext() );
		$user = $out->getUser();
		$request = $out->getRequest();

		switch ( $title->getNamespace() ) {
			// normal page
			case NS_MAIN:
				if (
					// view pages
					( $action == 'view' || $action == 'purge' )
					// if user is logged in, showing on action=purge is OK,
					// but if user is logged out, action=purge shows a form instead of the article,
					// so return false in that case.
					&& !( $action == 'purge' && $user->isAnon() )
					// current revision
					&& $request->getVal( 'diff' ) == null
					&& $request->getVal( 'oldid' ) == null
					// not viewing a redirect
					&& $request->getVal( 'redirect' ) != 'no'
					// not viewing the printable version
					&& $request->getVal( 'printable' ) != 'yes'
				) {
					$res = self::allowForPage( $title );
					if ( $res['allow'] ) {
						// load module
						$out->addJsConfigVars( 'aftv5Whitelist', $res['whitelist'] );
						$out->addModules( 'ext.articleFeedbackv5.startup' );
					}
				}
				break;

			// talk page
			case NS_TALK:
				$res = self::allowForPage( $title->getSubjectPage() );
				if ( $res['allow'] ) {
					// load module
					$out->addJsConfigVars( 'aftv5Whitelist', $res['whitelist'] );
					$out->addJsConfigVars( 'aftv5PageId', $title->getSubjectPage()->getArticleID() );
					$out->addModules( 'ext.articleFeedbackv5.talk' );
				}
				break;

			// special page
			case NS_SPECIAL:
				if ( $out->getTitle()->isSpecial( 'ArticleFeedbackv5' ) ) {
					// fetch the title of the article this special page is related to
					list( /* special */, $mainTitle) = SpecialPageFactory::resolveAlias( $out->getTitle()->getDBkey() );

					// Permalinks: drop the feedback ID
					$mainTitle = preg_replace( '/(\/[0-9]+)$/', '', $mainTitle );

					// Central feedback page OR allowed page
					$mainTitle = Title::newFromDBkey( $mainTitle );
					if ( $mainTitle === null ) {
						$res = array( 'allow' => true, 'whitelist' => true );
					} else {
						$res = self::allowForPage( $mainTitle );
					}
					if ( $res['allow'] ) {
						// load module
						$out->addJsConfigVars( 'aftv5Whitelist', $res['whitelist'] );
						if ( $mainTitle !== null ) {
							$out->addJsConfigVars( 'aftv5PageId', $mainTitle->getArticleID() );
						} else {
							$out->addJsConfigVars( 'aftv5PageId', 0 );
						}
						$out->addModules( 'ext.articleFeedbackv5.dashboard' );
					}
				}
				break;

			// other, unknown
			default:
 				return true;
		}

		return true;
	}

	/**
	 * Check if ArticleFeedbackv5 is allowed to show on a certain page, depending on:
	 * - if the user has disabled articlefeedback
	 * - if the article is in the allowed namespaces list
	 * - if the article is an existing page
	 * - if the article has the valid categories
	 *
	 * @param  Title $title the article to test
	 * @return array the results of the tests: keys are allowed, blacklist, and
	 *               whitelist
	 */
	public static function allowForPage( Title $title ) {
		global $wgUser,
			$wgArticleFeedbackv5Namespaces,
			$wgArticleFeedbackv5Categories,
			$wgArticleFeedbackv5BlacklistCategories;

		$result = array( 'allow' => false, 'blacklist' => false, 'whitelist' => false );
		if (
			// not disabled via preferences
			!$wgUser->getOption( 'articlefeedback-disable' )
			// only on pages in namespaces where it is enabled
			&& in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces )
			// existing pages
			&& $title->getArticleId() > 0
		) {
			$result['allow'] = true;

			// loop all categories linked to this page
			foreach ( $title->getParentCategories() as $category => $page ) {
				// get category title without prefix
				$category = Title::newFromDBkey( $category );
				$category = $category->getDBkey();

				// check exclusion - exclusion overrides everything else
				if ( in_array( $category, $wgArticleFeedbackv5BlacklistCategories ) ) {
					$result['blacklist'] = true;
					$result['allow'] = false;
					return $result;
				}

				if ( in_array( $category, $wgArticleFeedbackv5Categories ) ) {
					// one match is enough for include, however we are iterating on the 'current'
					// categories, and others might be blacklisted - so continue iterating
					$result['whitelist'] = true;
				}
			}

		}

		return $result;
	}

	/**
	 * ResourceLoaderRegisterModules hook
	 * @param $resourceLoader ResourceLoader
	 * @return bool
	 */
	public static function resourceLoaderRegisterModules( &$resourceLoader ) {
		global $wgExtensionAssetsPath,
			$wgArticleFeedbackv5Bucket5RatingCategories,
			$wgArticleFeedbackv5Bucket2TagNames;

		$localpath = dirname( __FILE__ ) . '/modules';
		$remotepath = "$wgExtensionAssetsPath/ArticleFeedbackv5/modules";

		foreach ( self::$modules as $name => $resources ) {
			if ( $name == 'jquery.articleFeedbackv5' ) {
				// Bucket 2: labels and comment defaults
				$prefix = 'articlefeedbackv5-bucket2-';
				foreach ( $wgArticleFeedbackv5Bucket2TagNames as $tag ) {
					$resources['messages'][] = $prefix . $tag . '-label';
					$resources['messages'][] = $prefix . $tag . '-comment-default';
				}
				// Bucket 5: labels and tooltips
				$prefix = 'articlefeedbackv5-bucket5-';
				foreach ( $wgArticleFeedbackv5Bucket5RatingCategories as $field ) {
					$resources['messages'][] = $prefix . $field . '-label';
					$resources['messages'][] = $prefix . $field . '-tip';
					$resources['messages'][] = $prefix . $field . '-tooltip-1';
					$resources['messages'][] = $prefix . $field . '-tooltip-2';
					$resources['messages'][] = $prefix . $field . '-tooltip-3';
					$resources['messages'][] = $prefix . $field . '-tooltip-4';
					$resources['messages'][] = $prefix . $field . '-tooltip-5';
				}
			}

			$resourceLoader->register(
				$name, new ResourceLoaderFileModule( $resources, $localpath, $remotepath )
			);
		}
		return true;
	}

	/**
	 * ResourceLoaderGetConfigVars hook
	 * @param $vars array
	 * @return bool
	 */
	public static function resourceLoaderGetConfigVars( &$vars ) {
		global $wgArticleFeedbackv5SMaxage,
			$wgArticleFeedbackv5Categories,
			$wgArticleFeedbackv5BlacklistCategories,
			$wgArticleFeedbackv5Debug,
			$wgArticleFeedbackv5Bucket2TagNames,
			$wgArticleFeedbackv5Bucket5RatingCategories,
			$wgArticleFeedbackv5DisplayBuckets,
			$wgArticleFeedbackv5CTABuckets,
			$wgArticleFeedbackv5Tracking,
			$wgArticleFeedbackv5Options,
			$wgArticleFeedbackv5LinkBuckets,
			$wgArticleFeedbackv5Namespaces,
			$wgArticleFeedbackv5LearnToEdit,
			$wgArticleFeedbackv5SurveyUrls,
			$wgArticleFeedbackv5InitialFeedbackPostCountToDisplay,
			$wgArticleFeedbackv5ThrottleThresholdPostsPerHour,
			$wgArticleFeedbackv5TalkPageLink,
			$wgArticleFeedbackv5DefaultSorts,
			$wgArticleFeedbackLotteryOdds;
		$vars['wgArticleFeedbackv5SMaxage'] = $wgArticleFeedbackv5SMaxage;
		$vars['wgArticleFeedbackv5Categories'] = $wgArticleFeedbackv5Categories;
		$vars['wgArticleFeedbackv5BlacklistCategories'] = $wgArticleFeedbackv5BlacklistCategories;
		$vars['wgArticleFeedbackv5Debug'] = $wgArticleFeedbackv5Debug;
		$vars['wgArticleFeedbackv5Bucket2TagNames'] = $wgArticleFeedbackv5Bucket2TagNames;
		$vars['wgArticleFeedbackv5Bucket5RatingCategories'] = $wgArticleFeedbackv5Bucket5RatingCategories;
		$vars['wgArticleFeedbackv5DisplayBuckets'] = $wgArticleFeedbackv5DisplayBuckets;
		$vars['wgArticleFeedbackv5CTABuckets'] = $wgArticleFeedbackv5CTABuckets;
		$vars['wgArticleFeedbackv5Tracking'] = $wgArticleFeedbackv5Tracking;
		$vars['wgArticleFeedbackv5Options'] = $wgArticleFeedbackv5Options;
		$vars['wgArticleFeedbackv5LinkBuckets'] = $wgArticleFeedbackv5LinkBuckets;
		$vars['wgArticleFeedbackv5Namespaces'] = $wgArticleFeedbackv5Namespaces;
		$vars['wgArticleFeedbackv5LearnToEdit'] = $wgArticleFeedbackv5LearnToEdit;
		$vars['wgArticleFeedbackv5WhatsThisPage'] = wfMsgForContent( 'articlefeedbackv5-bucket5-form-panel-explanation-link' );
		$vars['wgArticleFeedbackv5SurveyUrls'] = $wgArticleFeedbackv5SurveyUrls;
		$vars['wgArticleFeedbackv5InitialFeedbackPostCountToDisplay'] = $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		$vars['wgArticleFeedbackv5ThrottleThresholdPostsPerHour'] = $wgArticleFeedbackv5ThrottleThresholdPostsPerHour;
		$vars['wgArticleFeedbackv5SpecialUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getLinkUrl();
		$vars['wgArticleFeedbackv5TalkPageLink'] = $wgArticleFeedbackv5TalkPageLink;
		$vars['wgArticleFeedbackv5DefaultSorts'] = $wgArticleFeedbackv5DefaultSorts;
		$vars['wgArticleFeedbackv5SpecialUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getLinkUrl();
		$vars['wgArticleFeedbackv5TalkPageLink'] = $wgArticleFeedbackv5TalkPageLink;
		$vars['wgArticleFeedbackLotteryOdds'] = $wgArticleFeedbackLotteryOdds;
		return true;
	}

	/**
	 * MakeGlobalVariablesScript hook - this does pretty much the same as the ResourceLoaderGetConfigVars
	 * hook: it makes these variables accessible through JS. However, these are added on a per-page basis,
	 * on the page itself (also setting us free from potential browser cache issues)
	 * @param $vars array
	 * @return bool
	 */
	public static function makeGlobalVariablesScript( &$vars ) {
		global $wgUser;
		$vars['wgArticleFeedbackv5Permissions'] = array(
			'oversighter' => $wgUser->isAllowed( 'aftv5-delete-feedback' ),
			'moderator' => $wgUser->isAllowed( 'aftv5-hide-feedback' ),
			'editor' => !$wgUser->isAnon()
		);
		return true;
	}

	/**
	 * Add the preference in the user preferences with the GetPreferences hook.
	 * @param $user User
	 * @param $preferences
	 * @return bool
	 */
	public static function getPreferences( $user, &$preferences ) {
		// need to check for existing key, if deployed simultaneously with AFTv4
		if ( !array_key_exists( 'articlefeedback-disable', $preferences ) ) {
			$preferences['articlefeedback-disable'] = array(
				'type' => 'check',
				'section' => 'rendering/advancedrendering',
				'label-message' => 'articlefeedbackv5-disable-preference',
			);
		}
		return true;
	}

	/**
	 * Pushes the tracking fields into the edit page
	 *
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/EditPage::showEditForm:fields
	 * @param $editPage EditPage
	 * @param $output OutputPage
	 * @return bool
	 */
	public static function pushTrackingFieldsToEdit( $editPage, $output ) {
		$request = $output->getRequest();
		$tracking   = $request->getVal( 'articleFeedbackv5_click_tracking' );
		$bucketId   = $request->getVal( 'articleFeedbackv5_bucket_id' );
		$ctaId      = $request->getVal( 'articleFeedbackv5_cta_id' );
		$flinkId    = $request->getVal( 'articleFeedbackv5_f_link_id' );
		$experiment = $request->getVal( 'articleFeedbackv5_experiment' );
		$location   = $request->getVal( 'articleFeedbackv5_location' );
		$token      = $request->getVal( 'articleFeedbackv5_ct_token' );
		$ctEvent    = $request->getVal( 'articleFeedbackv5_ct_event' );

		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_click_tracking', $tracking );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_bucket_id', $bucketId );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_cta_id', $ctaId );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_f_link_id', $flinkId );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_experiment', $experiment );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_location', $location );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_ct_token', $token );
		$editPage->editFormTextAfterContent .= Html::hidden( 'articleFeedbackv5_ct_event', $ctEvent );

		return true;
	}

	/**
	 * Tracks edit attempts
	 *
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/EditPage::attemptSave
	 * @param $editpage EditPage
	 * @return bool
	 */
	public static function trackEditAttempt( $editpage ) {
		self::trackEvent( 'edit_attempt', $editpage->getArticle()->getTitle(), $editpage->getArticle()->getRevIdFetched()); // EditPage::getTitle() doesn't exist in 1.18wmf1
		return true;
	}

	/**
	 * Tracks successful edits
	 *
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/ArticleSaveComplete
	 * @param $article WikiPage
	 * @param $user
	 * @param $text
	 * @param $summary
	 * @param $minoredit
	 * @param $watchthis
	 * @param $sectionanchor
	 * @param $flags
	 * @param $revision
	 * @param $status
	 * @param $baseRevId
	 * @return bool
	 */
	public static function trackEditSuccess( &$article, &$user, $text,
			$summary, $minoredit, $watchthis, $sectionanchor, &$flags,
			$revision, &$status, $baseRevId /*, &$redirect */ ) { // $redirect not passed in 1.18wmf1
		$revID = $revision instanceof Revision ? $revision->getID() : 0;
		self::trackEvent( 'edit_success', $article->getTitle(), $revID );
		return true;
	}

	/**
	 * Internal use: Tracks an event
	 *
	 * @param $event string the event name
	 * @param $context IContextSource
	 * @return
	 */
	private static function trackEvent( $event, $title, $rev_id) {
		global $wgArticleFeedbackv5Tracking;
		$ctas = array( 'none', 'edit', 'learn_more' );

		$request = RequestContext::getMain()->getRequest();

		$tracking = $request->getVal( 'articleFeedbackv5_click_tracking' );
		if ( !$tracking ) {
			return;
		}

		$version    = $wgArticleFeedbackv5Tracking['version'];
		$bucketId   = $request->getVal( 'articleFeedbackv5_bucket_id' );
		$ctaId      = $request->getVal( 'articleFeedbackv5_cta_id' );
		$flinkId    = $request->getVal( 'articleFeedbackv5_f_link_id' );
		$experiment = $request->getVal( 'articleFeedbackv5_experiment' );
		$location   = $request->getVal( 'articleFeedbackv5_location' );
		$token      = $request->getVal( 'articleFeedbackv5_ct_token' );
		$ctEvent    = $request->getVal( 'articleFeedbackv5_ct_event' );

		if ( $ctEvent ) {
			$trackingId = $ctEvent . '-' . $event;
		} else {
			$trackingId = 'ext.articleFeedbackv5@' . $version;
			if ( $experiment ) {
				$trackingId .= '-' . $experiment; // Stage 3 or greater
			} else {
				$trackingId .= '-option' . $bucketId . $flinkId; // Prior to stage 3; handles cached js
			}
			$trackingId .= '-cta_' . ( isset( $ctas[$ctaId] ) ? $ctas[$ctaId] : 'unknown' )
				. '-' . $event
				. '-' . $location;
		}

		$params = new FauxRequest( array(
			'action' => 'clicktracking',
			'eventid' => $trackingId,
			'token' => $token,
			'additional' => $title->getText() . '|' . $rev_id,
			'namespacenumber' => $title->getNamespace()
		) );
		$api = new ApiMain( $params, true );
		$api->execute();
	}

	/**
	 * Intercept contribution entries and format those belonging to AFT
	 *
	 * @param $page SpecialPage object for contributions
	 * @param $ret string the HTML line
	 * @param $row Row the DB row for this line
	 * @return bool
	 */
	public static function contributionsLineEnding( &$page, &$ret, $row ) {
		if ( !isset( $row->af_id ) || $row->af_id === '' ) {
			return true;
		}

		$pageTitle = Title::newFromId( $row->af_page_id );
		if ( $pageTitle === null ) {
			return true;
		}

		$lang = $page->getLanguage();
		$user = $page->getUser();
		$feedbackTitle = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $pageTitle->getPrefixedDBkey() . "/$row->af_id" );

		// date
		$date = $lang->userTimeAndDate( $row->af_created, $user );
		$d = Linker::link(
			$feedbackTitle,
			htmlspecialchars( $date )
		);
		if ( $row->af_is_deleted ) {
			$d = '<span class="history-deleted">' . $d . '</span>';
		}

		// chardiff
		$chardiff = ' . . ' . ChangesList::showCharacterDifference( 0, strlen( $row->af_comment ) ) . ' . . ';

		// feedback
		$feedback = $lang->getDirMark() . wfMessage( 'articlefeedbackv5-contribs-feedback', $feedbackTitle->getPrefixedDBkey(), $pageTitle->getPrefixedText() )->parse();
		if ( $row->af_comment != '' ) {
			$feedback .= Linker::commentBlock( $lang->truncate( $row->af_comment, 250 ) );
		}

		// status (vote)
		if ( $row->af_yes_no === '1' ) {
			$status = wfMessage( 'articlefeedbackv5-contribs-status-positive' )->escaped();
		} elseif ( $row->af_yes_no === '0' ) {
			$status = wfMessage( 'articlefeedbackv5-contribs-status-negative' )->escaped();
		} else {
			$status = wfMessage( 'articlefeedbackv5-contribs-status-neutral' )->escaped();
		}
		$status = ' . . ' . wfMessage( 'articlefeedbackv5-contribs-status', $status )->escaped();

		// status (actions taken)
		$actions = array();
		if ( $row->af_net_helpfulness > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-helpful' )->escaped();
		}
		if ( $row->af_abuse_count > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-flagged' )->escaped();
		}
		if ( $row->af_is_featured > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-featured' )->escaped();
		}
		if ( $row->af_is_resolved > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-resolved' )->escaped();
		}
		if ( $row->af_oversight_count > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-oversight-requested' )->escaped();
		}
		if ( $row->af_is_deleted> 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-deleted' )->escaped();
		}
		if ( !empty( $actions ) ) {
			$status .= ' - ' . implode( ', ', $actions);
		}

		$ret = "{$d} {$chardiff} {$feedback} {$status}";
		$ret = "<li>$ret</li>\n";

		return true;
	}

	/**
	 * Adds a user's AFT-contributions to the My Contributions special page
	 *
	 * @param $data array an array of results of all contribs queries, to be merged to form all contributions data
	 * @param $pager: the ContribsPager object hooked into
	 * @param $offset String: index offset, inclusive
	 * @param $limit Integer: exact query limit
	 * @param $descending Boolean: query direction, false for ascending, true for descending
	 * @return bool
	 */
	public static function contributionsQuery( &$data, $pager, $offset, $limit, $descending ) {
		if ( $pager->namespace === '' ) {
			$ratingFields  = array( -1 );
			$commentFields = array( -1 );
			// This is in memcache so I don't feel that bad re-fetching it.
			// Needed to join in the comment and rating tables, for filtering
			// and sorting, respectively.
			foreach ( ApiArticleFeedbackv5Utils::getFields() as $field ) {
				if ( in_array( $field['afi_bucket_id'], array( 1, 6 ) ) && $field['afi_name'] == 'comment' ) {
					$commentFields[] = (int) $field['afi_id'];
				}
				if ( in_array( $field['afi_bucket_id'], array( 1, 6 ) ) && $field['afi_name'] == 'found' ) {
					$ratingFields[] = (int) $field['afi_id'];
				}
			}

			// build parameters for AFT-data
			$tables = array(
				'aft_article_feedback',
				'rating'  => 'aft_article_answer',
				'comment' => 'aft_article_answer',
			);

			$fields = array(
				'af_id',
				'af_page_id',
				'af_created',
				'af_net_helpfulness',
				'af_abuse_count',
				'af_is_featured',
				'af_is_resolved',
				'af_is_hidden',
				'af_oversight_count',
				'af_is_deleted',
				'rating.aa_response_boolean AS af_yes_no',
				'comment.aa_response_text AS af_comment',
				'af_created AS ' . $pager->getIndexField() // used for navbar
			);

			$conds = array();
			if ( $pager->contribs != 'newbie' ) {
				$uid = User::idFromName( $pager->target );
				if ( $uid ) {
					$conds['af_user_id'] = $uid;
					$conds['af_user_ip'] = null;
				} else {
					$conds['af_user_id'] = 0;
					$conds['af_user_ip'] = $pager->target;
				}
				if ( $offset ) {
					$operator = $descending ? '>' : '<';
					$conds[] = "af_created $operator " . $pager->mDb->addQuotes( $offset );
				}
			}

			$fname = __METHOD__;

			$order = $descending ? 'ASC' : 'DESC'; // something's wrong with $descending - see logic applied in includes/Pager.php
			$options = array(
				'ORDER BY' => array( $pager->getIndexField() . " $order" ),
				'LIMIT' => $limit
			);

			$join_conds = array(
				'rating'  => array(
					'LEFT JOIN',
					'rating.aa_feedback_id = af_id AND rating.aa_field_id IN (' . implode( ',', $ratingFields ) . ')'
				),
				'comment' => array(
					'LEFT JOIN',
					'comment.aa_feedback_id = af_id AND comment.aa_field_id IN (' . implode( ',', $commentFields ) . ')'
				)
			);

			$data[] = $pager->mDb->select( $tables, $fields, $conds, $fname, $options, $join_conds );
		}

		return true;
	}
}
