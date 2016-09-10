<?php
/**
 * Article Feedback v5 extension, allowing readers to provide feedback
 * in the form of a "yes/no" assessment if the page is considered useful
 * and a free form text field for additional comments.
 *
 * @file
 * @ingroup Extensions
 */

/* Configuration */

/**
 * Default filter and direction settings for groups
 *
 * readers (= all)
 * editors (autoconfirmed)
 * monitors
 * oversighters
 */
$wgArticleFeedbackv5DefaultFilters = array (
	'aft-reader' => 'featured',
	'aft-editor' => 'featured',
	'aft-monitor' => 'featured',
	'aft-oversighter' => 'featured',
);

/**
 * (Hidden) user preference, saving the last selected filter. This is saved
 * in a cookie (more volatile) as well, but this will make sure that even
 * (logged-in) users who have cookies disabled, will get to see their last
 * selected filter (instead of the default filter)
 */
$wgDefaultUserOptions['aftv5-last-filter'] = null;

/**
 * Default sorts by filter
 *
 * Because priviliges don't play a part in default sort, the visible-,
 * notdeleted-, and all- prefixes have been removed.
 */
$wgArticleFeedbackv5DefaultSorts = array (
	'featured' => array( 'relevance', 'DESC' ),
	'unreviewed' => array( 'age', 'DESC' ),
	'helpful' => array( 'helpful', 'DESC' ),
	'unhelpful' => array( 'helpful', 'ASC' ),
	'flagged' => array( 'age', 'DESC' ),
	'useful' => array( 'age', 'DESC' ),
	'resolved' => array( 'age', 'DESC' ),
	'noaction' => array( 'age', 'DESC' ),
	'inappropriate' => array( 'age', 'DESC' ),
	'archived' => array( 'age', 'DESC' ),
	'allcomment' => array( 'age', 'DESC' ),
	'hidden' => array( 'age', 'DESC' ),
	'requested' => array( 'age', 'DESC' ),
	'declined' => array( 'age', 'DESC' ),
	'oversighted' => array( 'age', 'DESC' ),
	'all' => array( 'age', 'DESC' ),
);

/**
 * Relevance Scoring
 * name => integer scoring actions pairs
 * after changing this you should also change the values in relevance_score.sql and run it to reset relevance
 *
 * @var array
 */
$wgArticleFeedbackv5RelevanceScoring = array(
	'helpful' => 1,
	'undo-helpful' => -1,
	'unhelpful' => -1,
	'undo-unhelpful' => 1,
	'flag' => -5,
	'unflag' => 5,
	'autoflag' => 5,
	'feature' => 50,
	'unfeature' => -50,
	'resolve' => -5,
	'unresolve' => 5,
	'noaction' => -5,
	'unnoaction' => 5,
	'inappropriate' => -50,
	'uninappropriate' => 50,
	'autohide' => -100,
	'hide' => -100,
	'unhide' => 100,
	'archive' => -50,
	'unarchive' => 50,
	'request' => -150,
	'unrequest' => 150,
	'decline' => 150,
	'oversight' => -750,
	'unoversight' => 750,
);

/**
 * Enable/disable the "archived" filter. This is a setting that needs to explicitly be
 * set to true since the functionality will depend on a cronjob to be run periodically.
 *
 * @var bool true to enable, false to disable
 */
$wgArticleFeedbackv5AutoArchiveEnabled = false;

/**
 * Defines the auto-archive period for feedback that is not being considered useful.
 * Value should be an strtotime-capable format.
 *
 * If defined as string, this will be a fixed TTL based on the feedback creation date.
 *
 * It is also possible to set a certain TTL per offset of unreviewed feedback, e.g.:
 * array(
 * 	0 => '+2 years', // < 9: 2 years
 * 	10 => '+1 month', // 10-19: 1 month
 * 	20 => '+1 week', // 20-29: 1 week
 * 	30 => '+3 days', // 30-39: 3 days
 * 	40 => '+2 days', // > 40: 2 days
 * );
 *
 * @var array|string strtotime-capable format
 */
$wgArticleFeedbackv5AutoArchiveTtl = '+2 weeks';

// Defines whether or not there should be a link to the corresponding feedback on the article page
$wgArticleFeedbackv5ArticlePageLink = true;

// Defines whether or not there should be a link to the corresponding feedback on the article page's talk page
$wgArticleFeedbackv5TalkPageLink = true;

// Defines whether or not there should be a link to the watchlisted feedback on the watchlist page
$wgArticleFeedbackv5WatchlistLink = true;

// Defines whether or not the special page for feedback on a user's watchlisted pages is enabled
$wgArticleFeedbackv5Watchlist = true;

// Email address to send oversight request emails to, if set to null no emails are sent
$wgArticleFeedbackv5OversightEmails = null;

// Name to send oversight request emails to
$wgArticleFeedbackv5OversightEmailName = 'Oversighters';

// Help link for oversight email
$wgArticleFeedbackv5OversightEmailHelp = 'https://en.wikipedia.org/wiki/Wikipedia:Article_Feedback_Tool/Version_5/Help/Feedback_page_Oversighters';

// Help link for auto flag/hide etc
$wgArticleFeedbackv5AutoHelp = 'http://en.wikipedia.org/wiki/Wikipedia:Article_Feedback_Tool/Version_5/Help';

// How long text-based feedback is allowed to be before returning an error.
// Set to 0 to disable length checking entirely.
$wgArticleFeedbackv5MaxCommentLength = 5000;

// How long text-based activity items are allowed to be - note this will not return
// an error but simply chop notes that are too long
$wgArticleFeedbackv5MaxActivityNoteLength =  5000;

// Number of revisions to keep a rating alive for
$wgArticleFeedbackv5RatingLifetime = 30;

// Percentage of article AFT should be enabled on
$wgArticleFeedbackv5LotteryOdds = 100;

// Which categories the pages must belong to have the rating widget added (with _ in text)
// Extension is "disabled" if this field is an empty array (as per default configuration)
$wgArticleFeedbackv5Categories = array( 'Article_Feedback_5' );

// Which categories the pages must not belong to have the rating widget added (with _ in text)
$wgArticleFeedbackv5BlacklistCategories = array( 'Article_Feedback_Blacklist' );

// Allow/disallow the ability to enable or disable AFTv5 on a per-article basis.
// This feature will add an AFTv5 entry in page protection settings (for admins)
// or a simple enable/disable link for editors.
// Disabling this will remove said links & entry in ?action=protect & ignore
// existing opt-in/-outs, leaving only lottery & whitelist/blacklist categories
// to define if an article should get AFTv5.
$wgArticleFeedbackv5EnableProtection = true;

// Only load the module / enable the tool in these namespaces
// Default to $wgContentNamespaces (defaults to array( NS_MAIN ) ).
$wgArticleFeedbackv5Namespaces = $wgContentNamespaces;

// This puts the JavaScript into debug mode. In debug mode, you can set your
// own bucket by passing it in the url (e.g., ?bucket=1), and the showstopper
// error mode will have a useful error message, if one exists, rather than the
// default message.
$wgArticleFeedbackv5Debug = false;

// Bucket settings for display options
$wgArticleFeedbackv5DisplayBuckets = array(
	// Users can fall into one of several display buckets (these are defined in
	// modules/jquery.articlefeedbackv5/jquery.articlefeedbackv5.js).  When a
	// user arrives at the page, this config will be used by core bucketing to
	// decide which of the available form options they see.  Whenever there's
	// an update to the available buckets, change the version number to ensure
	// the new odds are applied to everyone, not just people who have yet to be
	// placed in a bucket.
	'buckets' => array(
		'0'  => 0, // display nothing
		'1'   => 0, // display 1-step feedback form
//		'2'   => 0, // abandoned
//		'3' => 0, // abandoned
		'4'  => 0, // display encouragement to edit page
//		'5'  => 0, // abandoned
		'6'   => 100, // display 2-step feedback form
	),
	// This version number is added to all tracking event names, so that
	// changes in the software don't corrupt the data being collected. Bump
	// this when you want to start a new "experiment".
	'version' => 6,
	// Let users be tracked for a month, and then rebucket them, allowing some
	// churn.
	'expires' => 30,
);

// Bucket settings for click tracking across the plugin
$wgArticleFeedbackv5Tracking = array(
	// Not all users need to be tracked, but we do want to track some users
	// over time - these buckets are used when deciding to track someone or
	// not, placing them in one of four buckets: "ignore" (no clicktracking),
	// "track" (all clicktracking), "track-front" (clicktracking only on the
	// front end widget), or "track-special" (clicktracking only on the special
	// page. When the 'version' key changes, users will be re-bucketed, so you
	// should always increment the 'version' key when changing this number to
	// ensure the new odds are applied to everyone, not just people who have
	// yet to be placed in a bucket.
	'buckets' => array(
		'ignore' => 100,
		'track' => 0,
		'track-front' => 0,
		'track-special' => 0,
	),
	// This version number is added to all tracking event names, and to all
	// cookies, so that changes in the software don't corrupt the data being
	// collected. Bump this when you want to start a new "experiment".
	'version' => 11,
	// Let users be tracked for a month, and then rebucket them, allowing some churn
	'expires' => 30,
);

// Bucket settings for links to the feedback form
$wgArticleFeedbackv5LinkBuckets = array(
	// Users can fall into one of several buckets for links.  These are:
	//  X: No link; user must scroll to the bottom of the page
	//  A: After the site tagline (below the article title)
	//  B: Below the titlebar on the right
	//  C: Button fixed to right side
	//  D: Button fixed to bottom right
	//  E: Button fixed to bottom right, design D2
	//  F: Button fixed to left side
	//  G: Button below logo
	//  H: Link on each section bar
	'buckets' => array(
		'X' => 100,
		'A' => 0,
		'B' => 0,
		'C' => 0,
		'D' => 0,
		'E' => 0,
		'F' => 0,
		'G' => 0,
		'H' => 0,
	),
	// This version number is added to all tracking event names, so that
	// changes in the software don't corrupt the data being collected. Bump
	// this when you want to start a new "experiment".
	'version' => 5,
	// Let users be tracked for a month, and then rebucket them, allowing some
	// churn.
	'expires' => 30,
);

// Bucket settings for CTAs
$wgArticleFeedbackv5CTABuckets = array(
	// Users can fall into one of several CTAs (these are defined in
	// modules/jquery.articlefeedbackv5/jquery.articlefeedbackv5.js).  When a
	// user arrives at the page, this config will be used by core bucketing to
	// decide which of the available CTAs they see.  Whenever there's
	// an update to the available buckets, change the version number to ensure
	// the new odds are applied to everyone, not just people who have yet to be
	// placed in a bucket.
	'buckets' => array(
		'0' => 0, // display nothing
		'1' => 0, // display "Enticement to edit"
		'2' => 0, // display "Learn more"
		'3' => 0, // display "Take a survey"
		// NOTE: only 4 will be visible for anons, so 100% of anons
		'4' => 90, // display "Sign up or login"
		// NOTE: 5 & 6 will only be visible for logged in, so 90% vs 10% of logged in
		'5' => 9, // display "View feedback"
		'6' => 1, // display "Visit Teahouse"
	),
	// This version number is added to all tracking event names, so that
	// changes in the software don't corrupt the data being collected. Bump
	// this when you want to start a new "experiment".
	'version' => 7,
	// Users may constantly be rebucketed, giving them new CTAs each time.
	'expires' => 0,
);

/**
 * Abusive threshold
 *
 * After this many users flag a comment as abusive, it is marked as such.
 *
 * @var int
 */
$wgArticleFeedbackv5AbusiveThreshold = 3;

/**
 * Hide abuse threshold
 *
 * After this many users flag a comment as abusive, it is hidden.
 *
 * @var int
 */
$wgArticleFeedbackv5HideAbuseThreshold = 5;

/**
 * Turn on abuse filtering
 *
 * If this is set to true, comments will be run through:
 *   1. $wgSpamRegex, if set
 *   2. SpamBlacklist, if installed
 *   3. AbuseFilter, if installed
 *
 * @var boolean
 */
$wgArticleFeedbackv5AbuseFiltering = false;

/**
 * This is the custom group name for AbuseFilter
 *
 * It ensures that AbuseFilter only pulls the filters related to AFT.  If you
 * would like AbuseFilter to pull all of the filters, enter 'default' here.
 *
 * @var string
 */
$wgArticleFeedbackv5AbuseFilterGroup = 'feedback';

/**
 * How many feedback posts per hour before triggering a throttling response.
 *
 * This is per-user and is governed by last-posted timestamps stored in a cookie.
 * If this is set to -1, the number of posts is not throttled.
 *
 * @var int
 */
$wgArticleFeedbackv5ThrottleThresholdPostsPerHour = 20;

/**
 * The full URL for the "Learn to Edit" link
 *
 * @var string
 */
$wgArticleFeedbackv5LearnToEdit = "//en.wikipedia.org/wiki/Wikipedia:Tutorial";

/**
 * The full URL for the survey link
 *
 * @var string
 */
$wgArticleFeedbackv5SurveyUrls = array(
	'1' => 'https://www.surveymonkey.com/s/aft5-1',
	'2' => 'https://www.surveymonkey.com/s/aft5-2',
	'3' => 'https://www.surveymonkey.com/s/aft5-3',
	'6' => 'https://www.surveymonkey.com/s/aft5-6',
);

/**
 * The full URL for the special page survey link
 *
 * @var string
 */
$wgArticleFeedbackv5SpecialPageSurveyUrl = 'https://www.surveymonkey.com/s/aft5-5';

/* Setup */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Article Feedback',
	'author' => array(
		'Greg Chiasson',
		'Reha Sterbin',
		'Sam Reed',
		'Roan Kattouw',
		'Trevor Parscal',
		'Brandon Harris',
		'Adam Miller',
		'Nimish Gautam',
		'Arthur Richards',
		'Timo Tijhof',
		'Ryan Kaldari',
		'Elizabeth M Smith',
		'Michael Jackson',
		'Matthias Mullie',
	),
	'version' => '5.2.0',
	'descriptionmsg' => 'articlefeedbackv5-desc',
	'url' => '//www.mediawiki.org/wiki/Extension:ArticleFeedbackv5'
);

// Autoloading
$wgAutoloadClasses['ApiArticleFeedbackv5']              = __DIR__ . '/api/ApiArticleFeedbackv5.php';
$wgAutoloadClasses['ApiViewFeedbackArticleFeedbackv5']  = __DIR__ . '/api/ApiViewFeedbackArticleFeedbackv5.php';
$wgAutoloadClasses['ApiSetStatusArticleFeedbackv5']     = __DIR__ . '/api/ApiSetStatusArticleFeedbackv5.php';
$wgAutoloadClasses['ApiAddFlagNoteArticleFeedbackv5']   = __DIR__ . '/api/ApiAddFlagNoteArticleFeedbackv5.php';
$wgAutoloadClasses['ApiFlagFeedbackArticleFeedbackv5']  = __DIR__ . '/api/ApiFlagFeedbackArticleFeedbackv5.php';
$wgAutoloadClasses['ApiGetCountArticleFeedbackv5']      = __DIR__ . '/api/ApiGetCountArticleFeedbackv5.php';
$wgAutoloadClasses['ApiViewActivityArticleFeedbackv5']  = __DIR__ . '/api/ApiViewActivityArticleFeedbackv5.php';
$wgAutoloadClasses['DataModel']                         = __DIR__ . '/data/DataModel.php';
$wgAutoloadClasses['DataModelBackend']                  = __DIR__ . '/data/DataModelBackend.php';
$wgAutoloadClasses['DataModelBackendLBFactory']         = __DIR__ . '/data/DataModelBackend.LBFactory.php';
$wgAutoloadClasses['DataModelList']                     = __DIR__ . '/data/DataModelList.php';
$wgAutoloadClasses['ArticleFeedbackv5Utils']            = __DIR__ . '/ArticleFeedbackv5.utils.php';
$wgAutoloadClasses['ArticleFeedbackv5Hooks']            = __DIR__ . '/ArticleFeedbackv5.hooks.php';
$wgAutoloadClasses['ArticleFeedbackv5Permissions']      = __DIR__ . '/ArticleFeedbackv5.permissions.php';
$wgAutoloadClasses['ArticleFeedbackv5Log']              = __DIR__ . '/ArticleFeedbackv5.log.php';
$wgAutoloadClasses['ArticleFeedbackv5LogFormatter']     = __DIR__ . '/ArticleFeedbackv5.log.php';
$wgAutoloadClasses['ArticleFeedbackv5ProtectionLogFormatter'] = __DIR__ . '/ArticleFeedbackv5.log.php';
$wgAutoloadClasses['ArticleFeedbackv5Flagging']         = __DIR__ . '/ArticleFeedbackv5.flagging.php';
$wgAutoloadClasses['ArticleFeedbackv5MailerJob']        = __DIR__ . '/ArticleFeedbackv5.mailerJob.php';
$wgAutoloadClasses['ArticleFeedbackv5Render']           = __DIR__ . '/ArticleFeedbackv5.render.php';
$wgAutoloadClasses['SpecialArticleFeedbackv5']          = __DIR__ . '/SpecialArticleFeedbackv5.php';
$wgAutoloadClasses['SpecialArticleFeedbackv5Watchlist'] = __DIR__ . '/SpecialArticleFeedbackv5Watchlist.php';
$wgAutoloadClasses['ArticleFeedbackv5Model']            = __DIR__ . '/ArticleFeedbackv5.model.php';
$wgAutoloadClasses['ArticleFeedbackv5BackendLBFactory'] = __DIR__ . '/ArticleFeedbackv5.backend.LBFactory.php';
$wgAutoloadClasses['ArticleFeedbackv5Activity']         = __DIR__ . '/ArticleFeedbackv5.activity.php';
$wgMessagesDirs['ArticleFeedbackv5']                    = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['ArticleFeedbackv5']          = __DIR__ . '/ArticleFeedbackv5.i18n.php';
$wgExtensionMessagesFiles['ArticleFeedbackv5Alias']     = __DIR__ . '/ArticleFeedbackv5.alias.php';

// Hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ArticleFeedbackv5Hooks::loadExtensionSchemaUpdates';
$wgHooks['BeforePageDisplay'][] = 'ArticleFeedbackv5Hooks::beforePageDisplay';
$wgHooks['ResourceLoaderGetConfigVars'][] = 'ArticleFeedbackv5Hooks::resourceLoaderGetConfigVars';
$wgHooks['MakeGlobalVariablesScript'][] = 'ArticleFeedbackv5Hooks::makeGlobalVariablesScript';
$wgHooks['GetPreferences'][] = 'ArticleFeedbackv5Hooks::getPreferences';
$wgHooks['EditPage::showEditForm:fields'][] = 'ArticleFeedbackv5Hooks::pushFieldsToEdit';
$wgHooks['EditPage::attemptSave'][] = 'ArticleFeedbackv5Hooks::editAttempt';
$wgHooks['ArticleSaveComplete'][] = 'ArticleFeedbackv5Hooks::editSuccess';
$wgHooks['ContribsPager::reallyDoQuery'][] = 'ArticleFeedbackv5Hooks::contributionsData';
$wgHooks['ContributionsLineEnding'][] = 'ArticleFeedbackv5Hooks::contributionsLineEnding';
$wgHooks['ProtectionForm::buildForm'][] = 'ArticleFeedbackv5Hooks::onProtectionForm';
$wgHooks['ProtectionForm::save'][] = 'ArticleFeedbackv5Hooks::onProtectionSave';
$wgHooks['ProtectionForm::showLogExtract'][] = 'ArticleFeedbackv5Hooks::onShowLogExtract';
$wgHooks['UserLoginComplete'][] = 'ArticleFeedbackv5Hooks::userLoginComplete';
$wgHooks['UserGetReservedNames'][] = 'ArticleFeedbackv5Hooks::onUserGetReservedNames';

// API Registration
$wgAPIListModules['articlefeedbackv5-view-feedback'] = 'ApiViewFeedbackArticleFeedbackv5';
$wgAPIListModules['articlefeedbackv5-view-activity'] = 'ApiViewActivityArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5-set-status']        = 'ApiSetStatusArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5-add-flag-note']     = 'ApiAddFlagNoteArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5-flag-feedback']     = 'ApiFlagFeedbackArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5-get-count']         = 'ApiGetCountArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5']                   = 'ApiArticleFeedbackv5';

// Special Page
$wgSpecialPages['ArticleFeedbackv5'] = 'SpecialArticleFeedbackv5';
$wgSpecialPages['ArticleFeedbackv5Watchlist'] = 'SpecialArticleFeedbackv5Watchlist';

$wgArticleFeedbackv5Permissions = array(
	'aft-reader',
	'aft-member',
	'aft-editor',
	'aft-monitor',
	'aft-administrator',
	'aft-oversighter',
);
$wgAvailableRights += $wgArticleFeedbackv5Permissions;

// Jobs
$wgJobClasses['ArticleFeedbackv5MailerJob'] = 'ArticleFeedbackv5MailerJob';

// Logging
$wgLogTypes[] = 'articlefeedbackv5';

// register log handler for AFT protection log
$wgLogActionsHandlers['articlefeedbackv5/protect'] = 'ArticleFeedbackv5ProtectionLogFormatter';

// register log handler for feedback submission
$wgLogActionsHandlers['articlefeedbackv5/create'] = 'ArticleFeedbackv5LogFormatter';

// register activity log formatter hooks
foreach( ArticleFeedbackv5Activity::$actions as $action => $options ) {
	if ( isset( $options['log_type'] ) ) {
		$log = $options['log_type'];
		$wgLogActionsHandlers["$log/$action"] = 'ArticleFeedbackv5LogFormatter';
	}
}

if ( $wgArticleFeedbackv5AbuseFilterGroup != 'default' ) {
	// Add a custom filter group for AbuseFilter
	$wgAbuseFilterValidGroups[] = $wgArticleFeedbackv5AbuseFilterGroup;

	// set abusefilter emergency disable values for AFT feedback
	$wgAbuseFilterEmergencyDisableThreshold[$wgArticleFeedbackv5AbuseFilterGroup] = 0.10;
	$wgAbuseFilterEmergencyDisableCount[$wgArticleFeedbackv5AbuseFilterGroup] = 50;
	$wgAbuseFilterEmergencyDisableAge[$wgArticleFeedbackv5AbuseFilterGroup] = 86400; // One day.
}

// Add custom action handlers for AbuseFilter
$wgAbuseFilterAvailableActions[] = 'aftv5resolve';
$wgAbuseFilterAvailableActions[] = 'aftv5flagabuse';
$wgAbuseFilterAvailableActions[] = 'aftv5hide';
$wgAbuseFilterAvailableActions[] = 'aftv5request';

// Permissions: 6 levels of permissions are built into ArticleFeedbackv5: reader, member, editor,
// monitor, administrator, oversighter. The default (below-configured) permissions scheme can be seen at
// http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Access_and_permissions
$wgArticleFeedbackv5DefaultPermissions = array(
	'aft-reader' => array( '*', 'user', 'confirmed', 'autoconfirmed', 'rollbacker', 'reviewer', 'sysop', 'oversight' ),
	'aft-member' => array( 'user', 'confirmed', 'autoconfirmed', 'rollbacker', 'reviewer', 'sysop', 'oversight' ),
	'aft-editor' => array( 'confirmed', 'autoconfirmed', 'rollbacker', 'reviewer', 'sysop', 'oversight' ),
	'aft-monitor' => array( 'rollbacker', 'reviewer', 'sysop', 'oversight' ),
	'aft-administrator' => array( 'sysop', 'oversight' ),
	'aft-oversighter' => array( 'oversight' ),
);
foreach ( $wgArticleFeedbackv5DefaultPermissions as $permission => $groups ) {
	foreach ( (array) $groups as $group ) {
		if ( isset( $wgGroupPermissions[$group] ) ) {
			$wgGroupPermissions[$group][$permission] = true;
		}
	}
}

// register resources with ResourceLoader
$wgArticleFeedbackResourcePaths = array(
	'localBasePath' => __DIR__ . '/modules',
	'remoteExtPath' => "ArticleFeedbackv5/modules",
);
$wgResourceModules['jquery.articleFeedbackv5.utils'] = array(
	'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.utils.js',
	'messages' => array(
		'articlefeedbackv5-error-unknown',
	),
	'dependencies' => array(
		'mediawiki.util',
		'mediawiki.user',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['ext.articleFeedbackv5.startup'] = array(
	'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.startup.js',
	'messages' => array(
		'articlefeedbackv5-toolbox-enable',
		'articlefeedbackv5-enabled-form-message',
	),
	'dependencies' => array(
		'mediawiki.util',
		'mediawiki.user',
		'jquery.articleFeedbackv5.utils',
		'mediawiki.jqueryMsg',
		'mediawiki.api',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['ext.articleFeedbackv5'] = array(
	'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.js',
	'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.css',
	'messages' => array(
		'articlefeedbackv5-sitesub-linktext',
		'articlefeedbackv5-titlebar-linktext',
		'articlefeedbackv5-fixedtab-linktext',
		'articlefeedbackv5-bottomrighttab-linktext',
		'articlefeedbackv5-section-linktext',
		'articlefeedbackv5-article-view-feedback',
	),
	'dependencies' => array(
		'mediawiki.jqueryMsg',
		'jquery.ui.button',
		'jquery.articleFeedbackv5',
		'jquery.cookie',
		'jquery.articleFeedbackv5.track',
		'jquery.articleFeedbackv5.utils',
		'mediawiki.api',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['ext.articleFeedbackv5.ie'] = array(
	'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.ie.js',
	'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.ie.css'
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['ext.articleFeedbackv5.dashboard'] = array(
	'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.dashboard.js',
	'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.dashboard.css',
	'messages' => array(
		'articlefeedbackv5-no-feedback',
		'articlefeedbackv5-unsupported-message',
		'articlefeedbackv5-page-disabled',
	),
	'dependencies' => array(
		'jquery.articleFeedbackv5.utils',
		'jquery.articleFeedbackv5.special',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['jquery.articleFeedbackv5.track'] = array(
	'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.track.js',
	'dependencies' => array(
		'mediawiki.util',
		'mediawiki.user',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['ext.articleFeedbackv5.talk'] = array(
	'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.talk.js',
	'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.talk.css',
	'messages' => array(
		'articlefeedbackv5-talk-view-feedback',
	),
	'dependencies' => array(
		'jquery.articleFeedbackv5.utils',
		'jquery.articleFeedbackv5.track',
		'mediawiki.api',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['ext.articleFeedbackv5.watchlist'] = array(
	'scripts' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.watchlist.js',
	'styles' => 'ext.articleFeedbackv5/ext.articleFeedbackv5.watchlist.css',
	'messages' => array(
		'articlefeedbackv5-watchlist-view-feedback',
	),
	'dependencies' => array(
		'jquery.articleFeedbackv5.track',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['jquery.articleFeedbackv5'] = array(
	'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.js',
	'styles' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.css',
	'messages' => array(
		'articlefeedbackv5-error-validation',
		'articlefeedbackv5-error-nofeedback',
		'articlefeedbackv5-error-unknown',
		'articlefeedbackv5-error-submit',
		'articlefeedbackv5-cta-thanks',
		'articlefeedbackv5-error-abuse',
		'articlefeedbackv5-error-abuse-link',
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
		'articlefeedbackv5-bucket1-form-submit',
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
		'articlefeedbackv5-leave-warning',
		'articlefeedbackv5-error',
		'articlefeedbackv5-help-tooltip-title',
		'articlefeedbackv5-help-tooltip-info',
		'articlefeedbackv5-help-tooltip-linktext',
		'articlefeedbackv5-help-form-linkurl',
		'articlefeedbackv5-help-form-linkurl-editors',
		'articlefeedbackv5-help-form-linkurl-monitors',
		'articlefeedbackv5-help-form-linkurl-oversighters',
		'articlefeedbackv5-help-transparency-terms',
		'articlefeedbackv5-help-transparency-terms-anon',
		'parentheses',
		'articlefeedbackv5-disable-flyover-title',
		'articlefeedbackv5-disable-flyover-help-message',
		'articlefeedbackv5-disable-flyover-prefbutton',
		'articlefeedbackv5-disable-preference',
		'pipe-separator',
		'articlefeedbackv5-toolbox-view',
		'articlefeedbackv5-toolbox-add',
		'mypreferences',
		'prefs-rendering',
	),
	'dependencies' => array(
		'jquery.appear',
		'jquery.tipsy',
		'json',
		'jquery.localize',
		'jquery.ui.button',
		'jquery.cookie',
		'jquery.placeholder',
		'mediawiki.jqueryMsg',
		'jquery.articleFeedbackv5.track',
		'jquery.effects.highlight',
		'mediawiki.Uri',
	),
) + $wgArticleFeedbackResourcePaths;
$wgResourceModules['jquery.articleFeedbackv5.special'] = array(
	'scripts' => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.js',
	'styles'   => 'jquery.articleFeedbackv5/jquery.articleFeedbackv5.special.css',
	'messages' => array(
		'articlefeedbackv5-error-flagging',
		'articlefeedbackv5-invalid-feedback-id',
		'articlefeedbackv5-invalid-log-id',
		'articlefeedbackv5-invalid-log-update',
		'articlefeedbackv5-invalid-feedback-flag',
		'articlefeedbackv5-invalid-feedback-state',
		'articlefeedbackv5-feedback-reloaded-after-error',

		'articlefeedbackv5-comment-more',
		'articlefeedbackv5-comment-less',
		'articlefeedbackv5-error-loading-feedback',
		'articlefeedbackv5-loading-tag',
		'articlefeedbackv5-permalink-activity-more',
		'articlefeedbackv5-permalink-activity-fewer',
		'articlefeedbackv5-abuse-saved',
		'articlefeedbackv5-abuse-saved-tooltip',
		'articlefeedbackv5-form-unrequest',
		'articlefeedbackv5-form-declined',

		'articlefeedbackv5-help-special-linkurl',
		'articlefeedbackv5-help-special-linkurl-editors',
		'articlefeedbackv5-help-special-linkurl-monitors',
		'articlefeedbackv5-help-special-linkurl-oversighters',

		'articlefeedbackv5-viewactivity',

		'articlefeedbackv5-noteflyover-feature-caption',
//		'articlefeedbackv5-noteflyover-feature-description',
		'articlefeedbackv5-noteflyover-feature-label',
		'articlefeedbackv5-noteflyover-feature-placeholder',
		'articlefeedbackv5-noteflyover-feature-submit',
		'articlefeedbackv5-noteflyover-feature-help',
		'articlefeedbackv5-noteflyover-feature-help-link',

		'articlefeedbackv5-noteflyover-unfeature-caption',
//		'articlefeedbackv5-noteflyover-unfeature-description',
		'articlefeedbackv5-noteflyover-unfeature-label',
		'articlefeedbackv5-noteflyover-unfeature-placeholder',
		'articlefeedbackv5-noteflyover-unfeature-submit',
		'articlefeedbackv5-noteflyover-unfeature-help',
		'articlefeedbackv5-noteflyover-unfeature-help-link',

		'articlefeedbackv5-noteflyover-resolve-caption',
//		'articlefeedbackv5-noteflyover-resolve-description',
		'articlefeedbackv5-noteflyover-resolve-label',
		'articlefeedbackv5-noteflyover-resolve-placeholder',
		'articlefeedbackv5-noteflyover-resolve-submit',
		'articlefeedbackv5-noteflyover-resolve-help',
		'articlefeedbackv5-noteflyover-resolve-help-link',

		'articlefeedbackv5-noteflyover-unresolve-caption',
//		'articlefeedbackv5-noteflyover-unresolve-description',
		'articlefeedbackv5-noteflyover-unresolve-label',
		'articlefeedbackv5-noteflyover-unresolve-placeholder',
		'articlefeedbackv5-noteflyover-unresolve-submit',
		'articlefeedbackv5-noteflyover-unresolve-help',
		'articlefeedbackv5-noteflyover-unresolve-help-link',

		'articlefeedbackv5-noteflyover-noaction-caption',
//		'articlefeedbackv5-noteflyover-noaction-description',
		'articlefeedbackv5-noteflyover-noaction-label',
		'articlefeedbackv5-noteflyover-noaction-placeholder',
		'articlefeedbackv5-noteflyover-noaction-submit',
		'articlefeedbackv5-noteflyover-noaction-help',
		'articlefeedbackv5-noteflyover-noaction-help-link',

		'articlefeedbackv5-noteflyover-unnoaction-caption',
//		'articlefeedbackv5-noteflyover-unnoaction-description',
		'articlefeedbackv5-noteflyover-unnoaction-label',
		'articlefeedbackv5-noteflyover-unnoaction-placeholder',
		'articlefeedbackv5-noteflyover-unnoaction-submit',
		'articlefeedbackv5-noteflyover-unnoaction-help',
		'articlefeedbackv5-noteflyover-unnoaction-help-link',

		'articlefeedbackv5-noteflyover-inappropriate-caption',
//		'articlefeedbackv5-noteflyover-inappropriate-description',
		'articlefeedbackv5-noteflyover-inappropriate-label',
		'articlefeedbackv5-noteflyover-inappropriate-placeholder',
		'articlefeedbackv5-noteflyover-inappropriate-submit',
		'articlefeedbackv5-noteflyover-inappropriate-help',
		'articlefeedbackv5-noteflyover-inappropriate-help-link',

		'articlefeedbackv5-noteflyover-uninappropriate-caption',
//		'articlefeedbackv5-noteflyover-uninappropriate-description',
		'articlefeedbackv5-noteflyover-uninappropriate-label',
		'articlefeedbackv5-noteflyover-uninappropriate-placeholder',
		'articlefeedbackv5-noteflyover-uninappropriate-submit',
		'articlefeedbackv5-noteflyover-uninappropriate-help',
		'articlefeedbackv5-noteflyover-uninappropriate-help-link',

		'articlefeedbackv5-noteflyover-archive-caption',
//		'articlefeedbackv5-noteflyover-archive-description',
		'articlefeedbackv5-noteflyover-archive-label',
		'articlefeedbackv5-noteflyover-archive-placeholder',
		'articlefeedbackv5-noteflyover-archive-submit',
		'articlefeedbackv5-noteflyover-archive-help',
		'articlefeedbackv5-noteflyover-archive-help-link',

		'articlefeedbackv5-noteflyover-unarchive-caption',
//		'articlefeedbackv5-noteflyover-unarchive-description',
		'articlefeedbackv5-noteflyover-unarchive-label',
		'articlefeedbackv5-noteflyover-unarchive-placeholder',
		'articlefeedbackv5-noteflyover-unarchive-submit',
		'articlefeedbackv5-noteflyover-unarchive-help',
		'articlefeedbackv5-noteflyover-unarchive-help-link',

		'articlefeedbackv5-noteflyover-hide-caption',
//		'articlefeedbackv5-noteflyover-hide-description',
		'articlefeedbackv5-noteflyover-hide-label',
		'articlefeedbackv5-noteflyover-hide-placeholder',
		'articlefeedbackv5-noteflyover-hide-submit',
		'articlefeedbackv5-noteflyover-hide-help',
		'articlefeedbackv5-noteflyover-hide-help-link',

		'articlefeedbackv5-noteflyover-unhide-caption',
//		'articlefeedbackv5-noteflyover-unhide-description',
		'articlefeedbackv5-noteflyover-unhide-label',
		'articlefeedbackv5-noteflyover-unhide-placeholder',
		'articlefeedbackv5-noteflyover-unhide-submit',
		'articlefeedbackv5-noteflyover-unhide-help',
		'articlefeedbackv5-noteflyover-unhide-help-link',

		'articlefeedbackv5-noteflyover-request-caption',
//		'articlefeedbackv5-noteflyover-request-description',
		'articlefeedbackv5-noteflyover-request-label',
		'articlefeedbackv5-noteflyover-request-placeholder',
		'articlefeedbackv5-noteflyover-request-submit',
		'articlefeedbackv5-noteflyover-request-help',
		'articlefeedbackv5-noteflyover-request-help-link',

		'articlefeedbackv5-noteflyover-unrequest-caption',
//		'articlefeedbackv5-noteflyover-unrequest-description',
		'articlefeedbackv5-noteflyover-unrequest-label',
		'articlefeedbackv5-noteflyover-unrequest-placeholder',
		'articlefeedbackv5-noteflyover-unrequest-submit',
		'articlefeedbackv5-noteflyover-unrequest-help',
		'articlefeedbackv5-noteflyover-unrequest-help-link',

		'articlefeedbackv5-noteflyover-decline-caption',
//		'articlefeedbackv5-noteflyover-decline-description',
		'articlefeedbackv5-noteflyover-decline-label',
		'articlefeedbackv5-noteflyover-decline-placeholder',
		'articlefeedbackv5-noteflyover-decline-submit',
		'articlefeedbackv5-noteflyover-decline-help',
		'articlefeedbackv5-noteflyover-decline-help-link',

		'articlefeedbackv5-noteflyover-oversight-caption',
//		'articlefeedbackv5-noteflyover-oversight-description',
		'articlefeedbackv5-noteflyover-oversight-label',
		'articlefeedbackv5-noteflyover-oversight-placeholder',
		'articlefeedbackv5-noteflyover-oversight-submit',
		'articlefeedbackv5-noteflyover-oversight-help',
		'articlefeedbackv5-noteflyover-oversight-help-link',

		'articlefeedbackv5-noteflyover-unoversight-caption',
//		'articlefeedbackv5-noteflyover-unoversight-description',
		'articlefeedbackv5-noteflyover-unoversight-label',
		'articlefeedbackv5-noteflyover-unoversight-placeholder',
		'articlefeedbackv5-noteflyover-unoversight-submit',
		'articlefeedbackv5-noteflyover-unoversight-help',
		'articlefeedbackv5-noteflyover-unoversight-help-link',

		'articlefeedbackv5-activity-pane-header',

		'articlefeedbackv5-settings-status-enable',
		'articlefeedbackv5-settings-status-disable',
	),
	'dependencies' => array(
		'mediawiki.util',
		'jquery.tipsy',
		'jquery.localize',
		'jquery.articleFeedbackv5.track',
		'json',
		'jquery.ui.button',
	),
) + $wgArticleFeedbackResourcePaths;

/*
 * Database setup.
 *
 * $wgArticleFeedbackv5BackendClass defines that backend class to be used by
 * AFT's DataModel. Currently, only 1 (ArticleFeedbackv5BackendLBFactory)
 * backend is supported, so better don't touch that ;)
 *
 * $wgArticleFeedbackv5Cluster will define what external server should be used.
 * If set to false, the current database (wfGetDB) will be used to read/write
 * data from/to. If AFT data is supposed to be stored on an external database,
 * set the value of this variable to the $wgExternalServers key representing
 * that external connection.
 */
$wgArticleFeedbackv5BackendClass = 'ArticleFeedbackv5BackendLBFactory';
$wgArticleFeedbackv5Cluster = false;
