<?php
/**
 * Hooks for ArticleFeedback
 *
 * @file
 * @ingroup Extensions
 */

class ArticleFeedbackv5Hooks {

	protected static $modules = array(
		'ext.articleFeedback.startup' => array(
			'scripts' => 'ext.articleFeedback/ext.articleFeedback.startup.js',
			'dependencies' => array(
				'mediawiki.util',
			),
		),
		'ext.articleFeedback' => array(
			'scripts' => 'ext.articleFeedback/ext.articleFeedback.js',
			'styles' => 'ext.articleFeedback/ext.articleFeedback.css',
			'messages' => array(
				'articlefeedbackv5-pitch-reject',
				'articlefeedbackv5-pitch-or',
				'articlefeedbackv5-pitch-thanks',
				'articlefeedbackv5-pitch-survey-message',
				'articlefeedbackv5-pitch-survey-body',
				'articlefeedbackv5-pitch-survey-accept',
				'articlefeedbackv5-pitch-join-message',
				'articlefeedbackv5-pitch-join-body',
				'articlefeedbackv5-pitch-join-accept',
				'articlefeedbackv5-pitch-join-login',
				'articlefeedbackv5-pitch-edit-message',
				'articlefeedbackv5-pitch-edit-body',
				'articlefeedbackv5-pitch-edit-accept',
				'articlefeedbackv5-survey-title',
				'articlefeedbackv5-survey-message-success',
				'articlefeedbackv5-survey-message-error',
				'articlefeedbackv5-survey-disclaimer',
				'articlefeedbackv5-survey-disclaimerlink',
				'articlefeedbackv5-privacyurl'
			),
			'dependencies' => array(
				'jquery.ui.dialog',
				'jquery.ui.button',
				'jquery.articleFeedback',
				'jquery.cookie',
				'jquery.clickTracking',
				'ext.articleFeedback.ratingi18n',
			),
		),
		'ext.articleFeedback.ratingi18n' => array(
			'messages' => null, // Filled in by the resourceLoaderRegisterModules() hook function later
		),
		'ext.articleFeedback.dashboard' => array(
			'scripts' => 'ext.articleFeedback/ext.articleFeedback.dashboard.js',
			'styles' => 'ext.articleFeedback/ext.articleFeedback.dashboard.css',
		),
		'jquery.articleFeedback' => array(
			'scripts' => 'jquery.articleFeedback/jquery.articleFeedback.js',
			'styles' => 'jquery.articleFeedback/jquery.articleFeedback.css',
			'messages' => array(
				'articlefeedbackv5-error',
				'articlefeedbackv5-form-switch-label',
				'articlefeedbackv5-form-panel-title',
				'articlefeedbackv5-form-panel-explanation',
				'articlefeedbackv5-form-panel-explanation-link',
				'articlefeedbackv5-form-panel-clear',
				'articlefeedbackv5-form-panel-expertise',
				'articlefeedbackv5-form-panel-expertise-studies',
				'articlefeedbackv5-form-panel-expertise-profession',
				'articlefeedbackv5-form-panel-expertise-hobby',
				'articlefeedbackv5-form-panel-expertise-other',
				'articlefeedbackv5-form-panel-helpimprove',
				'articlefeedbackv5-form-panel-helpimprove-note',
				'articlefeedbackv5-form-panel-helpimprove-email-placeholder',
				'articlefeedbackv5-form-panel-helpimprove-privacy',
				'articlefeedbackv5-form-panel-submit',
				'articlefeedbackv5-form-panel-success',
				'articlefeedbackv5-form-panel-pending',
				'articlefeedbackv5-form-panel-expiry-title',
				'articlefeedbackv5-form-panel-expiry-message',
				'articlefeedbackv5-report-switch-label',
				'articlefeedbackv5-report-panel-title',
				'articlefeedbackv5-report-panel-description',
				'articlefeedbackv5-report-empty',
				'articlefeedbackv5-report-ratings',
				'articlefeedbackv5-privacyurl'
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
	);

	public static function addFeedbackLink($template, &$content_actions) {
	   # This needs like an is-article check or something
		$content_actions['namespaces'][] = array(
			'class'   => false or 'selected',
			'text'    => 'Feedback',
			'href'    => '/wiki-dev/index.php/Feedback:Greg', #TODO
			'context' => 'feedback'
		);
		return true;
	}

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
			// Guess that's it for now?
			global $wgExtNewTables;
			$wgExtNewTables[] = array(
				'article_feedback',
				dirname( __FILE__ ) . '/sql/ArticleFeedbackv5.sql'
			);
		} else {
			# no-op, since we dobn't have upgrades yet.
		}
		return true;
	}

	/**
	 * ParserTestTables hook
	 */
	public static function parserTestTables( &$tables ) {
		$tables[] = 'article_feedback';
		$tables[] = 'article_feedback_pages';
		$tables[] = 'article_feedback_revisions';
		$tables[] = 'article_feedback_properties';
		return true;
	}

	/**
	 * BeforePageDisplay hook
	 */
	public static function beforePageDisplay( $out ) {
		$out->addModules( 'ext.articleFeedback.startup' );
		return true;
	}

	/**
	 * ResourceLoaderRegisterModules hook
	 */
	public static function resourceLoaderRegisterModules( &$resourceLoader ) {
		global $wgExtensionAssetsPath;
		$localpath = dirname( __FILE__ ) . '/modules';
		$remotepath = "$wgExtensionAssetsPath/ArticleFeedbackv5/modules";

		foreach ( self::$modules as $name => $resources ) {
			$resourceLoader->register(
				$name, new ResourceLoaderFileModule( $resources, $localpath, $remotepath )
			);
		}
		return true;
	}

	/**
	 * ResourceLoaderGetConfigVars hook
	 */
	public static function resourceLoaderGetConfigVars( &$vars ) {
		global $wgArticleFeedbackv5SMaxage,
			$wgArticleFeedbackv5Categories,
			$wgArticleFeedbackv5BlacklistCategories,
			$wgArticleFeedbackv5LotteryOdds,
			$wgArticleFeedbackv5Tracking,
			$wgArticleFeedbackv5Options,
			$wgArticleFeedbackv5Namespaces;
		$vars['wgArticleFeedbackv5SMaxage'] = $wgArticleFeedbackv5SMaxage;
		$vars['wgArticleFeedbackv5Categories'] = $wgArticleFeedbackv5Categories;
		$vars['wgArticleFeedbackv5BlacklistCategories'] = $wgArticleFeedbackv5BlacklistCategories;
		$vars['wgArticleFeedbackv5LotteryOdds'] = $wgArticleFeedbackv5LotteryOdds;
		$vars['wgArticleFeedbackv5Tracking'] = $wgArticleFeedbackv5Tracking;
		$vars['wgArticleFeedbackv5Options'] = $wgArticleFeedbackv5Options;
		$vars['wgArticleFeedbackv5Namespaces'] = $wgArticleFeedbackv5Namespaces;
		$vars['wgArticleFeedbackv5WhatsThisPage'] = wfMsgForContent( 'articlefeedbackv5-form-panel-explanation-link' );

		$fields = ApiArticleFeedbackv5Utils::getFields();
error_log('hi');
		foreach( $fields as $field ) {
			$vars['wgArticleFeedbackv5RatingTypes'][] = $field->aaf_name;
			$vars['wgArticleFeedbackv5RatingTypesFlipped'][$field->aaf_name] = $fiekd->aaf_id;
		}
		return true;
	}

	/**
	 * Add the preference in the user preferences with the GetPreferences hook.
	 * @param $user User
	 * @param $preferences
	 */
	public static function getPreferences( $user, &$preferences ) {
		$preferences['articlefeedbackv5-disable'] = array(
			'type' => 'check',
			'section' => 'rendering/advancedrendering',
			'label-message' => 'articlefeedbackv5-disable-preference',
		);
		return true;
	}
}
