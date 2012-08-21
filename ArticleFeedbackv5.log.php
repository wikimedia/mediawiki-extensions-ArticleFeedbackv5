<?php
/**
 * This class formats all articlefeedbackv5log entries.
 *
 * @package    ArticleFeedback
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */
class ArticleFeedbackv5Log {
	/**
	 * Adds an activity item to the global log under the articlefeedbackv5
	 *
	 * @param $type      string the type of activity we'll be logging
	 * @param $pageId    int    the id of the page so we can look it up
	 * @param $itemId    int    the id of the feedback item, used to build permalinks
	 * @param $notes     string any notes that were stored with the activity
	 * @param $doer      User   user who did the action
	 * @param $params    array  of parameters that can be passed into the msg thing - used for "perpetrator" for log entry
	 */
	public static function logActivity( $type, $pageId, $itemId, $notes, $doer = null, array $params = array() ) {
		wfProfileIn( __METHOD__ );

		global $wgLogActionsHandlers, $wgArticleFeedbackv5MaxActivityNoteLength, $wgLang;

		// set the type of feedback - some feedback must go to the more hidden suppression log
		if ( isset( $wgLogActionsHandlers["suppress/$type"] ) ) {
			$logType = 'suppress';
			$increment = 'af_suppress_count';
		} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$type"] ) ) {
			$logType = 'articlefeedbackv5';
			$increment = 'af_activity_count';
		} else {
			wfProfileOut( __METHOD__ );
			return;
		}

		// fetch title of the page the feedback was given for: Special:ArticleFeedbackv5/<pagename>/<feedbackid>
		$pageTitle = Title::newFromID( $pageId );
		if ( !$pageTitle ) {
			wfProfileOut( __METHOD__ );
			return;
		}
		$target = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $pageTitle->getDBKey() . "/$itemId" );

		// truncate comment
		$note = $wgLang->truncate( $notes, $wgArticleFeedbackv5MaxActivityNoteLength );

		// add page id & feedback id to params
		$params['4::feedbackId'] = (int) $itemId;
		$params['5::pageId'] = (int) $pageId;

		// insert logging entry
		$logEntry = new ManualLogEntry( $logType, $type );
		$logEntry->setTarget( $target );
		$logEntry->setPerformer( $doer );
		$logEntry->setParameters( $params );
		$logEntry->setComment( $note);
		$logEntry->publish( $logEntry->insert() );

		// denormalized db: update log count in AFT table
		$dbw = wfGetDB( DB_MASTER );
		$dbw->begin();
		$dbw->update(
			'aft_article_feedback',
			array( $increment .' = ' .$increment . ' + 1' ),
			array(
				'af_id' => $itemId
			),
			__METHOD__
		);
		$dbw->commit();

		wfProfileOut( __METHOD__ );
	}
}

/**
 * This class formats all articlefeedbackv5log entries.
 *
 * @package    ArticleFeedback
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */
class ArticleFeedbackv5LogFormatter extends LogFormatter {
	/**
	 * Formats an activity log entry
	 *
	 * @return string           the log entry
	 */
	protected function getActionMessage() {
		global $wgLang, $wgContLang;

		$action          = $this->entry->getSubtype();
		$target          = $this->entry->getTarget();
		$skin            = $this->plaintext ? null : $this->context->getSkin();
		$parameters      = $this->entry->getParameters();

		// this should not happen, but might occur for legacy entries
		if ( !isset( $parameters['4::feedbackId'] ) || !isset( $parameters['5::pageId'] ) ) {
			return '';
		}

		// @todo: these 2 lines will spoof a new url which will lead to the central feedback page with the
		// selected post on top; this is due to a couple of oversighters reporting issues with the permalink page.
		// once these issues have been solved, these lines should be removed
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5' );
		$target = Title::makeTitle( NS_SPECIAL, $centralPageName, $parameters['4::feedbackId'] )->getFullText();

		$language = $skin === null ? $wgContLang : $wgLang;
		$action = wfMessage( "logentry-articlefeedbackv5-$action" )
			->params( array(
				Message::rawParam( $this->getPerformerElement() ),
				$this->entry->getPerformer()->getId(),
				$target,
				$parameters['4::feedbackId'],
				Title::newFromID( $parameters['5::pageId'] )
			) )
			->inLanguage( $language )
			->parse();

		return $action;
	}
}
