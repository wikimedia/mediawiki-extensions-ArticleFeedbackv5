<?php
/**
 * ArticleFeedbackv5Render class
 *
 * @package    ArticleFeedback
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

/**
 * Handles rendering of feedback
 *
 * @package    ArticleFeedback
 */
class ArticleFeedbackv5Render {
	/**
	 * The user
	 *
	 * @var User
	 */
	private $user;

	/**
	 * Whether this is a permalink
	 *
	 * @var bool
	 */
	private $isPermalink;

	/**
	 * Whether this is on the central log
	 *
	 * @var bool
	 */
	private $isCentral;

	/**
	 * Whether this is a highlighted row
	 *
	 * @var bool
	 */
	private $isHighlighted;

	/**
	 * Constructor
	 *
	 * @param $user      User [optional] the current user
	 * @param $permalink bool [optional] whether this is a permalink?
	 * @param $central   bool [optional] whether this is on the central log?
	 * @param $highlight bool [optional] whether this is a highlighted row?
	 */
	public function __construct( $user = null, $permalink = false, $central = false, $highlight = false ) {
		if ( $user instanceof User ) {
			$this->user = $user;
		} else {
			global $wgUser;
			$this->user = $wgUser;
		}

		$this->setIsPermalink( $permalink );
		$this->setIsCentral( $central );
		$this->setIsHighlighted( $highlight );
	}

	/**
	 * Runs the fetch
	 *
	 * @param  $record ArticleFeedbackv5Model the record, with keys 0 + answers
	 * @return string  the rendered row
	 */
	public function run( $record ) {
		if ( !$record instanceof ArticleFeedbackv5Model ) {
			return '';
		}

		// Special cases: when the record is deleted/hidden, but the user
		// doesn't have permission to see it
		if ( ( $record->isOversighted() && !ArticleFeedbackv5Activity::canPerformAction( 'oversight', $this->user ) ) ||
			( $record->isHidden() && !ArticleFeedbackv5Activity::canPerformAction( 'hide', $this->user ) ) ) {
			// Called via permalink: show an empty gray mask
			if ( $this->isPermalink ) {
				return $this->emptyGrayMask( $record );
			} else {
				return '';
			}
		}

		// Build with the actual content of the feedback (header + comment)
		$content = $this->render( $record );

		// Build the footer
		$footer = $this->renderFooter( $record );

		// Build the toolbox
		$toolbox = $this->renderToolbox( $record );

		// Get the top class
		$topClass = 'articleFeedbackv5-feedback';
		if ( $record->isHidden() ) {
			$topClass .= ' articleFeedbackv5-feedback-hide';
		}
		if ( $record->isOversighted() ) {
			$topClass .= ' articleFeedbackv5-feedback-oversight';
		}
		if ( $record->isFeatured() ) {
			$topClass .= ' articleFeedbackv5-feedback-feature';
		}
		if ( $record->isResolved() ) {
			$topClass .= ' articleFeedbackv5-feedback-resolve';
		}
		if ( !$toolbox ) {
			$topClass .= ' articleFeedbackv5-comment-notoolbox';
		}
		if ( $this->isPermalink ) {
			$topClass .= ' articleFeedbackv5-feedback-permalink';
		}
		if ( $this->isHighlighted ) {
			$topClass .= ' articleFeedbackv5-feedback-highlighted';
		}

		// Get the class for the comment wrap
		$wrapClass = 'articleFeedbackv5-comment-wrap articleFeedbackv5-comment-' . $this->getMood( $record );

		// Permalink info
		$permalinkInfo = '';
		if ( $this->isPermalink ) {
			$permalinkInfo = $this->renderPermalinkInfo( $record );
		}

		return
			Html::rawElement(
				'div',
				array(
					'class' => $topClass,
					'data-id'   => $record->aft_id,
					'data-pageid'   => $record->aft_page
				),
				// {toolbox, e.g. feature, hide}
				$toolbox .
				// {gray mask, if applicable}
				$this->grayMask( $record ) .
				Html::rawElement( 'div', array( 'class' => 'articleFeedbackv5-comment-container' ),
					Html::rawElement( 'div', array( 'class' => $wrapClass ),
						// {feedback content}
						$content .
						// {footer links, e.g. helpful, abuse}
						$footer
					)
				)
			) .
			// {info section for permalinks}
			$permalinkInfo;
	}

	/**
	 * Gets whether this is a permalink
	 *
	 * @return bool whether this is a permalink
	 */
	public function getIsPermalink() {
		return $this->isPermalink;
	}

	/**
	 * Sets whether this is a permalink
	 *
	 * @param  $isPermalink bool whether this is a permalink
	 * @return bool         whether it passed validation and was set
	 */
	public function setIsPermalink( $isPermalink ) {
		$this->isPermalink = $isPermalink ? true : false;
		return true;
	}

	/**
	 * Gets whether this is on the central log
	 *
	 * @return bool whether this is on the central log
	 */
	public function getIsCentral() {
		return $this->isCentral;
	}

	/**
	 * Sets whether this is on the central log
	 *
	 * @param  $isCentral bool whether this is on the central log
	 * @return bool       whether it passed validation and was set
	 */
	public function setIsCentral( $isCentral ) {
		$this->isCentral = $isCentral ? true : false;
		return true;
	}

	/**
	 * Gets whether this is is a highlighted row
	 *
	 * @return bool whether this is is a highlighted row
	 */
	public function getIsHighlighted() {
		return $this->isHighlighted;
	}

	/**
	 * Sets whether this is is a highlighted row
	 *
	 * @param  $isHighlighted bool whether this is is a highlighted row
	 * @return bool       whether it passed validation and was set
	 */
	public function setIsHighlighted( $isHighlighted ) {
		$this->isHighlighted = $isHighlighted ? true : false;
		return true;
	}

	/**
	 * Sets a permission
	 *
	 * @param  $key   string the key
	 * @param  $value bool   whether that permission is on
	 * @return bool   whether it passed validation and was set
	 */
	public function setPermission( $key, $value ) {
		if ( !isset( $this->permissions[$key] ) ) {
			return false;
		}
		$this->permissions[$key] = $value ? true : false;
		return true;
	}

	/**
	 * Returns whether a permission is on
	 *
	 * @param  $key string the key
	 * @return bool whether the permission is on
	 */
	public function hasPermission( $key ) {
		if ( !isset( $this->permissions[$key] ) ) {
			return false;
		}
		return $this->permissions[$key];
	}

	/**
	 * Returns an empty gray mask
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string the empty gray mask
	 */
	private function emptyGrayMask( $record ) {
		// hide or oversight?
		if ( $record->isOversighted() ) {
			$class = 'oversight';
		} elseif ( $record->isHidden() ) {
			$class = 'hide';
		} else {
			return '';
		}

		return
			Html::rawElement(
				'div',
				array( 'class' => "articleFeedbackv5-feedback articleFeedbackv5-feedback-$class articleFeedbackv5-feedback-emptymask" ),
				$this->grayMask( $record, true ) .
				Html::element( 'div', array( 'class' => 'articleFeedbackv5-comment-wrap' ) )
			);
	}

	/**
	 * Returns a gray mask
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @param  $empty  bool  [optional] whether the mask is empty; defaults to
	 *                       false
	 * @return string the gray mask
	 */
	private function grayMask( $record, $empty = false ) {
		if ( $record->isOversighted() ) {
			$type = 'oversight';
		} elseif ( $record->isHidden() ) {
			$type = 'hide';
		} else {
			return '';
		}

		$viewLink = '';
		if ( !$empty ) {
			$viewLink =
				Html::rawElement(
					'span',
					array( 'class' => 'articleFeedbackv5-mask-view' ),
					Html::rawElement(
						'a',
						array(
							'href' => '#',
							'onclick' => 'return false;',
						),
						wfMessage( 'articlefeedbackv5-mask-view-contents' )->escaped()
					)
				);
		}

		$last = $this->getLastActivity( $record );
		if ( !$last ) {
			// if this happens, some data is corrupt
			return '';
		}
		return
			Html::rawElement(
				'div',
				array( 'class' => 'articleFeedbackv5-post-screen' ),
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-mask-text-wrapper' ),
					Html::rawElement(
						'span',
						array( 'class' => 'articleFeedbackv5-mask-text' ),
						Html::rawElement(
							'span',
							array( 'class' => 'articleFeedbackv5-mask-info' ),
							ArticleFeedbackv5Utils::renderMaskLine(
								$type,
								$record->aft_id,
								$last->log_user,
								$last->log_timestamp
							)
						) .
						$viewLink
					)
				)
			);
	}

	/**
	 * Returns the mood of the feedback
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the mood (positive or negative)
	 */
	public function getMood( $record ) {
		return $record->aft_rating ? 'positive' : 'negative';
	}

	/**
	 * Returns the feedback head and comment
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the rendered feedback info
	 */
	private function render( $record ) {
		if ( $this->isCentral ) {
			$msg = 'articlefeedbackv5-central-header-left-comment';
		} else {
			$mood = $this->getMood( $record );

			if ( $mood == 'positive' ) {
				$msg = 'articlefeedbackv5-form1-header-found';
			} elseif ( $mood == 'negative' ) {
				$msg = 'articlefeedbackv5-form1-header-not-found';
			}
		}

		return
			$this->feedbackHead( $msg, $record ) .
			$this->renderComment( $record );
	}

	/**
	 * Returns the feedback head
	 *
	 * @param  $message string   the message key describing the the nature of
	 *                           the feedback (e.g., "USER found what they were
	 *                           looking for")
	 * @param  $record  ArticleFeedbackv5 the record
	 * @return string   the rendered feedback head
	 */
	private function feedbackHead( $message, $record ) {
		$anonMessage = '';

		// User info
		if ( $record->aft_user == 0 ) {
			// This is an anonymous (IP) user

			$title = SpecialPage::getTitleFor( 'Contributions', $record->aft_user_text );

			if ( IP::isIPv4( $record->aft_user_text ) ) {
				// IPv4 - display the same way regular users are displayed

				// display name = visitor's ip
				$userName = Linker::link( $title, htmlspecialchars( $record->aft_user_text ) );
			} else {
				// not IPv4 - display IP on next line (since IPv6 is rather long, it'd break our display)

				// display name = "a reader" (without link to contributions)
				$userName = wfMessage( 'articlefeedbackv5-form-anon-username' )->escaped();

				// additional line to be printed with the IPv6 address (with link to contributions)
				$userLink = Linker::link( $title, htmlspecialchars( $record->aft_user_text ) );
				$anonMessage = wfMessage( 'articlefeedbackv5-form-anon-message' )->rawParams( $userLink )->escaped();
			}
		} else {
			// This is a logged in user

			// build link to user's page
			$title = Title::makeTitleSafe( NS_USER, $record->aft_user_text );

			// no user page = build link to user's contributions
			if ( !$title || !$title->exists() ) {
				$title = SpecialPage::getTitleFor( 'Contributions', $record->aft_user_text );
			}

			// display name = username
			$userName = Linker::link( $title, htmlspecialchars( $record->aft_user_text ) );
		}

		if ( $this->isCentral ) {
			$article = Title::newFromId( $record->aft_page );
			$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5', $article->getPrefixedDBkey() );
			$feedbackCentralPageTitle = Title::makeTitle( NS_SPECIAL, $centralPageName, "$record->aft_id" );

			$userMessage = wfMessage( $message, $record->aft_user_text )
				->rawParams( $userName, Linker::linkKnown( $article ) )
				->params( $feedbackCentralPageTitle->getFullText() )
				->parse();
		} else {
			$userMessage = wfMessage( $message, $record->aft_user_text )->rawParams( $userName )->escaped();
		}

		// build messages
		$userMessage = Html::rawElement( 'h3', array(), $userMessage );
		if ( $anonMessage ) {
			$anonMessage = Html::rawElement(
				'p',
				array( 'class' => 'articleFeedbackv5-comment-anon-message' ),
				$anonMessage
			);
		}

		return
			Html::rawElement(
				'div',
				array( 'class' => 'articleFeedbackv5-comment-head' ),
				$this->renderTagBlock( $record ) .
				$userMessage .
				$this->renderPermalinkTimestamp( $record ) .
				$anonMessage
			);
	}

	/**
	 * Returns the permalink/timestamp
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the rendered permalink/timestamp
	 */
	private function renderPermalinkTimestamp( $record ) {
		$id = $record->aft_id;
		$title = Title::newFromId( $record->aft_page )->getPrefixedDBkey();
		$timestamp = new MWTimestamp( $record->aft_timestamp );

		// link to permalink page
		$permalink = '';
		if ( !$this->isPermalink ) {
			$permalink =
				wfMessage( 'pipe-separator' )->escaped() .
				Html::rawElement(
					'span',
					array( 'class' => 'articleFeedbackv5-comment-details-link' ),
					Linker::link(
						SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$title/$id" ),
						wfMessage( 'articleFeedbackv5-details-link' )->escaped()
					)
				);
		}

		return
			Html::rawElement(
				'span',
				array( 'class' => 'articleFeedbackv5-comment-details' ),
				Html::rawElement(
					'span',
					array( 'class' => 'articleFeedbackv5-comment-details-date' ),
					$timestamp->getHumanTimestamp()->escaped()
				) .
				$permalink
			);
	}

	/**
	 * Returns the marked-up feedback comment
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the rendered comment
	 */
	private function renderComment( $record ) {
		global $wgLang;

		$id = $record->aft_id;
		$text = $record->aft_comment;
		$short = $this->isPermalink ? $text : $wgLang->truncate( $text, 250 );

		// If the short string is the same size as the original, no truncation
		// happened, so no controls are needed.  If it's longer, show the short
		// text, with the 'show more' control.
		$fullLengthToggle = '';
		if ( strlen( $short ) != strlen( $text ) ) {
			$title = Title::newFromID( $record->aft_page )->getPrefixedDBkey();

			$fullLengthToggle =
				Html::element(
					'span',
					array(
						'class' => 'articleFeedbackv5-comment-full',
						'id'    => "articleFeedbackv5-comment-full-$id"
					),
					$text
				) .
				Html::element(
					'a',
					array(
						'href'  => SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$title/$id" )->getLinkURL(),
						'class' => 'articleFeedbackv5-comment-toggle',
						'id'    => "articleFeedbackv5-comment-toggle-$id"
					),
					wfMessage( 'articlefeedbackv5-comment-more' )->text()
				);
		}

		return
			Html::rawElement(
				'blockquote',
				array(),
				Html::element( 'span',
					array(
						'class' => 'articleFeedbackv5-comment-short',
						'id'    => "articleFeedbackv5-comment-short-$id"
					),
					$short
				) .
				$fullLengthToggle
			);
	}

	/**
	 * Returns the footer links
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the rendered footer
	 */
	private function renderFooter( $record ) {
		global $wgLang, $wgUser;

		$id = $record->aft_id;
		$ownFeedback = ArticleFeedbackv5Utils::isOwnFeedback( $record );

		$voteLinks = '';
		$voteStats = '';
		$abuseLink = '';
		$abuseStats = '';

		// Add helpful/unhelpful voting links (for posts other than your own)
		// only for readers; editors have more powerful tools
		if ( !$this->isAllowed( 'aft-editor' ) && !$ownFeedback ) {
			$voteLinks =
				Html::element(
					'span',
					array( 'class' => 'articleFeedbackv5-helpful-caption' ),
					wfMessage( 'articlefeedbackv5-form-helpful-label' )->text()
				) .
				Html::element(
					'a',
					array(
						'id' => "articleFeedbackv5-helpful-link-$id",
						'class' => 'articleFeedbackv5-helpful-link',
						'href' => '#',
						'data-action' => 'helpful'
					),
					wfMessage( 'articlefeedbackv5-form-helpful-yes-label' )->text()
				) .
				Html::element(
					'a',
					array(
						'id' => "articleFeedbackv5-unhelpful-link-$id",
						'class' => 'articleFeedbackv5-unhelpful-link',
						'href' => '#',
						'data-action' => 'unhelpful'
					),
					wfMessage( 'articlefeedbackv5-form-helpful-no-label' )->text()
				);

		// add helpful voting percentage for editors
		} elseif ( $this->isAllowed( 'aft-editor' ) ) {
			$percent =
				wfMessage( 'articlefeedbackv5-form-helpful-votes-percent' )
					->rawParams(
						ArticleFeedbackv5Utils::percentHelpful(
							$record->aft_helpful,
							$record->aft_unhelpful
						)
					)->escaped();

			$counts =
				wfMessage(
					'articlefeedbackv5-form-helpful-votes-count',
					$record->aft_helpful,
					$record->aft_unhelpful
				)->text();

			$votesClass = 'articleFeedbackv5-helpful-votes';
			if ( $record->aft_helpful + $record->aft_unhelpful > 0 ) {
				$votesClass .= ' articleFeedbackv5-has-votes';

				if ( $record->aft_helpful >= $record->aft_unhelpful ) {
					$votesClass .= ' articleFeedbackv5-votes-positive';
				} else {
					$votesClass .= ' articleFeedbackv5-votes-negative';
				}
			}

			$voteStats =
				Html::rawElement(
					'span',
					array(
						'class' => $votesClass,
						'id'    => "articleFeedbackv5-helpful-votes-$id",
						'title' => $counts
					),
					$percent
				);
		}

		// add abuse flagging (for posts other than your own)
		// only for readers; editors have more powerful tools
		if ( $this->isAllowed( 'aft-reader' ) && !$ownFeedback ) {
			global $wgArticleFeedbackv5AbusiveThreshold;

			if ( !$this->isAllowed( 'aft-editor' ) ) {
				$abuseLink =
					Html::element(
						'a',
						array(
							'id'    => "articleFeedbackv5-flag-link-$id",
							'class' => 'articleFeedbackv5-flag-link',
							'title' => wfMessage( 'articlefeedbackv5-form-tooltip-flag' )->text(),
							'href'  => '#',
							'data-action'  => 'flag',
						),
						wfMessage(
							'articlefeedbackv5-form-flag',
							$wgLang->formatNum( $record->aft_flag )
						)->text()
					);

			// add count for editors
			} else {
				$aclass = 'articleFeedbackv5-abuse-count';
				if ( $record->aft_flag > 0 ) {
					$aclass .= ' articleFeedbackv5-has-abuse-flags';
				}
				if ( $record->aft_flag >= $wgArticleFeedbackv5AbusiveThreshold ) {
					$aclass .= ' abusive';
				}

				$abuseStats =
					Html::element(
						'span',
						array(
							'id'    => "articleFeedbackv5-abuse-count-$id",
							'class' => $aclass
						),
						wfMessage(
							'articlefeedbackv5-form-abuse-count',
							$wgLang->formatNum( $record->aft_flag )
						)->text()
					);
			}
		}

		$ownPost = '';
		if ( !$this->isAllowed( 'aft-editor' ) && $ownFeedback ) {
			// Add ability to hide own posts for readers, only when we're
			// certain that the feedback was posted by the current user
			if ( $wgUser->getId() && $wgUser->getId() == intval( $record->aft_user ) ) {
				// Message can be:
				//  * articlefeedbackv5-form-(hide|unhide)[-own]
				if ( $record->isHidden() ) {
					$action = 'unhide';
				} else {
					$action = 'hide';
				}

				$ownPost =
					Html::rawElement(
						'div',
						array( 'class' => 'articleFeedbackv5-comment-foot-hide' ),
						Html::element(
							'a',
							array(
								'id' => "articleFeedbackv5-$action-link-$id",
								'class' => "articleFeedbackv5-$action-link",
								'title' => wfMessage( "articlefeedbackv5-form-tooltip-$action-own" )->text(),
								'href' => '#',
								'data-action' => $action,
							),
							wfMessage( "articlefeedbackv5-form-$action-own" )->text()
						)
					);

			// display message they can't monitor own feedback
			} else {
				$ownPost .=
					Html::element(
						'p',
						array( 'class' => 'articleFeedbackv5-form-own-feedback' ),
						wfMessage( 'articlefeedbackv5-form-own-feedback' )
					);
			}
		}

		return
			Html::rawElement(
				'div',
				array( 'class' => 'articleFeedbackv5-vote-wrapper' ),
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-comment-foot-helpful' ),
					$voteLinks . $voteStats
				) .
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-comment-foot-abuse' ),
					$abuseLink . $abuseStats
				) .
				$ownPost .
				Html::element( 'div', array( 'class' => 'clear' ) )
			);
	}

	/**
	 * Returns the tag block
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the rendered tag block
	 */
	private function renderTagBlock( $record ) {
		if ( $this->isAllowed( 'aft-editor' ) && $record->isOversighted() ) {
			$status = 'oversight';
		} elseif ( $this->isAllowed( 'aft-editor' ) && $record->isHidden() ) {
			$status = 'hide';
		} elseif ( $record->isResolved() ) {
			$status = 'resolve';
		} elseif ( $record->isFeatured() ) {
			$status = 'feature';
		} elseif ( $record->isNonActionable() ) {
			$status = 'noaction';
		} else {
			return '';
		}

		return
			Html::rawElement(
			'div',
			array( 'class' => 'articleFeedbackv5-comment-tags' ),
			Html::element(
				'span',
				array( 'class' => "articleFeedbackv5-$status-marker" ),
				wfMessage( "articlefeedbackv5-$status-marker" )->text()
			)
		);

	}

	/**
	 * Returns the toolbox
	 *
	 * @param  $record ArticleFeedbackv5Model the record
	 * @return string  the rendered toolbox
	 */
	private function renderToolbox( $record ) {
		// check if people are allowed to perform actions
		if ( !$this->isAllowed( 'aft-editor' ) ) {
			return '';
		}

		$toolbox = '';

		// no editor-action has yet been performed, show tools
		if ( !$record->isFeatured() && !$record->isResolved() && !$record->isNonActionable() && !$record->isHidden() && !$record->isOversighted() ) {
			$tools =
				$this->buildToolboxLink( $record, 'feature' ) .
				$this->buildToolboxLink( $record, 'resolve' ) .
				$this->buildToolboxLink( $record, 'noaction' ) .
				$this->buildToolboxLink( $record, 'hide' );

			if ( $tools ) {
				$toolbox .=
					Html::element(
						'p',
						array( 'class' => 'articleFeedbackv5-form-toolbox-label' ),
						wfMessage( 'articlefeedbackv5-form-toolbox-label' )->text()
					) .
					Html::rawElement(
						'ul',
						array( 'id' => "articleFeedbackv5-feedback-tools-list-$record->aft_id" ),
						$tools
				);
			}

		// editor-action already performed; display "undo" + details
		} else {
			// get details on last editor action
			$last = $record->getLastEditorActivity( $record );

			// it shouldn't even be possible that $last contains nothing, but hey
			if ( $last ) {
				$tools = '';

				// don't add editor-action undo possibility if post has been oversighted (that would no longer make sense...)
				if ( !$record->isOversighted() ) {
					// undo-link
					$tools .= $this->buildToolboxLink( $record, "un$last->log_action" );
				}

				// build oversight-related tools
				if ( $record->isHidden() ) {
					// only show "request oversight" to those who don't have permissions to oversight themselves
					if ( $this->isAllowed( 'aft-monitor' ) && !$this->isAllowed( 'aft-oversighter' ) ) {
						/*
						 * When requested by this user already, it will be transformed into an unrequest
						 * link through JS. When the request has been declined already, add a class to
						 * make sure this user knows about it & is no longer capable to request for this entry.
						 */
						$class = $record->isDeclined() ? 'inactive' : '';
						$tools .= $this->buildToolboxLink( $record, 'request', $class );
					}

					if ( $this->isAllowed( 'aft-oversighter' ) ) {
						if ( $record->isOversighted() ) {
							$tools .= $this->buildToolboxLink( $record, 'unoversight' );
						} else {
							if ( $record->isRequested() || $record->isDeclined() ) {
								$class = $record->isDeclined() ? 'inactive' : '';
								$tools .= $this->buildToolboxLink( $record, 'decline', $class );
							}
							$tools .= $this->buildToolboxLink( $record, 'oversight' );
						}
					}
				}

				// if current user is the one who performed the action, add a link to
				// leave a note to clarify why the action was performed
				$commentLink = '';
				if ( $last->log_user && $last->log_user == $this->user->getId() && $last->log_comment == '' ) {
					$commentLink .=
						Html::element(
							'a',
							array(
								'id' => "articleFeedbackv5-note-link-$record->aft_id",
								'class' => 'articleFeedbackv5-tipsy-link articleFeedbackv5-note-link', // tipsy for given data-action will be loaded when clicked
								'title' => wfMessage( 'articlefeedbackv5-form-tooltip-note' )->text(),
								'href' => '#',
								'data-action' => $last->log_action,
								'data-log-id' => $last->log_id,
							),
							wfMessage( 'articlefeedbackv5-form-note' )->text()
						);
				}

				$toolbox .=
					// performer/action info
					Html::rawElement(
						'div',
						array( 'class' => "articleFeedbackv5-feedback-tools-details" ),

						// performer/action info
						Html::rawElement(
							'p',
							array( 'class' => "articleFeedbackv5-activity-short-status" ),
							wfMessage( "articlefeedbackv5-short-status-$last->log_action" )
								->rawParams( ArticleFeedbackv5Utils::getUserLink( $last->log_user, $last->log_user_text ) )
								->parse()
						) .

						// link for activity log popup
						Html::element(
							'a',
							array(
								'id' => "articleFeedbackv5-activity-link-$record->aft_id",
								'class' => 'articleFeedbackv5-tipsy-link articleFeedbackv5-activity-link'.( $last->log_comment ? '' : ' activity-empty' ), // tipsy for given data-action will be loaded when clicked
								'href' => '#',
								'data-action' => 'activity',
							),
							wfMessage( "articlefeedbackv5-viewactivity".( $last->log_comment ? '' : '-empty' ) )->text()
						) .

						// link to add comment
						$commentLink .

						// tools (undo & possibly oversight-related actions)
						Html::rawElement(
							'ul',
							array( 'id' => "articleFeedbackv5-feedback-tools-list-$record->aft_id" ),
							$tools
						)
					);
			}
		}

		// display message they can't monitor own feedback
		if ( ArticleFeedbackv5Utils::isOwnFeedback( $record ) ) {
			$toolbox .=
				Html::element(
					'p',
					array( 'class' => 'articleFeedbackv5-form-own-feedback' ),
					wfMessage( 'articlefeedbackv5-form-own-feedback' )
				);
		}

		return
			Html::rawElement(
				'div',
				array(
					'class' => 'articleFeedbackv5-feedback-tools',
					'id'    => "articleFeedbackv5-feedback-tools-$record->aft_id"
				),
				$toolbox
			);
	}

	/**
	 * Returns the permalink info section
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered info section
	 */
	private function renderPermalinkInfo( $record ) {
		global $wgLang;

		if ( !$this->isAllowed( 'aft-editor' ) ) {
			return '';
		}

		// Metadata section
		$metadata =
			Html::rawElement(
				'div',
				array( 'class' => 'articleFeedbackv5-feedback-permalink-meta' ),
				Html::rawElement(
					'p',
					array(),
					wfMessage( 'articlefeedbackv5-permalink-written-by-' . ( $record->aft_user == 0 ? 'reader' : 'editor' ) )
						->params( $record->getExperiment() )
						->parse()
				) .
				Html::rawElement(
					'p',
					array(),
					wfMessage( 'articlefeedbackv5-permalink-info-posted' )
						->params( $wgLang->date( $record->aft_timestamp ), $wgLang->time( $record->aft_timestamp ) )
						->escaped()
				) .
				Html::rawElement(
					'p',
					array( 'class' => 'articleFeedbackv5-old-revision' ),
					Linker::link(
						Title::newFromID( $record->aft_page ),
						wfMessage( 'articlefeedbackv5-permalink-info-revision-link' )->escaped(),
						array(),
						array( 'oldid'  => $record->aft_page_revision )
					)
				)
			);

		$comment = '';
		if ( $record->aft_comment ) {
			$comment .=
				Html::rawElement(
					'dt',
					array(),
					wfMessage( 'articlefeedbackv5-permalink-info-stats-title-length' )->escaped()
				) .
				Html::rawElement(
					'dd',
					array(),
					wfMessage(
						'articlefeedbackv5-permalink-info-length-words',
						str_word_count( $record->aft_comment )
					)->escaped() .
					'&nbsp;' .
					Html::rawElement(
						'span',
						array(),
						wfMessage(
							'articlefeedbackv5-permalink-info-length-characters',
							strlen( $record->aft_comment )
						)->escaped()
					)
				);
		}

		// Stats section
		$relevance = $record->aft_relevance_score;
		$helpfulness = $record->aft_helpful - $record->aft_unhelpful;
		$stats =
			Html::rawElement(
				'dl',
				array( 'class' => 'articleFeedbackv5-feedback-permalink-stats' ),
				$comment .
				Html::rawElement(
					'dt',
					array(),
					wfMessage( 'articlefeedbackv5-permalink-info-stats-title-scores' )->escaped()
				) .
				Html::rawElement(
					'dd',
					array( 'class' => 'articleFeedbackv5-feedback-permalink-scores' ),
					Html::rawElement(
						'dl',
						array(),
						Html::rawElement(
							'dt',
							array(),
							wfMessage( 'articlefeedbackv5-permalink-info-stats-subtitle-relevance' )->escaped()
						) .
						Html::element(
							'dd',
							array(),
							$relevance > 0 ? '+' . $relevance : $relevance
						) .
						Html::rawElement(
							'dt',
							array(),
							wfMessage( 'articlefeedbackv5-permalink-info-stats-subtitle-helpfulness' )->escaped()
						) .
						Html::element(
							'dd',
							array(),
							$helpfulness > 0 ? '+' . $helpfulness : $helpfulness
						)
					)
				)
			);

		// Activity section
		$last = $this->getLastActivity( $record );
		if ( $last ) {
			$timestamp = new MWTimestamp( $last->log_timestamp );

			$notes = '';
			if ( $last->log_comment ) {
				$notes .=
					Html::element(
						'p',
						array( 'class' => 'articleFeedbackv5-feedback-permalink-activity-status' ),
						$last->log_comment
					);
			}

			$activity =
				Html::rawElement(
					'p',
					array( 'class' => 'articleFeedbackv5-feedback-permalink-activity-status' ),
					Html::rawElement(
						'span',
						array( 'class' => 'articleFeedbackv5-feedback-permalink-status articleFeedbackv5-laststatus-' . $last->log_action ),
						wfMessage( 'articlefeedbackv5-permalink-status-' . $last->log_action )
							->rawParams( ArticleFeedbackv5Utils::getUserLink( $last->log_user, $last->log_user_text ) )
							->rawParams( $timestamp->getHumanTimestamp()->escaped() )
							->parse()
					)
				) .
				$notes .
				Html::rawElement(
					'p',
					array( 'class' => 'articleFeedbackv5-feedback-permalink-activity-more' ),
					Html::rawElement(
						'a',
						array(
							'href' => '#',
							'class' => 'articleFeedbackv5-activity2-link', // tipsy for given data-action will be loaded when clicked
							'data-action' => 'activity2'
						),
						wfMessage( 'articlefeedbackv5-permalink-activity-more' )->escaped()
					)
				) .
				Html::element(
					'div',
					array( 'id' => 'articleFeedbackv5-permalink-activity-log' )
				);
		} else {
			$activity =
				Html::rawElement(
					'p',
					array( 'class' => 'articleFeedbackv5-feedback-permalink-activity-none' ),
					wfMessage( 'articlefeedbackv5-permalink-activity-none')->escaped()
				);
		}

		// Frame and return
		return
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-feedback-permalink-info' ),
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-feedback-permalink-about' ),
					Html::rawElement(
						'h4',
						array(),
						wfMessage( 'articlefeedbackv5-permalink-info-title' )->escaped() .
						Html::rawElement(
							'span',
							array(),
							wfMessage( 'articlefeedbackv5-permalink-info-subtitle' )
								->params( $record->aft_id )
								->escaped()
						)
					) .
					$metadata .
					$stats
				) .
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-feedback-permalink-activity' ),
					Html::rawElement(
						'h4',
						array(),
						wfMessage( 'articlefeedbackv5-permalink-activity-title' )->escaped() .
						Html::rawElement(
							'span',
							array(),
							wfMessage( 'articlefeedbackv5-permalink-activity-subtitle' )
								->params( ArticleFeedbackv5Activity::getActivityCount( $record ) )
								->escaped()
						)
					) .
					$activity
				)
			);
	}

	/**
	 * Will build the link to perform a certain action
	 *
	 * @param ArticleFeedbackv5Model $record
	 * @param string $action
	 * @param string[optional] $class Additional class to add
	 * @return string
	 */
	private function buildToolboxLink( $record, $action, $class = '' ) {
		// check if user is allowed to perform this action
		if ( !isset( ArticleFeedbackv5Activity::$actions[$action] ) ||
			ArticleFeedbackv5Utils::isOwnFeedback( $record ) ||
			!ArticleFeedbackv5Activity::canPerformAction( $action, $this->user ) ) {
			return '';
		}

		return Html::rawElement(
			'li',
			array(),
			Html::element(
				'a',
				array(
					'id' => "articleFeedbackv5-$action-link-$record->aft_id",
					'class' => "articleFeedbackv5-$action-link $class",
					'title' => wfMessage( "articlefeedbackv5-form-tooltip-$action" )->text(),
					'href' => '#',
					'data-action' => $action,
				),
				wfMessage( "articlefeedbackv5-form-$action" )->text()
			)
		);
	}

	/**
	 * @param ArticleFeedbackv5Model $record
	 * @return ResultWrapper|bool
	 */
	public function getLastActivity( ArticleFeedbackv5Model $record ) {
		foreach( ArticleFeedbackv5Activity::getList( $record, $this->user, 1 ) as $last ) {
			return $last;
		}
		return false;
	}

	/**
	 * Returns whether an action is allowed
	 *
	 * @param  $action string the name of the action
	 * @return bool whether it's allowed
	 */
	public function isAllowed( $permission ) {
		return $this->user->isAllowed( $permission ) && !$this->user->isBlocked();
	}
}
