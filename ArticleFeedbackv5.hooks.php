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
				'jquery.clickTracking',
				'ext.articleFeedbackv5.ratingi18n',
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
			'dependencies' => array(
				'mediawiki.util',
				'mediawiki.user',
				'jquery.articleFeedbackv5.special',
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
				'articlefeedbackv5-cta5-confirmation-title',
				'articlefeedbackv5-cta5-confirmation-call',
				'articlefeedbackv5-cta5-button-text',
				'articlefeedbackv5-bucket1-title',
				'articlefeedbackv5-bucket1-question-toggle',
				'articlefeedbackv5-bucket1-toggle-found-yes',
				'articlefeedbackv5-bucket1-toggle-found-yes-full',
				'articlefeedbackv5-bucket1-toggle-found-no',
				'articlefeedbackv5-bucket1-toggle-found-no-full',
				'articlefeedbackv5-bucket1-question-comment-yes',
				'articlefeedbackv5-bucket1-question-comment-no',
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
				'articlefeedbackv5-transparency-terms',
				'articlefeedbackv5-transparency-terms-linktext',
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
				'jquery.clickTracking',
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
				'articlefeedbackv5-form-abuse-masked',
				'articlefeedbackv5-abuse-saved',
				'articlefeedbackv5-abuse-saved-masked',
				'articlefeedbackv5-form-hide',
				'articlefeedbackv5-form-unhide',
				'articlefeedbackv5-form-delete',
				'articlefeedbackv5-form-undelete',
				'articlefeedbackv5-form-oversight',
				'articlefeedbackv5-form-unoversight',
				'articlefeedbackv5-comment-more',
				'articlefeedbackv5-comment-less',
				'articlefeedbackv5-unsupported-message',
				'articlefeedbackv5-error-loading-feedback',
				'articlefeedbackv5-loading-tag',

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

				'articlefeedbackv5-featured-marker',
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

				'articlefeedbackv5-resolved-marker',
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
			),
			'dependencies' => array(
				'mediawiki.util',
				'jquery.tipsy',
				'jquery.localize',
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
		if ( $updater === null ) {
			global $wgExtNewTables;
			$wgExtNewTables[] = array(
				'article_feedback',
				dirname( __FILE__ ) . '/sql/ArticleFeedbackv5.sql'
			);
		} else {
			$updater->addExtensionUpdate( array(
				'addTable',
				'article_feedback',
				dirname( __FILE__ ) . '/sql/ArticleFeedbackv5.sql',
				true
			) );
		}
		return true;
	}

	/**
	 * BeforePageDisplay hook
	 * @param $out OutputPage
	 * @return bool
	 */
	public static function beforePageDisplay( $out ) {
		global $wgTitle;

		// Load modules
		switch ( $wgTitle->getNamespace() ) {
			case NS_SPECIAL:
				// note: ext.articleFeedbackv5.dashboard is being loaded from SpecialArticleFeedbackv5.php
				break;
			case NS_TALK:
				$out->addModules( 'ext.articleFeedbackv5.talk' );
				break;
			default:
				$out->addModules( 'ext.articleFeedbackv5.startup' );
		}

		return true;
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
			$wgArticleFeedbackv5LotteryOdds,
			$wgArticleFeedbackv5Debug,
			$wgArticleFeedbackv5Bucket2TagNames,
			$wgArticleFeedbackv5Bucket5RatingCategories,
			$wgArticleFeedbackv5DisplayBuckets,
			$wgArticleFeedbackv5Tracking,
			$wgArticleFeedbackv5Options,
			$wgArticleFeedbackv5LinkBuckets,
			$wgArticleFeedbackv5Namespaces,
			$wgArticleFeedbackv5LearnToEdit,
			$wgArticleFeedbackv5SurveyUrls,
			$wgArticleFeedbackv5InitialFeedbackPostCountToDisplay,
			$wgUser,
			$wgArticleFeedbackv5TalkPageLink;
		$vars['wgArticleFeedbackv5SMaxage'] = $wgArticleFeedbackv5SMaxage;
		$vars['wgArticleFeedbackv5Categories'] = $wgArticleFeedbackv5Categories;
		$vars['wgArticleFeedbackv5BlacklistCategories'] = $wgArticleFeedbackv5BlacklistCategories;
		$vars['wgArticleFeedbackv5LotteryOdds'] = $wgArticleFeedbackv5LotteryOdds;
		$vars['wgArticleFeedbackv5Debug'] = $wgArticleFeedbackv5Debug;
		$vars['wgArticleFeedbackv5Bucket2TagNames'] = $wgArticleFeedbackv5Bucket2TagNames;
		$vars['wgArticleFeedbackv5Bucket5RatingCategories'] = $wgArticleFeedbackv5Bucket5RatingCategories;
		$vars['wgArticleFeedbackv5DisplayBuckets'] = $wgArticleFeedbackv5DisplayBuckets;
		$vars['wgArticleFeedbackv5Tracking'] = $wgArticleFeedbackv5Tracking;
		$vars['wgArticleFeedbackv5Options'] = $wgArticleFeedbackv5Options;
		$vars['wgArticleFeedbackv5LinkBuckets'] = $wgArticleFeedbackv5LinkBuckets;
		$vars['wgArticleFeedbackv5Namespaces'] = $wgArticleFeedbackv5Namespaces;
		$vars['wgArticleFeedbackv5LearnToEdit'] = $wgArticleFeedbackv5LearnToEdit;
		$vars['wgArticleFeedbackv5WhatsThisPage'] = wfMsgForContent( 'articlefeedbackv5-bucket5-form-panel-explanation-link' );
		$vars['wgArticleFeedbackv5TermsPage'] = wfMsgForContent( 'articlefeedbackv5-transparency-terms-url' );
		$vars['wgArticleFeedbackv5SurveyUrls'] = $wgArticleFeedbackv5SurveyUrls;
		$vars['wgArticleFeedbackv5InitialFeedbackPostCountToDisplay'] = $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		$vars['wgArticleFeedbackv5Permissions'] = array(
			'oversighter' => $wgUser->isAllowed( 'aftv5-delete-feedback' ),
			'moderator' => $wgUser->isAllowed( 'aftv5-hide-feedback' ),
			'editor' => !$wgUser->isAnon()
		);
		$vars['wgArticleFeedbackv5SpecialUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getLinkUrl();
		$vars['wgArticleFeedbackv5TalkPageLink'] = $wgArticleFeedbackv5TalkPageLink;
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

}
