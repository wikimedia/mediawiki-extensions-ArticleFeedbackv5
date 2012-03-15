<?php
/**
 * Stupidly simple helper script will read in all en entries
 * from i18n file and array diff it against the entries in qqq
 *
 * Then it will take those key names and echo them to stdout 1 by 1
 */

include dirname(__FILE__) . '/../ArticleFeedbackv5.i18n.php';

foreach( array_keys($messages['en']) as $needle ) {
	if( !array_key_exists($needle, $messages['qqq']) ) {
		echo "'$needle' => '',\n";
	}
}
