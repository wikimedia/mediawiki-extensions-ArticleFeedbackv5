<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		// Our custom directories in addition to includes/*, relative to _repo root path_
		'api',
		'data',
		'maintenance',
		// Extensions that we support but don't really depend on,
		// but phan can't tell the difference anyway...
		'../../extensions/AbuseFilter',
		'../../extensions/Echo',
		'../../extensions/SpamBlacklist',
		'../../extensions/SpamRegex',
	]
);

$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		'data/sample',
		'data/tests',
		'../../extensions/AbuseFilter',
		'../../extensions/Echo',
		'../../extensions/SpamBlacklist',
		'../../extensions/SpamRegex',
	]
);

return $cfg;
