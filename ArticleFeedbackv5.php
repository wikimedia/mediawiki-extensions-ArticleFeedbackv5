<?php
/**
 * Article Feedback extension
 *
 * @file
 * @ingroup Extensions
 *
 * @author Trevor Parscal <trevor@wikimedia.org>
 * @license GPL v2 or later
 * @version 0.1.0
 */

/* Configuration */

/**
 * Default filter and direction settings for groups
 *
 * all
 * editors (autoconfirmed) (can-feature)
 * monitors (see hidden)
 * oversighters (see deleted)
 */
$wgArticleFeedbackv5DefaultFilters = array (
	'all'      => 'visible-relevant',
	'featured' => 'visible-relevant',
	'hidden'   => 'visible-relevant',
	'deleted'  => 'visible-relevant',
	'central'  => 'visible-relevant',
);

/**
 * Default sorts by filter
 *
 * Because priviliges don't play a part in default sort, the visible-,
 * notdeleted-, and all- prefixes have been removed.
 */
$wgArticleFeedbackv5DefaultSorts = array (
	'abusive'       => array( 'age', 'desc' ),
	'all'           => array( 'age', 'desc' ),
	'comment'       => array( 'age', 'desc' ),
	'declined'      => array( 'age', 'desc' ),
	'featured'      => array( 'relevance', 'asc' ),
	'helpful'       => array( 'helpful', 'desc' ),
	'hidden'        => array( 'age', 'desc' ),
	'id'            => array( 'age', 'desc' ),
	'notdeleted'    => array( 'age', 'desc' ),
	'oversighted'   => array( 'age', 'desc' ),
	'relevant'      => array( 'relevance', 'asc' ),
	'requested'     => array( 'age', 'desc' ),
	'resolved'      => array( 'age', 'desc' ),
	'unfeatured'    => array( 'relevance', 'desc' ),
	'unhelpful'     => array( 'helpful', 'asc' ),
	'unhidden'      => array( 'age', 'desc' ),
	'unoversighted' => array( 'age', 'desc' ),
	'unrequested'   => array( 'age', 'desc' ),
	'unresolved'    => array( 'age', 'desc' ),
	'visible'       => array( 'age', 'desc' ),
);

/**
 * Relevance Cutoff value
 * A signed integer controlling the point at which items are "cutoff" from the relevant filter
 * That means anything > value is in the relevant filter, anything <= is "cutoff"
 */
$wgArticleFeedbackv5Cutoff = -5;

/**
 * Relevance Scoring
 * name => integer scoring actions pairs
 * after changing this you should also change the values in relevance_score.sql and run it to reset relevance
 *
 * @var array
 */
$wgArticleFeedbackv5RelevanceScoring = array(
	'feature' => 50,
	'unfeature' => -50,
	'helpful' => 1,
	'unhelpful' => -1,
	'resolve' => -5,
	'unresolve' => 5,
	'flag' => -5,
	'unflag' => 5,
	'autohide' => -100,
	'hide' => -100,
	'unhide' => 100,
	'request' => -150,
	'unrequest' => 150,
	'decline' => 150,
	'oversight' => -750,
	'unoversight' => 750,
);

// Defines whether or not there should be a link to the corresponding feedback on the page's talk page
$wgArticleFeedbackv5TalkPageLink = true;

// Defines whether or not there should be a link to the watchlisted feedback on the watchlist page
$wgArticleFeedbackv5WatchlistLink = true;

// Email address to send oversight request emails to, if set to null no emails are sent
$wgArticleFeedbackv5OversightEmails = null;

// Name to send oversight request emails to
$wgArticleFeedbackv5OversightEmailName = 'Oversighters';

// Help link for oversight email
$wgArticleFeedbackv5OversightEmailHelp = 'http://en.wikipedia.org/wiki/Wikipedia:Article_Feedback_Tool/Version_5/Help/Feedback_page_Oversighters';

// Help link for auto flag/hide etc
$wgArticleFeedbackv5AutoHelp = 'http://en.wikipedia.org/wiki/Wikipedia:Article_Feedback_Tool/Version_5/Help';

// How long text-based feedback is allowed to be before returning an error.
// Set to 0 to disable length checking entirely.
$wgArticleFeedbackv5MaxCommentLength = 5000;

// How long text-based activity items are allowed to be - note this will not return
// an error but simply chop notes that are too long
$wgArticleFeedbackv5MaxActivityNoteLength =  5000;

// How long to keep ratings in the squids (they will also be purged when needed)
$wgArticleFeedbackv5SMaxage = 2592000;

// Number of revisions to keep a rating alive for
$wgArticleFeedbackv5RatingLifetime = 30;

// Percentage of article AFT should be enabled on
$wgArticleFeedbackv5LotteryOdds = 100;

// Which categories the pages must belong to have the rating widget added (with _ in text)
// Extension is "disabled" if this field is an empty array (as per default configuration)
$wgArticleFeedbackv5Categories = array( 'Article_Feedback_5' );

// Which categories the pages must not belong to have the rating widget added (with _ in text)
$wgArticleFeedbackv5BlacklistCategories = array( 'Article_Feedback_Blacklist' );

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
	// Track the event of users being bucketed - so we can be sure the odds
	// worked out right. [LATER - depends on UDP logging being set up]
	'tracked' => false,
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
		'ignore' => 0,
		'track' => 0,
		'track-front' => 100,
		'track-special' => 0,
	),
	// This version number is added to all tracking event names, and to all
	// cookies, so that changes in the software don't corrupt the data being
	// collected. Bump this when you want to start a new "experiment".
	'version' => 10,
	// Let users be tracked for a month, and then rebucket them, allowing some churn
	'expires' => 30,
	// Do not track the event of users being bucketed, at least for now.
	'tracked' => false,
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
	// Track the event of users being bucketed - so we can be sure the odds
	// worked out right. [LATER - depends on UDP logging being set up]
	'tracked' => false
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
		'1' => 49, // display "Enticement to edit"
		'2' => 0, // display "Learn more"
		'3' => 0, // display "Take a survey"
		'4' => 50, // display "Sign up or login"
		'5' => 0, // display "View feedback"
		'6' => 1, // display "Visit Teahouse"
	),
	// This version number is added to all tracking event names, so that
	// changes in the software don't corrupt the data being collected. Bump
	// this when you want to start a new "experiment".
	'version' => 6,
	// Users may constantly be rebucketed, giving them new CTAs each time.
	'expires' => 30, // metrics testing of buckets, need fixed CTAs for now
	// Track the event of users being bucketed - so we can be sure the odds
	// worked out right. [LATER - depends on UDP logging being set up]
	'tracked' => false,
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

// Replace default emailcapture message
$wgEmailCaptureAutoResponse['body-msg'] = 'articlefeedbackv5-emailcapture-response-body';

/**
 * How many feedback posts to display initially.
 *
 * @var int
 */
$wgArticleFeedbackv5InitialFeedbackPostCountToDisplay = 50;

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
	'version' => '1.5.0',
	'descriptionmsg' => 'articlefeedbackv5-desc',
	'url' => '//www.mediawiki.org/wiki/Extension:ArticleFeedbackv5'
);

// Autoloading
$dir = dirname( __FILE__ ) . '/';
$wgAutoloadClasses['ApiArticleFeedbackv5Utils']         = $dir . 'api/ApiArticleFeedbackv5Utils.php';
$wgAutoloadClasses['ApiArticleFeedbackv5']              = $dir . 'api/ApiArticleFeedbackv5.php';
$wgAutoloadClasses['ApiViewRatingsArticleFeedbackv5']   = $dir . 'api/ApiViewRatingsArticleFeedbackv5.php';
$wgAutoloadClasses['ApiViewFeedbackArticleFeedbackv5']  = $dir . 'api/ApiViewFeedbackArticleFeedbackv5.php';
$wgAutoloadClasses['ApiFlagFeedbackArticleFeedbackv5']  = $dir . 'api/ApiFlagFeedbackArticleFeedbackv5.php';
$wgAutoloadClasses['ApiViewActivityArticleFeedbackv5']  = $dir . 'api/ApiViewActivityArticleFeedbackv5.php';
$wgAutoloadClasses['ArticleFeedbackv5Hooks']            = $dir . 'ArticleFeedbackv5.hooks.php';
$wgAutoloadClasses['ArticleFeedbackv5Permissions']      = $dir . 'ArticleFeedbackv5.permissions.php';
$wgAutoloadClasses['ArticleFeedbackv5Log']              = $dir . 'ArticleFeedbackv5.log.php';
$wgAutoloadClasses['ArticleFeedbackv5LogFormatter']     = $dir . 'ArticleFeedbackv5.log.php';
$wgAutoloadClasses['ArticleFeedbackv5Fetch']            = $dir . 'ArticleFeedbackv5.fetch.php';
$wgAutoloadClasses['ArticleFeedbackv5Flagging']         = $dir . 'ArticleFeedbackv5.flagging.php';
$wgAutoloadClasses['ArticleFeedbackv5MailerJob']        = $dir . 'ArticleFeedbackv5MailerJob.php';
$wgAutoloadClasses['ArticleFeedbackv5Render']           = $dir . 'ArticleFeedbackv5.render.php';
$wgAutoloadClasses['SpecialArticleFeedbackv5']          = $dir . 'SpecialArticleFeedbackv5.php';
$wgAutoloadClasses['SpecialArticleFeedbackv5Watchlist'] = $dir . 'SpecialArticleFeedbackv5Watchlist.php';
$wgExtensionMessagesFiles['ArticleFeedbackv5']          = $dir . 'ArticleFeedbackv5.i18n.php';
$wgExtensionMessagesFiles['ArticleFeedbackv5Alias']     = $dir . 'ArticleFeedbackv5.alias.php';

// Hooks
$wgHooks['LoadExtensionSchemaUpdates'][] = 'ArticleFeedbackv5Hooks::loadExtensionSchemaUpdates';
$wgHooks['BeforePageDisplay'][] = 'ArticleFeedbackv5Hooks::beforePageDisplay';
$wgHooks['ResourceLoaderRegisterModules'][] = 'ArticleFeedbackv5Hooks::resourceLoaderRegisterModules';
$wgHooks['ResourceLoaderGetConfigVars'][] = 'ArticleFeedbackv5Hooks::resourceLoaderGetConfigVars';
$wgHooks['MakeGlobalVariablesScript'][] = 'ArticleFeedbackv5Hooks::makeGlobalVariablesScript';
$wgHooks['GetPreferences'][] = 'ArticleFeedbackv5Hooks::getPreferences';
$wgHooks['EditPage::showEditForm:fields'][] = 'ArticleFeedbackv5Hooks::pushTrackingFieldsToEdit';
$wgHooks['EditPage::attemptSave'][] = 'ArticleFeedbackv5Hooks::trackEditAttempt';
$wgHooks['ArticleSaveComplete'][] = 'ArticleFeedbackv5Hooks::trackEditSuccess';
$wgHooks['ContribsPager::reallyDoQuery'][] = 'ArticleFeedbackv5Hooks::contributionsData';
$wgHooks['ContributionsLineEnding'][] = 'ArticleFeedbackv5Hooks::contributionsLineEnding';
$wgHooks['ProtectionForm::buildForm'][] = 'ArticleFeedbackv5Hooks::onProtectionForm';
$wgHooks['ProtectionForm::save'][] = 'ArticleFeedbackv5Hooks::onProtectionSave';

// API Registration
$wgAPIListModules['articlefeedbackv5-view-ratings']  = 'ApiViewRatingsArticleFeedbackv5';
$wgAPIListModules['articlefeedbackv5-view-feedback'] = 'ApiViewFeedbackArticleFeedbackv5';
$wgAPIListModules['articlefeedbackv5-view-activity'] = 'ApiViewActivityArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5-flag-feedback']     = 'ApiFlagFeedbackArticleFeedbackv5';
$wgAPIModules['articlefeedbackv5']                   = 'ApiArticleFeedbackv5';

// Special Page
$wgSpecialPages['ArticleFeedbackv5'] = 'SpecialArticleFeedbackv5';
$wgSpecialPages['ArticleFeedbackv5Watchlist'] = 'SpecialArticleFeedbackv5Watchlist';
$wgSpecialPageGroups['ArticleFeedbackv5'] = 'other';

$wgAvailableRights[] = 'aft-reader';
$wgAvailableRights[] = 'aft-member';
$wgAvailableRights[] = 'aft-editor';
$wgAvailableRights[] = 'aft-monitor';
$wgAvailableRights[] = 'aft-administrator';
$wgAvailableRights[] = 'aft-oversighter';

// Jobs
$wgJobClasses['ArticleFeedbackv5MailerJob'] = 'ArticleFeedbackv5MailerJob';

// Logging
$wgLogTypes[] = 'articlefeedbackv5';

// register activity log formatter hooks
foreach ( array( 'oversight', 'unoversight', 'decline', 'request', 'unrequest' ) as $t) {
	$wgLogActionsHandlers["suppress/$t"] = 'ArticleFeedbackv5LogFormatter';
}
foreach ( array( 'hide', 'unhide', 'flag', 'unflag', 'autoflag', 'autohide', 'feature', 'unfeature', 'resolve', 'unresolve', 'helpful', 'unhelpful', 'undo-helpful', 'undo-unhelpful', 'clear-flags' ) as $t) {
	$wgLogActionsHandlers["articlefeedbackv5/$t"] = 'ArticleFeedbackv5LogFormatter';
}

// Add a custom filter group for AbuseFilter
if ( $wgArticleFeedbackv5AbuseFilterGroup != 'default' ) {
	$wgAbuseFilterValidGroups[] = $wgArticleFeedbackv5AbuseFilterGroup;
}

// Add custom action handlers for AbuseFilter
$wgAbuseFilterAvailableActions[] = 'aftv5flagabuse';
$wgAbuseFilterAvailableActions[] = 'aftv5hide';
$wgAbuseFilterAvailableActions[] = 'aftv5requestoversight';

// Permissions: 6 levels of permissions are built into ArticleFeedbackv5: reader, member, editor,
// monitor, administrator, oversighter. The default (below-configured) permissions scheme can be seen at
// http://www.mediawiki.org/wiki/Article_feedback/Version_5/Feature_Requirements#Access_and_permissions
foreach ( array( '*', 'user', 'confirmed', 'autoconfirmed', 'rollbacker', 'reviewer', 'sysop', 'oversight' ) as $group ) {
	$wgGroupPermissions[$group]['aft-reader'] = true;
}
foreach ( array( 'user', 'confirmed', 'autoconfirmed', 'rollbacker', 'reviewer', 'sysop', 'oversight' ) as $group ) {
	$wgGroupPermissions[$group]['aft-member'] = true;
}
foreach ( array( 'confirmed', 'autoconfirmed', 'rollbacker', 'reviewer', 'sysop', 'oversight' ) as $group ) {
	$wgGroupPermissions[$group]['aft-editor'] = true;
}
foreach ( array( 'rollbacker', 'reviewer', 'sysop', 'oversight' ) as $group ) {
	$wgGroupPermissions[$group]['aft-monitor'] = true;
}
foreach ( array( 'sysop', 'oversight' ) as $group ) {
	$wgGroupPermissions[$group]['aft-administrator'] = true;
}
foreach ( array( 'oversight' ) as $group ) {
	$wgGroupPermissions[$group]['aft-oversighter'] = true;
}
