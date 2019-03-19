<?php

use MediaWiki\MediaWikiServices;

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
	 * @param string $type The type of activity we'll be logging
	 * @param int $pageId The id of the page so we can look it up
	 * @param int $itemId The id of the feedback item, used to build permalinks
	 * @param string $notes Any notes that were stored with the activity
	 * @param User $doer User who did the action
	 * @param array $params Array of parameters that can be passed into the msg thing - used for "perpetrator" for log entry
	 * @return int The id of the newly inserted log entry
	 */
	public static function log( $type, $pageId, $itemId, $notes, $doer, array $params = [] ) {
		global $wgLogActionsHandlers, $wgArticleFeedbackv5MaxActivityNoteLength, $wgLang;

		if ( isset( ArticleFeedbackv5Activity::$actions[$type]['log_type'] ) ) {
			// log type for actions (the more delicate actions should go to suppression log)
			$logType = ArticleFeedbackv5Activity::$actions[$type]['log_type'];
		} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$type"] ) ) {
			// other AFTv5-related log entry (e.g. "create")
			$logType = 'articlefeedbackv5';
		} else {
			return null;
		}

		// fetch title of the page the feedback was given for: Special:ArticleFeedbackv5/<pagename>/<feedbackid>
		$pageTitle = Title::newFromID( $pageId );
		if ( !$pageTitle ) {
			return null;
		}
		$target = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $pageTitle->getPrefixedDBkey() . "/$itemId" );

		// if no doer specified, use default AFT user
		if ( !( $doer instanceof User ) ) {
			$defaultUser = wfMessage( 'articlefeedbackv5-default-user' )->text();
			$doer = User::newFromName( $defaultUser );
			if ( !$doer ) {
				throw new MWException( "Default user '$defaultUser' does not exist." );
			}
		}

		// truncate comment
		$note = $wgLang->truncateForDatabase( $notes, $wgArticleFeedbackv5MaxActivityNoteLength );

		// add page id & feedback id to params
		$params['feedbackId'] = (string)$itemId;
		$params['pageId'] = (int)$pageId;

		// insert logging entry
		$logEntry = new ManualLogEntry( $logType, $type );
		$logEntry->setTarget( $target );
		$logEntry->setPerformer( $doer );
		$logEntry->setParameters( $params );
		$logEntry->setComment( $note );
		$logId = $logEntry->insert();
		$logEntry->publish( $logId );

		/**
		 * ManualLogEntry will have written to database. To make sure that subsequent
		 * reads are up-to-date, I'll set a flag to know that we've written data, so
		 * DB_MASTER will be queried.
		 */
		$wiki = false;
		ArticleFeedbackv5Utils::$written[$wiki] = true;

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
	 * @return string The log entry
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
			// Ideally we would build the page title from logging.log_title instead, as
			// it's a place which stores the human-readable page name, although for AFTv5
			// entries it is always in the form "ArticleFeedbackv5/<wiki page name>/<feedback ID>"
			// so we wouldn't be able use it simply as-is even if we had access to it.
			// The DB row for this log entry exists as $this->entry->row, but that
			// property is protected in core and there is no getRow() accessor or
			// anything, so we have to use getTarget(), which (hopefully) returns a
			// Title formed from the row's log_namespace and log_title.
			// Then we work our magic on this Title, first getting the
			// "ArticleFeedbackv5/<wiki page name>" part of "ArticleFeedbackv5/<wiki page name>/<feedback ID>"
			// and then extracting the deleted page's name from this.
			// @see https://phabricator.wikimedia.org/T167401
			$pageTitle = $this->entry->getTarget();
			if ( !$pageTitle instanceof Title ) {
				return '';
			}
			$pageTitle = $pageTitle->getText();
			$a = explode( '/', $pageTitle );
			array_shift( $a );
			if ( isset( $a[0] ) && $a[0] ) {
				$page = $a[0];
			} else {
				return '';
			}
		}

		// Give grep a chance to find the usages:
		// logentry-articlefeedbackv5-create, logentry-articlefeedbackv5-oversight, logentry-articlefeedbackv5-unoversight,
		// logentry-articlefeedbackv5-decline, logentry-articlefeedbackv5-request, logentry-articlefeedbackv5-unrequest,
		// logentry-articlefeedbackv5-flag, logentry-articlefeedbackv5-unflag, logentry-articlefeedbackv5-autoflag,
		// logentry-articlefeedbackv5-feature, logentry-articlefeedbackv5-unfeature, logentry-articlefeedbackv5-resolve,
		// logentry-articlefeedbackv5-unresolve, logentry-articlefeedbackv5-noaction, logentry-articlefeedbackv5-unnoaction,
		// logentry-articlefeedbackv5-inappropriate, logentry-articlefeedbackv5-uninappropriate, logentry-articlefeedbackv5-archive,
		// logentry-articlefeedbackv5-unarchive, logentry-articlefeedbackv5-hide, logentry-articlefeedbackv5-unhide,
		// logentry-articlefeedbackv5-autohide, logentry-articlefeedbackv5-helpful, logentry-articlefeedbackv5-unhelpful,
		// logentry-articlefeedbackv5-undo-helpful, logentry-articlefeedbackv5-undo-unhelpful, logentry-articlefeedbackv5-clear-flags
		$language = $skin === null ? $wgContLang : $wgLang;
		return wfMessage( "logentry-articlefeedbackv5-$action" )
			->params( [
				Message::rawParam( $this->getPerformerElement() ),
				$this->entry->getPerformer()->getId(),
				$target,
				ArticleFeedbackv5Utils::formatId( $parameters['feedbackId'] ),
				$page
			] )
			->inLanguage( $language )
			->parse();
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

/**
 * This class formats AFTv5 protection log entries.
 *
 * @package    ArticleFeedback
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */
class ArticleFeedbackv5ProtectionLogFormatter extends LogFormatter {
	/**
	 * @return array
	 */
	protected function getMessageParameters() {
		$params = parent::getMessageParameters();

		$articleId = $this->entry->getTarget()->getArticleID();
		$page = WikiPage::newFromID( $articleId );
		if ( $page ) {
			$parameters = $this->entry->getParameters();
			$permission = [ 'articlefeedbackv5' => $parameters['permission'] ];
			$expiry = [ 'articlefeedbackv5' => $parameters['expiry'] ];

			$params[] = $page->protectDescriptionLog( $permission, $expiry );
		}

		return $params;
	}

	/**
	 * Returns extra links that comes after the action text, like "revert", etc.
	 *
	 * @return string
	 */
	public function getActionLinks() {
		$links = [
			MediaWikiServices::getInstance()->getLinkRenderer()->makeLink(
				$this->entry->getTarget(),
				$this->msg( 'hist' )->text(),
				[],
				[
					'action' => 'history',
					'offset' => $this->entry->getTimestamp()
				]
			)
		];

		return $this->msg( 'parentheses' )->rawParams(
			$this->context->getLanguage()->pipeList( $links ) )->escaped();
	}
}
