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
		'jquery.articleFeedbackv5.verify' => array(
			'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.verify.js',
			'dependencies' => array(
				'mediawiki.util',
				'mediawiki.user',
			),
		),
		'ext.articleFeedbackv5.startup' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.startup.js',
			'dependencies' => array(
				'mediawiki.util',
				'mediawiki.user',
				'jquery.articleFeedbackv5.verify',
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
				'articlefeedbackv5-toolbox-view',
				'articlefeedbackv5-toolbox-add',
				'articlefeedbackv5-bucket5-toolbox-linktext',
			),
			'dependencies' => array(
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
				'jquery.articleFeedbackv5.verify',
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
				'jquery.articleFeedbackv5.verify',
				'jquery.articleFeedbackv5.track',
			),
		),
		'ext.articleFeedbackv5.watchlist' => array(
			'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.watchlist.js',
			'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.watchlist.css',
			'messages' => array(
				'articlefeedbackv5-watchlist-view-feedback',
			),
			'dependencies' => array(
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
				'articlefeedbackv5-error-throttled',
				'articlefeedbackv5-cta-confirmation-message',
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
				'articlefeedbackv5-cta6-confirmation-title',
				'articlefeedbackv5-cta6-confirmation-call',
				'articlefeedbackv5-cta6-button-text',
				'articlefeedbackv5-cta6-button-link',
				'articlefeedbackv5-overlay-close',
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
				'articlefeedbackv5-help-form-linkurl',
				'articlefeedbackv5-help-form-linkurl-editors',
				'articlefeedbackv5-help-form-linkurl-monitors',
				'articlefeedbackv5-help-form-linkurl-oversighters',
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
				'jquery.ui.button',
				'jquery.cookie',
				'jquery.placeholder',
				'mediawiki.jqueryMsg',
				'jquery.articleFeedbackv5.track',
				'jquery.effects.highlight',
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

				'articlefeedbackv5-help-special-linkurl',
				'articlefeedbackv5-help-special-linkurl-editors',
				'articlefeedbackv5-help-special-linkurl-monitors',
				'articlefeedbackv5-help-special-linkurl-oversighters',

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

				'articlefeedbackv5-beta-label',
			),
			'dependencies' => array(
				'mediawiki.util',
				'jquery.tipsy',
				'jquery.localize',
				'jquery.articleFeedbackv5.track',
				'jquery.json',
				'jquery.ui.button',
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
			'aft_article_feedback',
			dirname( __FILE__ ) . '/sql/ArticleFeedbackv5.sql',
			true
		) );

		$updater->addExtensionUpdate( array(
			'addTable',
			'aft_article_answer_text',
			dirname( __FILE__ ) . '/sql/offload_large_feedback.sql',
			true
		) );

		$updater->addExtensionIndex(
			'aft_article_feedback',
			'af_user_id_user_ip_created',
			dirname( __FILE__ ) . '/sql/index_user_data.sql'
		);

		return true;
	}

	/**
	 * BeforePageDisplay hook - this hook will determine if and what javascript will be loaded
	 *
	 * @param $out OutputPage
	 * @return bool
	 */
	public static function beforePageDisplay( OutputPage &$out, Skin &$skin ) {
		global $wgArticleFeedbackv5Namespaces;
		$title = $out->getTitle();
		$action = Action::getActionName( $out->getContext() );
		$user = $out->getUser();
		$request = $out->getRequest();

		// normal page where form can be displayed
		if ( in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces ) ) {
			// check if we actually fetched article content & no error page
			if ( $out->getRevisionTimestamp() != null ) {
				// load module
				$out->addJsConfigVars( 'aftv5Article', self::getPageInformation( $title ) );
				$out->addModules( 'ext.articleFeedbackv5.startup' );
			}

		// talk page
		} elseif ( in_array( $title->getSubjectPage()->getNameSpace(), $wgArticleFeedbackv5Namespaces ) ) {
			// load module
			$out->addJsConfigVars( 'aftv5Article', self::getPageInformation( $title->getSubjectPage() ) );
			$out->addModules( 'ext.articleFeedbackv5.talk' );

		// special page
		} elseif ( $title->getNamespace() == NS_SPECIAL) {
			// central feedback page, article feedback page, permalink page & watchlist feedback page
			if ( $out->getTitle()->isSpecial( 'ArticleFeedbackv5' ) ||  $out->getTitle()->isSpecial( 'ArticleFeedbackv5Watchlist' ) ) {
				// fetch the title of the article this special page is related to
				list( /* special */, $mainTitle) = SpecialPageFactory::resolveAlias( $out->getTitle()->getDBkey() );

				// Permalinks: drop the feedback ID
				$mainTitle = preg_replace( '/(\/[0-9]+)$/', '', $mainTitle );
				$mainTitle = Title::newFromDBkey( $mainTitle );

				// Central feedback page
				if ( $mainTitle === null ) {
					$article = array(
						'id' => 0,
						'title' => '',
						'namespace' => '-1',
						'categories' => array(),
						'permissionLevel' => ''
					);

				// Article feedback page
				} else {
					$article = self::getPageInformation( $mainTitle );
				}

				// load module
				$out->addJsConfigVars( 'aftv5Article', $article );
				$out->addModules( 'ext.articleFeedbackv5.dashboard' );
			}

			// watchlist page
			elseif ( $out->getTitle()->isSpecial( 'Watchlist' ) ) {
				if ( $user->getId() ) {
					// check if there is feedback on the user's watchlist
					$fetch = new ArticleFeedbackv5Fetch();
					$fetch->setUserId( $user->getId() );
					$fetch->setLimit( 1 );
					$fetched = $fetch->run();
					if ( count( $fetched->records ) > 0 ) {
						$out->addModules( 'ext.articleFeedbackv5.watchlist' );
					}
				}
			}
		}

		return true;
	}

	/**
	 * This will fetch some page information: the actual check if AFT can be loaded
	 * will be done JS-side (because PHP output may be cached and thus not completely
	 * up-to-date)
	 * However, not all checks can be performed on JS-side - well, they can only be
	 * performed on the article page, not on the talk page & special page. Since these
	 * pages don't have the appropriate information available for Javascript, this
	 * method will build the relevant info.
	 *
	 * @param  Title $title the article
	 * @return array the article's info, to be exposed to JS
	 */
	public static function getPageInformation( Title $title ) {
		$article = array(
			'id' => $title->getArticleID(),
			'title' => $title->getFullText(),
			'namespace' => $title->getNamespace(),
			'categories' => array(),
			'permissionLevel' => ArticleFeedbackv5Permissions::getRestriction( $title->getArticleID() )->pr_level
		);

		foreach ( $title->getParentCategories() as $category => $page ) {
			// get category title without prefix
			$category = Title::newFromDBkey( $category );
			$article['categories'][] = str_replace( '_', ' ', $category->getDBkey() );
		}

		return $article;
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
			$wgArticleFeedbackv5WatchlistLink,
			$wgArticleFeedbackv5DefaultSorts,
			$wgArticleFeedbackv5LotteryOdds;
		$vars['wgArticleFeedbackv5SMaxage'] = $wgArticleFeedbackv5SMaxage;
		$vars['wgArticleFeedbackv5Categories'] = $wgArticleFeedbackv5Categories;
		$vars['wgArticleFeedbackv5BlacklistCategories'] = $wgArticleFeedbackv5BlacklistCategories;
		$vars['wgArticleFeedbackv5Debug'] = $wgArticleFeedbackv5Debug;
		$vars['wgArticleFeedbackv5Bucket2TagNames'] = $wgArticleFeedbackv5Bucket2TagNames;
		$vars['wgArticleFeedbackv5Bucket5RatingCategories'] = $wgArticleFeedbackv5Bucket5RatingCategories;
		$vars['wgArticleFeedbackv5Tracking'] = $wgArticleFeedbackv5Tracking;
		$vars['wgArticleFeedbackv5Options'] = $wgArticleFeedbackv5Options;
		$vars['wgArticleFeedbackv5LinkBuckets'] = $wgArticleFeedbackv5LinkBuckets;
		$vars['wgArticleFeedbackv5Namespaces'] = $wgArticleFeedbackv5Namespaces;
		$vars['wgArticleFeedbackv5LearnToEdit'] = $wgArticleFeedbackv5LearnToEdit;
		$vars['wgArticleFeedbackv5WhatsThisPage'] = wfMessage( 'articlefeedbackv5-bucket5-form-panel-explanation-link' )->inContentLanguage()->text();
		$vars['wgArticleFeedbackv5SurveyUrls'] = $wgArticleFeedbackv5SurveyUrls;
		$vars['wgArticleFeedbackv5InitialFeedbackPostCountToDisplay'] = $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		$vars['wgArticleFeedbackv5ThrottleThresholdPostsPerHour'] = $wgArticleFeedbackv5ThrottleThresholdPostsPerHour;
		$vars['wgArticleFeedbackv5SpecialUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getLinkUrl();
		$vars['wgArticleFeedbackv5SpecialWatchlistUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5Watchlist' )->getLinkUrl();
		$vars['wgArticleFeedbackv5TalkPageLink'] = $wgArticleFeedbackv5TalkPageLink;
		$vars['wgArticleFeedbackv5WatchlistLink'] = $wgArticleFeedbackv5WatchlistLink;
		$vars['wgArticleFeedbackv5DefaultSorts'] = $wgArticleFeedbackv5DefaultSorts;
		$vars['wgArticleFeedbackv5LotteryOdds'] = $wgArticleFeedbackv5LotteryOdds;

		// make sure that these keys are being encoded to an object rather than to an array
		$wgArticleFeedbackv5DisplayBuckets['buckets'] = (object) $wgArticleFeedbackv5DisplayBuckets['buckets'];
		$wgArticleFeedbackv5CTABuckets['buckets'] = (object) $wgArticleFeedbackv5CTABuckets['buckets'];
		$vars['wgArticleFeedbackv5DisplayBuckets'] = $wgArticleFeedbackv5DisplayBuckets;
		$vars['wgArticleFeedbackv5CTABuckets'] = (object) $wgArticleFeedbackv5CTABuckets;

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

		// expose AFT permissions for this user to JS
		$vars['wgArticleFeedbackv5Permissions'] = array();
		foreach ( ArticleFeedbackv5Permissions::$permissions as $permission ) {
			$vars['wgArticleFeedbackv5Permissions'][$permission] = $wgUser->isAllowed( $permission );
		}

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
	 * @param $classes the classes to add to the surrounding <li>
	 * @return bool
	 */
	public static function contributionsLineEnding( $page, &$ret, $row, &$classes ) {
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
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5', $pageTitle->getPrefixedDBkey() );
		$feedbackCentralPageTitle = Title::makeTitle( NS_SPECIAL, $centralPageName, "$row->af_id" );

		// date
		$date = $lang->userTimeAndDate( $row->af_created, $user );
		$d = Linker::link(
			$feedbackTitle,
			htmlspecialchars( $date )
		);
		if ( $row->af_is_hidden > 0 || $row->af_oversight_count > 0 || $row->af_is_deleted > 0 ) {
			$d = '<span class="history-deleted">' . $d . '</span>';
		}

		// chardiff
		$chardiff = ' . . ' . ChangesList::showCharacterDifference( 0, strlen( $row->af_comment ) ) . ' . . ';

		// article feedback is given on
		$article = $lang->getDirMark() . wfMessage( 'articlefeedbackv5-contribs-feedback', $feedbackCentralPageTitle->getFullText(), $pageTitle->getPrefixedText() )->parse();

		// show user names for /newbies as there may be different users.
		$userlink = '';
		if ( $page->contribs == 'newbie' ) {
			$username = User::whoIs( $row->af_user_id );
			if ( $username !== false ) {
				$userlink = ' . . ' . Linker::userLink( $row->af_user_id, $username );
				$userlink .= ' ' . wfMessage( 'parentheses' )->rawParams(
					Linker::userTalkLink( $row->af_user_id, $username ) )->escaped() . ' ';
			}
		}

		// feedback (truncated)
		$feedback = '';
		if ( $row->af_comment != '' ) {
			if ( $row->af_is_hidden > 0 || $row->af_oversight_count > 0 || $row->af_is_deleted > 0 ) {
				// (probably) abusive comment that has been hidden/oversight-requested/oversighted
				$feedback = Linker::commentBlock( wfMessage( 'articlefeedbackv5-contribs-hidden-feedback' )->escaped() );
			} else {
				$feedback = Linker::commentBlock( $lang->truncate( $row->af_comment, 250 ) );
			}
		}

		// status (actions taken)
		$status = '';
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
		if ( $row->af_is_hidden > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-hidden' )->escaped();
		}
		if ( $row->af_oversight_count > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-oversight-requested' )->escaped();
		}
		if ( $row->af_is_deleted > 0 ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-deleted' )->escaped();
		}
		if ( !empty( $actions ) ) {
			$status = ' . . ' . wfMessage( 'articlefeedbackv5-contribs-status', implode( ', ', $actions) )->escaped();
		}

		$ret = "{$d} {$chardiff} {$article} {$userlink} {$feedback} {$status}\n";
		$classes[] = 'mw-aft-contribution';

		return true;
	}

	/**
	 * Adds a user's AFT-contributions to the My Contributions special page
	 *
	 * @param $data array an array of results of all contribs queries, to be merged to form all contributions data
	 * @param $pager ContribsPager object hooked into
	 * @param $offset String: index offset, inclusive
	 * @param $limit Integer: exact query limit
	 * @param $descending Boolean: query direction, false for ascending, true for descending
	 * @return bool
	 */
	public static function contributionsData( &$data, $pager, $offset, $limit, $descending ) {
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

			$tables[] = 'aft_article_feedback';
			$tables['rating'] = 'aft_article_answer';
			$tables['comment'] = 'aft_article_answer';

			$fields[] = 'af_id';
			$fields[] = 'af_page_id';
			$fields[] = 'af_created';
			$fields[] = 'af_user_id';
			$fields[] = 'af_user_ip';
			$fields[] = 'af_net_helpfulness';
			$fields[] = 'af_abuse_count';
			$fields[] = 'af_is_featured';
			$fields[] = 'af_is_resolved';
			$fields[] = 'af_is_hidden';
			$fields[] = 'af_oversight_count';
			$fields[] = 'af_is_deleted';
			$fields[] = 'rating.aa_response_boolean AS af_yes_no';
			$fields[] = 'comment.aa_response_text AS af_comment';
			$fields[] = 'af_created AS ' . $pager->getIndexField(); // used for navbar

			if ( $pager->contribs != 'newbie' ) {
				$uid = User::idFromName( $pager->target );
				if ( $uid ) {
					$conds['af_user_id'] = $uid;
					$conds['af_user_ip'] = null;
				} else {
					$conds['af_user_id'] = 0;
					$conds['af_user_ip'] = $pager->target;
				}
			} else {
				// fetch max user id from cache (if present)
				global $wgMemc;
				$key = wfMemcKey( 'articlefeedbackv5', 'maxUserId' );
				$max = $wgMemc->get( $key );
				if ( $max === false ) {
					// max user id not present in cache; fetch from db & save to cache for 1h
					$max = (int) $pager->getDatabase()->selectField( 'user', 'max(user_id)', '', __METHOD__ );
					$wgMemc->set( $key, $max, 60 * 60 );
				}

				// newbie = last 1% of users, without usergroup
				$tables[] = 'user_groups';
				$conds[] = 'af_user_id >' . (int)( $max - $max / 100 );
				$conds[] = 'ug_group IS NULL';

				$join_conds['user_groups'] = array(
					'LEFT JOIN',
					array(
						'ug_user = af_user_id',
						'ug_group' => 'bot',
					)
				);
			}
			if ( $offset ) {
				$operator = $descending ? '>' : '<';
				$conds[] = "af_created $operator " . $pager->getDatabase()->addQuotes( $offset );
			}

			$fname = __METHOD__;

			$order = $descending ? 'ASC' : 'DESC'; // something's wrong with $descending - see logic applied in includes/Pager.php
			$options['ORDER BY'] = array( $pager->getIndexField() . " $order" );
			$options['LIMIT'] = $limit;

			$join_conds['rating'] = array(
				'LEFT JOIN',
				array(
					'rating.aa_feedback_id = af_id',
					'rating.aa_field_id' => $ratingFields
				)
			);
			$join_conds['comment'] = array(
				'LEFT JOIN',
				array(
					'comment.aa_feedback_id = af_id',
					'comment.aa_field_id' => $commentFields,
				)
			);

			$data[] = $pager->getDatabase()->select( $tables, $fields, $conds, $fname, $options, $join_conds );
		}

		return true;
	}

	/**
	 * Add an AFT entry to article's protection levels
	 *
	 * Basically, all this code will do the same as adding a value to $wgRestrictionTypes
	 * However, that would use the same permission types as the other entries, whereas the
	 * AFT permission levels should be different.
	 *
	 * Parts of code are heavily "inspired" by ProtectionForm.
	 *
	 * @param Page $article
	 * @param $output
	 * @return bool
	 */
	public static function onProtectionForm( Page $article, &$output ) {
		global $wgLang;

		$articleId = $article->getId();

		// on a per-page basis, AFT can only be restricted from these levels
		$levels = array(
			'aft-reader' => 'articlefeedbackv5-protection-permission-reader',
			'aft-member' => 'articlefeedbackv5-protection-permission-member',
			'aft-editor' => 'articlefeedbackv5-protection-permission-editor',
			'aft-administrator' => 'articlefeedbackv5-protection-permission-administrator'
		);

		// build permissions dropdown
		$existingPermissions = ArticleFeedbackv5Permissions::getRestriction( $articleId )->pr_level;
		$id = 'articlefeedbackv5-protection-level';
		$attribs = array(
			'id' => $id,
			'name' => $id,
			'size' => count( $levels )
		);
		$permissionsDropdown = Xml::openElement( 'select', $attribs );
		foreach( $levels as $key => $label ) {
			// possible labels: articlefeedbackv5-protection-permission-(all|reader|editor)
			$permissionsDropdown .= Xml::option( wfMessage( $label )->escaped(), $key, $key == $existingPermissions );
		}
		$permissionsDropdown .= Xml::closeElement( 'select' );

		$scExpiryOptions = wfMessage( 'protect-expiry-options' )->inContentLanguage()->text();
		$showProtectOptions = ( $scExpiryOptions !== '-' );

		list(
			$mExistingExpiry,
			$mExpiry,
			$mExpirySelection
			) = ArticleFeedbackv5Permissions::getExpiry( $articleId );

		if( $showProtectOptions ) {
			$expiryFormOptions = '';

			// add option to re-use existing expiry
			if ( $mExistingExpiry && $mExistingExpiry != 'infinity' ) {
				$timestamp = $wgLang->timeanddate( $mExistingExpiry, true );
				$d = $wgLang->date( $mExistingExpiry, true );
				$t = $wgLang->time( $mExistingExpiry, true );
				$expiryFormOptions .=
					Xml::option(
						wfMessage( 'protect-existing-expiry', $timestamp, $d, $t )->escaped(),
						'existing',
						$mExpirySelection == 'existing'
					);
			}

			// add regular expiry options
			$expiryFormOptions .= Xml::option( wfMessage( 'protect-othertime-op' )->escaped(), 'othertime' );
			foreach( explode( ',', $scExpiryOptions ) as $option ) {
				if ( strpos( $option, ':' ) === false ) {
					$show = $value = $option;
				} else {
					list( $show, $value ) = explode( ':', $option );
				}

				$expiryFormOptions .= Xml::option(
					htmlspecialchars( $show ),
					htmlspecialchars( $value ),
					$mExpirySelection == $value
				);
			}

			// build expiry dropdown
			$protectExpiry = Xml::tags( 'select',
				array(
					'id' => 'articlefeedbackv5-protection-expiration-selection',
					'name' => 'articlefeedbackv5-protection-expiration-selection',
					// when selecting anything other than "othertime", clear the input field for other time
					'onchange' => 'javascript:if ( $( this ).val() != "othertime" ) $( "#articlefeedbackv5-protection-expiration" ).val( "" );',
				),
				$expiryFormOptions );
			$mProtectExpiry = Xml::label( wfMessage( 'protectexpiry' )->escaped(), 'mwProtectExpirySelection-aft' );
		}

		// build custom expiry field
		$attribs = array(
			'id' => 'articlefeedbackv5-protection-expiration',
			// when entering an other time, make sure "othertime" is selected in the dropdown
			'onkeyup' => 'javascript:if ( $( this ).val() ) $( "#articlefeedbackv5-protection-expiration-selection" ).val( "othertime" );',
			'onchange' => 'javascript:if ( $( this ).val() ) $( "#articlefeedbackv5-protection-expiration-selection" ).val( "othertime" );'
		);

		$protectOther = Xml::input( 'articlefeedbackv5-protection-expiration', 50, $mExpiry, $attribs );
		$mProtectOther = Xml::label( wfMessage( 'protect-othertime' )->escaped(), "mwProtect-aft-expires" );

		// build output
		$output .= "
				<tr>
					<td>".
			Xml::openElement( 'fieldset' ) .
			Xml::element( 'legend', null, wfMessage( 'articlefeedbackv5-protection-level' )->text() ) .
			Xml::openElement( 'table', array( 'id' => 'mw-protect-table-aft' ) ) . "
								<tr>
									<td>$permissionsDropdown</td>
								</tr>
								<tr>
									<td>";

		if( $showProtectOptions ) {
			$output .= "				<table>
											<tr>
												<td class='mw-label'>$mProtectExpiry</td>
												<td class='mw-input'>$protectExpiry</td>
											</tr>
										</table>";
		}

		$output .= "					<table>
											<tr>
												<td class='mw-label'>$mProtectOther</td>
												<td class='mw-input'>$protectOther</td>
											</tr>
										</table>
									</td>
								</tr>" .
			Xml::closeElement( 'table' ) .
			Xml::closeElement( 'fieldset' ) . "
					</td>
				</tr>";

		return true;
	}

	/**
	 * Write AFT's article's protection levels to DB
	 *
	 * Parts of code are heavily "inspired" by ProtectionForm.
	 *
	 * @param Page $article
	 * @param string $errorMsg
	 * @return bool
	 */
	public static function onProtectionSave( Page $article, &$errorMsg ) {
		global $wgRequest;

		$requestPermission = $wgRequest->getVal( 'articlefeedbackv5-protection-level' );
		$requestExpiry = $wgRequest->getText( 'articlefeedbackv5-protection-expiration' );
		$requestExpirySelection = $wgRequest->getVal( 'articlefeedbackv5-protection-expiration-selection' );

		// fetch permissions set to edit page ans make sure that AFT permissions are no tighter than these
		$editPermission = $article->getTitle()->getRestrictions( 'edit' );
		if ( !$editPermission ) {
			$editPermission[] = '*';
		}
		$availablePermissions = User::getGroupPermissions( $editPermission );
		if ( !in_array( $requestPermission, $availablePermissions ) ) {
			$errorMsg .= wfMessage( 'articlefeedbackv5-protection-level-error' )->escaped();
			return false;
		}

		if ( $requestExpirySelection == 'existing' ) {
			$expirationTime = ArticleFeedbackv5Permissions::getRestriction( $article->getId() )->pr_expiry;
		} else {
			if ( $requestExpirySelection == 'othertime' ) {
				$value = $requestExpiry;
			} else {
				$value = $requestExpirySelection;
			}

			if ( $value == 'infinite' || $value == 'indefinite' || $value == 'infinity' ) {
				$expirationTime = wfGetDB( DB_SLAVE )->getInfinity();
			} else {
				$unix = strtotime( $value );

				if ( !$unix || $unix === -1 ) {
					$errorMsg .= wfMessage( 'protect_expiry_invalid' )->escaped();
					return false;
				} else {
					// @todo FIXME: Non-qualified absolute times are not in users specified timezone
					// and there isn't notice about it in the ui
					$expirationTime = wfTimestamp( TS_MW, $unix );
				}
			}
		}

		$success = ArticleFeedbackv5Permissions::setRestriction(
			$article->getId(),
			$requestPermission,
			$expirationTime
		);

		return $success;
	}
}
