<?php

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'ArticleFeedbackv5' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['ArticleFeedbackv5'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['ArticleFeedbackv5Alias'] = __DIR__ . '/ArticleFeedbackv5.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for ArticleFeedbackv5 extension. Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the ArticleFeedbackv5 extension requires MediaWiki 1.25+' );
}