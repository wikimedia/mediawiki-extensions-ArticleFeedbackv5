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
	 * @return int       the id of the newly inserted log entry
	 */
	public static function logActivity( $type, $pageId, $itemId, $notes, $doer, array $params = array() ) {
		wfProfileIn( __METHOD__ );

		global $wgLogActionsHandlers, $wgArticleFeedbackv5MaxActivityNoteLength, $wgLang;

		// set the type of feedback - some feedback must go to the more hidden suppression log
		if ( isset( $wgLogActionsHandlers["suppress/$type"] ) ) {
			$logType = 'suppress';
		} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$type"] ) ) {
			$logType = 'articlefeedbackv5';
		} else {
			wfProfileOut( __METHOD__ );
			return null;
		}

		// fetch title of the page the feedback was given for: Special:ArticleFeedbackv5/<pagename>/<feedbackid>
		$pageTitle = Title::newFromID( $pageId );
		if ( !$pageTitle ) {
			wfProfileOut( __METHOD__ );
			return null;
		}
		$target = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $pageTitle->getDBKey() . "/$itemId" );

		// if no doer specified, use default AFT user
		if ( !( $doer instanceof User ) ) {
			$defaultUser = wfMessage( 'articlefeedbackv5-default-user' )->text();
			$doer = User::newFromName( $defaultUser );
		}

		// truncate comment
		$note = $wgLang->truncate( $notes, $wgArticleFeedbackv5MaxActivityNoteLength );

		// add page id & feedback id to params
		$params['feedbackId'] = (string) $itemId;
		$params['pageId'] = (int) $pageId;

		// insert logging entry
		$logEntry = new ManualLogEntry( $logType, $type );
		$logEntry->setTarget( $target );
		$logEntry->setPerformer( $doer );
		$logEntry->setParameters( $params );
		$logEntry->setComment( $note );
		$logId = $logEntry->insert();
		$logEntry->publish( $logId );

		// update log count in cache
		ArticleFeedbackv5Activity::incrementActivityCount( $itemId, $type );

		wfProfileOut( __METHOD__ );

		return $logId;
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
		if ( !isset( $parameters['feedbackId'] ) || !isset( $parameters['pageId'] ) ) {
			return '';
		}

		// this could happen when a page has since been removed
		$page = Title::newFromID( $parameters['pageId'] );
		if ( !$page ) {
			return '';
		}

		$language = $skin === null ? $wgContLang : $wgLang;
		$action = wfMessage( "logentry-articlefeedbackv5-$action" )
			->params( array(
				Message::rawParam( $this->getPerformerElement() ),
				$this->entry->getPerformer()->getId(),
				$target,
				$parameters['feedbackId'],
				$page
			) )
			->inLanguage( $language )
			->parse();

		return $action;
	}

	/**
	 * The native LogFormatter::getActionText provides no clean way of
	 * handling the AFT action text in a plain text format (e.g. as
	 * used by CheckUser)
	 *
	 * @return string
	 */
	public function getActionText() {
		$text = $this->getActionMessage();
		return $this->plaintext ? strip_tags( $text ) : $text;
	}
}
