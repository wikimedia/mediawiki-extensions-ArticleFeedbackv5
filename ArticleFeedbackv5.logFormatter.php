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
	 * @return string           the log entry
	 */
	protected function getActionMessage() {
		$entry           = $this->entry;
		$action          = $entry->getSubtype();
		$title           = $entry->getTarget();
		$skin            = $this->plaintext ? null : $this->context->getSkin();

		if ( preg_match( '|^ArticleFeedbackv5/(.*)/(\d+)$|', $title->getDBKey(), $matches ) ) {
			$page = Title::newFromDBKey( $matches[1] );
			$fbid = $matches[2];
		} else {
			$fbid = '?';
		}

		if ( $page ) {
			$pagelink = Message::rawParam( Linker::link( $page ) );
		} else {
			$pagelink = '?';
		}

		// @todo: these 2 lines will spoof a new url which will lead to the central feedback page with the
		// selected post on top; this is due to a couple of oversighters reporting issues with the permalink page.
		// once these issues have been solved, these lines should be removed
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5' );
		$title = Title::makeTitle( NS_SPECIAL, $centralPageName, "$fbid" );

		$fbtext = wfMessage( 'articlefeedbackv5-log-feedback-linktext',
				$fbid )->escaped();
		$fblink = Message::rawParam( Linker::link( $title, $fbtext ) );

		global $wgLang, $wgContLang;
		$language = $skin === null ? $wgContLang : $wgLang;
		$action = wfMessage( "articlefeedbackv5-log-$action" )->params(
				array( $fblink, $pagelink ) )->inLanguage( $language )->text();

		$performer = $this->getPerformerElement();
		if ( !$this->irctext ) {
			$action = $performer . $this->msg( 'word-separator' )->text() . $action;
		}

		return $action;
	}

}
