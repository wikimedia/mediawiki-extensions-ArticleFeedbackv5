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
		$parameters		 = $this->extractParameters();
		$feedbackId		 = $parameters[3];

		// link to the page the feedback was given for
		$pagelink = Message::rawParam( Linker::link( $title ) );

		// to build our permalink, use the feedback entry key + the page name (isn't page name a title? but title is an object? confusing)
		$pageName = $title->getDBKey();
		$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$pageName/$feedbackId" );

		// @todo: these 2 lines will spoof a new url which will lead to the central feedback page with the
		// selected post on top; this is due to a couple of oversighters reporting issues with the permalink page.
		// once these issues have been solved, these lines should be removed
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5' );
		$title = Title::makeTitle( NS_SPECIAL, $centralPageName, "$feedbackId" );

		$fbtext = wfMessage( 'articlefeedbackv5-log-feedback-linktext', $feedbackId )->escaped();
		$fblink = Message::rawParam( Linker::link( $title, $fbtext ) );

		global $wgLang, $wgContLang;
		$language = $skin === null ? $wgContLang : $wgLang;
		$action = wfMessage( "articlefeedbackv5-log-$action" )
			->params( array( $fblink, $pagelink ) )
			->inLanguage( $language )->text();

		$performer = $this->getPerformerElement();
		if ( !$this->irctext ) {
			$action = $performer . $this->msg( 'word-separator' )->text() . $action;
		}

		return $action;
	}
}
