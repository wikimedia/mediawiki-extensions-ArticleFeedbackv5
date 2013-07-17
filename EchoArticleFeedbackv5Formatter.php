<?php

class EchoArticleFeedbackv5Formatter extends EchoBasicFormatter {
	/**
	 * {@inheritdoc}
	 */
	protected function processParam( $event, $param, $message, $user ) {
		switch ( $param ) {
			case 'aft_permalink':
				$feedbackId = $event->getExtraParam( 'aft_id' );
				$feedbackPage = $event->getExtraParam( 'aft_page' );

				// build link to this specific feedback entry
				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$permalink = $this->buildLinkParam(
						$event,
						SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedbackId ),
						array(
							'linkText' => wfMessage( 'notification-feedback-permalink-text' )->text(),
						)
					);
					$message->params( $permalink );
				} else {
					$message->params( '' );
				}
				break;

			case 'aft_comment':
				$message->params( $event->getExtraParam( 'aft_comment' ) );
				break;

			case 'aft_moderation_flag':
				$flag = $event->getExtraParam( 'aft_moderation_flag' );
				$status = wfMessage( 'notification-feedback-moderation-flag-' . $flag )->text();

				$message->params( $status );
				break;

			// agent-other-display, agent-other-count & others
			default:
				parent::processParam( $event, $param, $message, $user );
				break;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function formatPayload( $payload, $event, $user ) {
		switch ( $payload ) {
			case 'aft_comment':
				return $event->getExtraParam( 'aft_comment' );
				break;

			case 'aft_moderation_flag':
				/*
				 * I'd like to include moderation notes as well, but for most
				 * actions, moderation notes are optional and only added after
				 * moderating feedback.
				 */

				$flag = $event->getExtraParam( 'aft_moderation_flag' );
				$status = wfMessage( 'notification-feedback-moderation-flag-' . $flag )->text();
				return ucfirst( $status );
				break;

			default:
				return parent::formatPayload( $payload, $event, $user );
				break;
		}
	}
}
