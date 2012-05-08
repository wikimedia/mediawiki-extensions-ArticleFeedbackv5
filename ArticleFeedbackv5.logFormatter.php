<?php
/**
 * ArticleFeedbackv5LogFormatter class
 *
 * @package    ArticleFeedback
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * This class formats all articlefeedbackv5log entries.
 *
 * @package    ArticleFeedback
 */
class ArticleFeedbackv5LogFormatter extends LogFormatter {

	/**
	 * Formats an activity log entry
	 *
	 * @param  $type            string the log type
	 * @param  $action          string the action (usually, but not always, the flag)
	 * @param  $title           Title  the title
	 * @param  $skin            Skin   the skin
	 * @param  $params          array  any extra params
	 * @param  $filterWikilinks bool   whether to filter links
	 * @return string           the log entry
	 */
	protected function getActionMessage() {
		$entry           = $this->entry;
		$type            = $entry->getType();
		$action          = $entry->getSubtype();
		$title           = $entry->getTarget();
		$skin            = $this->plaintext ? null : $this->context->getSkin();
// 		$params          = (array)$entry->getParameters()
// 		$filterWikilinks = !$this->plaintext;

		if ( preg_match( '|^ArticleFeedbackv5/(.*)/(\d+)$|', $title->getDBKey(), $matches ) ) {
			$page = Title::newFromDBKey( $matches[1] );
			$fbid = $matches[2];
		} else {
			$fbid = '?';
		}

		$fbtext = wfMessage( 'articlefeedbackv5-log-feedback-linktext',
				$fbid )->escaped();
		$fblink = Message::rawParam( Linker::makeLinkObj( $title, $fbtext ) );

		if ( $page ) {
			$pagelink = Message::rawParam( Linker::makeLinkObj( $page ) );
		} else {
			$pagelink = '?';
		}

		global $wgLang, $wgContLang;
		$language = $skin === null ? $wgContLang : $wgLang;
		$action = wfMessage( "$type-log-$action" )->params(
				array( $fblink, $pagelink ) )->inLanguage( $language )->text();

		$performer = $this->getPerformerElement();
		if ( !$this->irctext ) {
			$action = $performer .  $this->msg( 'word-separator' )->text() . $action;
		}

		return $action;
	}

}
