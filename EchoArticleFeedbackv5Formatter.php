<?php

/**
 * Formatter for feedback-moderated
 */
class EchoArticleFeedbackv5Formatter extends EchoBasicFormatter {
	/**
	 * {@inheritdoc}
	 */
	protected function processParam( $event, $param, $message, $user ) {
		switch ( $param ) {
			// text-form permalink to AFTv5 entry (Special:ArticleFeedbackv5/<PageTitle>/<FeedbackId>)
			case 'aft-permalink':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedbackId );

					/*
					 * We can't use this textual representation to build a link
					 * in the message (like [[$1]]), because both html and plain
					 * text email will use the same message (and we don't want
					 * <a> elements in plain text emails.
					 * So, unlike all other outputformats, in htmlemail, this
					 * parameter will be an <a> element instead of title text.
					 */
					if ( $this->outputFormat === 'htmlemail' ) {
						$props['attribs'] = array( 'style' => $this->getHTMLLinkStyle() );

						$link = $this->buildLinkParam( $title, $props );
						$message->params( $link );
					} else {
						$message->params( $this->formatTitle( $title ) );
					}
				} else {
					$message->params( '' );
				}
				break;

			// link-form permalink to AFTv5 entry (Special:ArticleFeedbackv5/<PageTitle>/<FeedbackId>)
			case 'aft-permalink-link':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedbackId );

					$props = array();
					if ( $this->outputFormat === 'htmlemail' ) {
						$props['attribs'] = array( 'style' => $this->getHTMLLinkStyle() );
					}

					$link = $this->buildLinkParam( $title, $props );
					$message->params( $link );
				} else {
					$message->params( '' );
				}
				break;

			// i18n'ed link-form permalink to AFTv5 entry (Special:ArticleFeedbackv5/<PageTitle>/<FeedbackId>)
			case 'aft-permalink-i18n-link':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedbackId );

					$props['linkText'] = wfMessage( 'articlefeedbackv5-notification-link-text-view-feedback' )->text();
					if ( $this->outputFormat === 'htmlemail' ) {
						$props['attribs'] = array( 'style' => $this->getHTMLLinkStyle() );
					}

					$link = $this->buildLinkParam( $title, $props );
					$message->params( $link );
				} else {
					$message->params( '' );
				}
				break;

			// text-form link to all feedbacks per page (Special:ArticleFeedbackv5/<PageTitle>#<FeedbackId>)
			case 'aft-page':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey(), $feedbackId );

					/*
					 * We can't use this textual representation to build a link
					 * in the message (like [[$1]]), because both html and plain
					 * text email will use the same message (and we don't want
					 * <a> elements in plain text emails.
					 * So, unlike all other outputformats, in htmlemail, this
					 * parameter will be an <a> element instead of title text.
					 */
					if ( $this->outputFormat === 'htmlemail' ) {
						$props['attribs'] = array( 'style' => $this->getHTMLLinkStyle() );

						$link = $this->buildLinkParam( $title, $props );
						$message->params( $link );
					} else {
						$message->params( $this->formatTitle( $title ) );
					}
				} else {
					$message->params( '' );
				}
				break;

			// link-form link to all feedbacks per page (Special:ArticleFeedbackv5/<PageTitle>#<FeedbackId>)
			case 'aft-page-link':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey(), $feedbackId );

					$props = array();
					if ( $this->outputFormat === 'htmlemail' ) {
						$props['attribs'] = array( 'style' => $this->getHTMLLinkStyle() );
					}

					$link = $this->buildLinkParam( $title, $props );
					$message->params( $link );
				} else {
					$message->params( '' );
				}
				break;

			// i18n'ed link-form link to all feedbacks per page (Special:ArticleFeedbackv5/<PageTitle>#<FeedbackId>)
			case 'aft-page-i18n-link':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$title = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey(), $feedbackId );

					$props['linkText'] = wfMessage( 'articlefeedbackv5-notification-link-text-view-feedback' )->text();
					if ( $this->outputFormat === 'htmlemail' ) {
						$props['attribs'] = array( 'style' => $this->getHTMLLinkStyle() );
					}

					$link = $this->buildLinkParam( $title, $props );
					$message->params( $link );
				} else {
					$message->params( '' );
				}
				break;

			case 'aft-comment':
				global $wgLang;

				$comment = $event->getExtraParam( 'aft-comment' );
				$comment = $wgLang->truncate( $comment, 250 );

				$message->params( $comment );
				break;

			case 'aft-moderation-flag':
				$flag = $event->getExtraParam( 'aft-moderation-flag' );
				$status = wfMessage( 'articlefeedbackv5-notification-feedback-moderation-flag-' . $flag )->text();

				$message->params( $status );
				break;

			// default echo params like agent-other-display & agent-other-count
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
			case 'aft-comment':
				global $wgLang;

				$comment = $event->getExtraParam( 'aft-comment' );
				$comment = $wgLang->truncate( $comment, 250 );

				return $comment;
				break;

			case 'aft-moderation-flag':
				/*
				 * I'd like to include moderation notes as well, but for most
				 * actions, moderation notes are optional and only added after
				 * moderating feedback.
				 */

				$flag = $event->getExtraParam( 'aft-moderation-flag' );
				$status = wfMessage( 'articlefeedbackv5-notification-feedback-moderation-flag-' . $flag )->text();

				return ucfirst( $status );
				break;

			default:
				return parent::formatPayload( $payload, $event, $user );
				break;
		}
	}

	/**
	 * Helper function for getLink()
	 *
	 * @param EchoEvent $event
	 * @param User $user The user receiving the notification
	 * @param String $destination The destination type for the link
	 * @return Array including target and query parameters
	 */
	protected function getLinkParams( $event, $user, $destination ) {
		$target = null;
		$query = array();

		switch ( $destination ) {
			case 'aft-permalink':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$target = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedbackId );
				}
				break;

			case 'aft-page':
				$feedbackId = $event->getExtraParam( 'aft-id' );
				$feedbackPage = $event->getExtraParam( 'aft-page' );

				$page = Title::newFromID( $feedbackPage );
				if ( $page ) {
					$target = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey(), $feedbackId );
				}
				break;

			default:
				return parent::getLinkParams( $event, $user, $destination );
				break;
		}

		return array( $target, $query );
	}
}

/**
 * While most of the formatter code for both notifications is shared, both
 * follow different bundle rules. One is per-page, the other per-feedback entry.
 * The per-feedback entry can use BasicFormatter's default agent-based bundle
 * params; page-paged needs to know the amount of moderated feedback entries,
 * not the amount of agents that moderated the feedback entries.
 */
class EchoArticleFeedbackv5FormatterWatch extends EchoArticleFeedbackv5Formatter {
	/**
	 * {@inheritdoc}
	 */
	protected function processParam( $event, $param, $message, $user ) {
		switch ( $param ) {
			// pretty-formatted amount of AFTv5 entries
			case 'aft-other-display':
				global $wgEchoMaxNotificationCount;

				if ( $this->bundleData['aft-other-count'] > $wgEchoMaxNotificationCount ) {
					$message->params(
						wfMessage( 'echo-notification-count' )
							->inLanguage( $user->getOption( 'language' ) )
							->params( $wgEchoMaxNotificationCount )
							->text()
					);
				} else {
					$message->params( $this->bundleData['aft-other-count'] );
				}
				break;

			// the number used for plural support
			case 'aft-other-count':
				$message->params( $this->bundleData['aft-other-count'] );
				break;

			// all other params
			default:
				parent::processParam( $event, $param, $message, $user );
				break;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function generateBundleData( $event, $user, $type ) {
		global $wgEchoMaxNotificationCount;

		$data = $this->getRawBundleData( $event, $user, $type );

		if ( !$data ) {
			return;
		}

		// initialize with 1 for the current event
		$count = 1;
		$entries = array( $event->getExtraParam( 'aft-id' ) );
		foreach ( $data as $row ) {
			$extra = unserialize( $row->event_extra );

			if ( isset( $extra['aft-id'] ) && !in_array( $extra['aft-id'], $entries ) ) {
				$count++;
				$entries[] = $extra['aft-id'];
			}

			if ( $count > $wgEchoMaxNotificationCount + 1 ) {
				break;
			}
		}

		$this->bundleData['aft-other-count'] = $count - 1;
		if ( $count > 1 ) {
			$this->bundleData['use-bundle'] = true;
		}
	}
}
