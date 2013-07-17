<?php

class EchoArticleFeedbackv5Formatter extends EchoBasicFormatter {
	/**
	 * @param EchoEvent $event
	 * @param mixed $param
	 * @param Message $message
	 * @param User $user
	 */
	protected function processParam( $event, $param, $message, $user ) {
		switch ( $param ) {
			case 'aft_permalink':
				$feedbackId = $event->getExtraParam( 'aft_id' );
				$feedbackPage = $event->getExtraParam( 'aft_page' );

				// build url to permalink and special page
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

			default:
				parent::processParam( $event, $param, $message, $user );
				break;
		}
	}

	/**
	 * Formats the payload of a notification, child method overwriting this method should
	 * always call this method in default case so they can use the payload defined in this
	 * function as well
	 *
	 * @param string $payload
	 * @param EchoEvent $event
	 * @param User $user
	 * @return string
	 */
	protected function formatPayload( $payload, $event, $user ) {
		switch ( $payload ) {
			case 'aft_comment':
				return $event->getExtraParam( 'aft_comment' );
				break;
			case 'aft_moderation_notes':
				return $event->getExtraParam( 'aft_moderation_notes' );
				break;
			default:
				return parent::formatPayload( $payload, $event, $user );
		}
	}
}
