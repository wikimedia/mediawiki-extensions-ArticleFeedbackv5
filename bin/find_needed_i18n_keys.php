<?php
/**
 * Stupidly simple helper script will read in all en entries
 * from i18n file and array diff it against the entries in qqq
 *
 * Then it will take those key names and echo them to stdout 1 by 1
 */

include dirname(__FILE__) . '/../ArticleFeedbackv5.i18n.php';

$en_keys = array_keys($messages['en']);
$qqq_keys = array_keys($messages['qqq']);

$needed_keys = array_diff($en_keys, $qqq_keys);

foreach($needed_keys as $name) {
    echo "$name\n";
}