<?php
/**
 * ArticleFeedbackv5Render class
 *
 * @package    ArticleFeedback
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * Handles rendering of feedback
 *
 * @package    ArticleFeedback
 */
class ArticleFeedbackv5Render {

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
	 * Permissions
	 *
	 * Keys: can_flag, can_vote, can_hide, can_delete, can_feature,
	 * see_deleted, and see_hidden
	 *
	 * @var array
	 */
	private $permissions = array(
		'can_flag'    => false,
		'can_vote'    => false,
		'can_hide'    => false,
		'can_delete'  => false,
		'can_feature' => false,
		'see_deleted' => false,
		'see_hidden'  => false,
	);

	/**
	 * Constructor
	 *
	 * @param $user      User [optional] the current user
	 * @param $permalink bool [optional] whether this is a permalink?
	 * @param $central   bool [optional] whether this is on the central log?
	 * @param $highlight bool [optional] whether this is a highlighted row?
	 */
	public function __construct( $user = null, $permalink = false, $central = false, $highlight = false ) {
		if ( $user ) {
			$this->setPermission( 'can_flag', !$user->isBlocked() );
			$this->setPermission( 'can_vote', !$user->isBlocked() );
			$this->setPermission( 'can_hide', $user->isAllowed( 'aftv5-hide-feedback' ) );
			$this->setPermission( 'can_delete', $user->isAllowed( 'aftv5-delete-feedback' ) );
			$this->setPermission( 'can_feature', $user->isAllowed( 'aftv5-feature-feedback' ) );
			$this->setPermission( 'see_deleted', $user->isAllowed( 'aftv5-see-deleted-feedback' ) );
			$this->setPermission( 'see_hidden', $user->isAllowed( 'aftv5-see-hidden-feedback' ) );
		}
		$this->setIsPermalink( $permalink );
		$this->setIsCentral( $central );
		$this->setIsHighlighted( $highlight );
	}

	/**
	 * Runs the fetch
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered row
	 */
	public function run( $record ) {
		global $wgLang;

		// Empty gray mask, for permalinks where the feedback is deleted or
		// hidden, and the user doesn't have permission to see them
		if  ( ( $this->isPermalink || $this->isHighlighted ) && (
				( $record[0]->af_is_deleted && !$this->hasPermission( 'see_deleted' ) )
				|| ( $record[0]->af_is_hidden && !$this->hasPermission( 'see_hidden') )
			) ) {
			return $this->emptyGrayMask( $record );
		}

		// Build with the actual content of the feedback (header + comment)
		if ( $this->isCentral ) {
			$content = $this->renderCentral( $record );
		} else {
			switch( $record[0]->af_form_id ) {
				case 1: $content = $this->renderBucket1( $record ); break;
				case 2: $content = $this->renderBucket2( $record ); break;
				case 3: $content = $this->renderBucket3( $record ); break;
				case 6: $content = $this->renderBucket1( $record ); break;
				default: $content = $this->renderNoBucket( $record ); break;
			}
		}

		// Build the footer
		$footer = $this->renderFooter( $record );

		// Build the toolbox
		$toolbox = $this->renderToolbox( $record );

		// Get the top class
		$topClass = 'articleFeedbackv5-feedback';
		if ( $record[0]->af_is_hidden ) {
			$topClass .= ' articleFeedbackv5-feedback-hidden';
		}
		if ( $record[0]->af_is_deleted ) {
			$topClass .= ' articleFeedbackv5-feedback-deleted';
		}
		if ( $record[0]->af_is_featured ) {
			$topClass .= ' articleFeedbackv5-feedback-featured';
		}
		if ( $record[0]->af_is_resolved ) {
			$topClass .= ' articleFeedbackv5-feedback-resolved';
		}
		if ( !$this->hasToolbox() ) {
			$topClass .= ' articleFeedbackv5-comment-notoolbox';
		}
		if ( $this->isPermalink ) {
			$topClass .= ' articleFeedbackv5-feedback-permalink';
		}
		if ( $this->isHighlighted ) {
			$topClass .= ' articleFeedbackv5-feedback-highlighted';
		}

		// Get the class for the comment wrap
		$wrapClass = 'articleFeedbackv5-comment-wrap'
			. ' articleFeedbackv5-comment-' . $this->getMood( $record );

		// Permalink info
		$permalinkInfo = '';
		if ( $this->isPermalink ) {
			$permalinkInfo = $this->renderPermalinkInfo( $record );
		}

		// Join it all together...
		return
			// <div class={$topClass}" rel="{feedback id}">
			Html::openElement( 'div', array(
				'class' => $topClass,
				'rel'   => $record[0]->af_id )
			)
				// {gray mask, if applicable}
				. $this->grayMask( $record )
				// <div class="{$wrapClass}">
				. Html::openElement( 'div', array( 'class' => $wrapClass ) )
					// {feedback content}
					. $content
					// {footer links, e.g. helpful, abuse}
					. $footer
				// </div>
				. Html::closeElement( 'div' )
				// {toolbox, e.g. feature, hide}
				. $toolbox
			// </div>
			. Html::closeElement( 'div' )
			// {info section for permalinks}
			. $permalinkInfo;
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
	 * @param  $record array the record, with keys 0 + answers
	 * @return string the empty gray mask
	 */
	private function emptyGrayMask( array $record ) {
		global $wgLang;
		// hide or oversight?
		if ( $record[0]->af_is_deleted ) {
			$class = 'oversight';
		} else {
			$class = 'hidden';
		}
		return
			// <div class="articleFeedbackv5-feedback
			//     articleFeedbackv5-feedback-{oversight|hidden}
			//     articleFeedbackv5-feedback-emptymask">
			Html::openElement( 'div',  array(
				'class' => 'articleFeedbackv5-feedback '
					. 'articleFeedbackv5-feedback-' . $class . ' '
					. 'articleFeedbackv5-feedback-emptymask'
				) )
				// {gray mask}
				. $this->grayMask( $record, true )
				// <div class="articleFeedbackv5-comment-wrap">
				// </div>
				. Html::element( 'div', array(
					'class' => 'articleFeedbackv5-comment-wrap'
					) )
			// </div>
			. Html::closeElement( 'div' );
	}

	/**
	 * Returns a gray mask
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @param  $empty  bool  [optional] whether the mask is empty; defaults to
	 *                       false
	 * @return string the gray mask
	 */
	private function grayMask( array $record, $empty = false ) {
		global $wgLang;
		if ( $record[0]->af_is_deleted ) {
			$type = 'oversight';
		} elseif ( $record[0]->af_is_hidden ) {
			$type = 'hidden';
		} else {
			return '';
		}

		$viewLink = '';
		if ( !$empty ) {
			$viewLink =
				// <span class="articleFeedbackv5-mask-view">
				Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-mask-view' ) )
					//   <a href="#" onclick="return false;">
					//     {msg:articlefeedbackv5-mask-view-contents}
					//   </a>
					. Html::rawElement( 'a', array(
							'href' => '#',
							'onclick' => 'return false;',
						),
						wfMessage( 'articlefeedbackv5-mask-view-contents' )->escaped()
					)
				// </span>
				. Html::closeElement( 'span' );
		}

		return
			// <div class="articleFeedbackv5-post-screen">
			Html::openElement( 'div', array(
					'class' => 'articleFeedbackv5-post-screen'
				) )
				// <div class="articleFeedbackv5-mask-text-wrapper">
				. Html::openElement( 'div', array(
						'class' => 'articleFeedbackv5-mask-text-wrapper'
					) )
					// <span class="articleFeedbackv5-mask-text">
					. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-mask-text' ) )
						// <span class="articleFeedbackv5-mask-info">
						//   {msg:articlefeedbackv5-mask-text-{oversight|hidden}}
						// </span>
						. Html::rawElement( 'span', array( 'class' => 'articleFeedbackv5-mask-info' ),
							ApiArticleFeedbackv5Utils::renderMaskLine( $type,
								$record[0]->af_id,
								$record[0]->af_last_status_user_id,
								$record[0]->af_last_status_timestamp )
						)
						. $viewLink
					// </span>
					. Html::closeElement( 'span' )
				// </div>
				. Html::closeElement( 'div' )
			// </div>
			. Html::closeElement( 'div' );
	}

	/**
	 * Returns the mood of the feedback
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the mood (positive, negative, or neutral)
	 */
	public function getMood( $record ) {
		switch( $record[0]->af_form_id ) {
			case 1:
				if ( isset( $record['found'] ) && $record['found']->aa_response_boolean == 1 ) {
					return 'positive';
				} elseif ( isset( $record['found'] ) && $record['found']->aa_response_boolean !== null ) {
					return 'negative';
				} else {
					return 'neutral';
				}
			case 2:
				$type = $record['tag']->afo_name;
				return $type == 'problem' ? 'negative' : 'positive';
			case 3:
				return $record['rating']->aa_response_rating >= 3 ? 'positive' : 'negative';
			default:
				return 'neutral';
		}
	}

	/**
	 * Returns the feedback head and comment for form #1
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderBucket1( $record ) {
		$mood = $this->getMood( $record );
		if ( 'positive' == $mood ) {
			$msg = 'articlefeedbackv5-form1-header-found';
		} elseif ( 'negative' == $mood ) {
			$msg = 'articlefeedbackv5-form1-header-not-found';
		} else {
			$msg = 'articlefeedbackv5-form1-header-left-comment';
		}
		return $this->feedbackHead( $msg, $record[0] )
			. $this->renderComment(
				isset( $record['comment'] ) ? $record['comment']->aa_response_text : '',
				$record[0]->af_id
			);
	}

	/**
	 * Returns the feedback head and comment for form #2
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderBucket2( $record ) {
		$type  = $record['tag']->afo_name;
		// Document for grepping. Uses any of the messages:
		// * articlefeedbackv5-form2-header-praise
		// * articlefeedbackv5-form2-header-problem
		// * articlefeedbackv5-form2-header-question
		// * articlefeedbackv5-form2-header-suggestion
		return $this->feedbackHead( "articlefeedbackv5-form2-header-$type", $record[0], $type )
			. $this->renderComment(
				isset( $record['comment'] ) ? $record['comment']->aa_response_text : '',
				$record[0]->af_id
			);
	}

	/**
	 * Returns the feedback head and comment for form #3
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderBucket3( $record ) {
		return $this->feedbackHead( 'articlefeedbackv5-form3-header', $record[0], $record['rating']->aa_response_rating )
			. $this->renderComment(
				isset( $record['comment'] ) ? $record['comment']->aa_response_text : '',
				$record[0]->af_id
			);
	}

	/**
	 * Returns the feedback head and comment when the form is unknown
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderNoBucket( $record ) {
		return $this->feedbackHead( 'articlefeedbackv5-form-invalid', $record[0] );
	}

	/**
	 * Returns the feedback head and comment for the central log
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderCentral( $record ) {
		return $this->feedbackHead( 'articlefeedbackv5-central-header-left-comment', $record[0] )
			. $this->renderComment(
				isset( $record['comment'] ) ? $record['comment']->aa_response_text : '',
				$record[0]->af_id
			);
	}

	/**
	 * Returns the feedback head
	 *
	 * @param  $message string   the message key describing the the nature of
	 *                           the feedback (e.g., "USER found what they were
	 *                           looking for")
	 * @param  $record  stdClass the record (from the 0 index)
	 * @param  $extra   string   any extra info to send to the message
	 * @return string   the rendered feedback head
	 */
	private function feedbackHead( $message, $record, $extra = '' ) {

		// User info
		$name = $record->user_name;
		if ( $record->af_user_ip ) {
			// Anonymous user, go to contributions page.
			$title =  SpecialPage::getTitleFor( 'Contributions', $record->user_name );
		} else {
			// Registered user, go to user page.
			$title = Title::makeTitleSafe( NS_USER, $record->user_name );
		}

		// If user page doesn't exist, go someplace else.
		// Use the contributions page for now, but it's really up to Fabrice.
		if ( !$title->exists() ) {
			$title = SpecialPage::getTitleFor( 'Contributions', $record->user_name );
		}

		if ( $this->isCentral ) {
			$article = Title::newFromRow($record);
			$parsedMsg = wfMessage( $message, $name )->rawParams(
					Linker::link( $title, $name )
				)->rawParams(
					Linker::link( $article )
				)->escaped();
		} else {
			$parsedMsg = wfMessage( $message, $name )->rawParams(
					Linker::link( $title, $name )
				)->escaped();
		}

		return
			// <div class="articleFeedbackv5-comment-head">
			Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-comment-head'
			) )
				// <h3>{type-appropriate message}</h3>
				. Html::rawElement( 'h3', array(), $parsedMsg )
				// {permalink/timestamp}
				. $this->renderPermalinkTimestamp( $record )
				// The tag block (featured/resolved markers)
				. $this->renderTagBlock( $record )
			// </div>
			. Html::closeElement( 'div' );
	}

	/**
	 * Returns the permalink/timestamp
	 *
	 * @param  $record stdClass the record (from the 0 index)
	 * @return string  the rendered permalink/timestamp
	 */
	private function renderPermalinkTimestamp( $record ) {
		global $wgLang;
		$id    = $record->af_id;
		$title = Title::newFromRow($record)->getPrefixedDBkey();

		$date = ApiArticleFeedbackv5Utils::renderTimeAgo( $record->af_created );
		$message = wfMessage( 'articleFeedbackv5-comment-ago' )
			->rawParams( $date )
			->escaped();

		$html =
			// <span class="articleFeedbackv5-feedback-details">
			Html::openElement( 'span', array(
				'class' => 'articleFeedbackv5-comment-details'
			) )
				// <span class="articleFeedbackv5-comment-details-date">{relative date}</span>
				. Html::rawElement( 'span', array(
					'class' => 'articleFeedbackv5-comment-details-date'
				), $message );
		if ( !$this->isPermalink ) {
			$html .= wfMessage( 'pipe-separator' )->escaped()
				// <span class="articleFeedbackv5-comment-details-link">
				. Html::openElement( 'span', array(
					'class' => 'articleFeedbackv5-comment-details-link'
				) )
					// <a href="{permalink}">{msg:articleFeedbackv5-details-link}</a>
					. Linker::link(
						SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$title/$id" ),
						wfMessage( 'articleFeedbackv5-details-link' )->escaped()
					)
				// </span>
				. Html::closeElement( 'span' );
		}
		$html .=
			// </span>
			Html::closeElement( 'span' );

		return $html;
	}

	/**
	 * Returns the marked-up feedback comment
	 *
	 * @param  $text       string the comment
	 * @param  $feedbackId int    the feedback ID
	 * @return string      the rendered comment
	 */
	private function renderComment( $text, $feedbackId ) {
		global $wgLang;

		$short = $this->isPermalink ? $text : $wgLang->truncate( $text, 500 );

		// <blockquote>
		$rv = Html::openElement( 'blockquote' )
			// <span class="articleFeedbackv5-comment-short"
			//   id="articleFeedbackv5-comment-short-{$feedbackId}">
			//   {truncated comment}
			// </span>
			. Html::element( 'span',
				array(
					'class' => 'articleFeedbackv5-comment-short',
					'id'    => "articleFeedbackv5-comment-short-$feedbackId"
				),
				$short
			);

		// If the short string is the same size as the original, no truncation
		// happened, so no controls are needed.  If it's longer, show the short
		// text, with the 'show more' control.
		if ( $short != $text ) {
			// <span class="articleFeedbackv5-comment-full"
			//   id="articleFeedbackv5-comment-full-{$feedbackId}">
			//   {full-length comment}
			// </span>
			$rv .= Html::element( 'span',
					array(
						'class' => 'articleFeedbackv5-comment-full',
						'id'    => "articleFeedbackv5-comment-full-$feedbackId"
					),
					$text
				)
				// <a class="articleFeedbackv5-comment-toggle"
				//   id="articleFeedbackv5-comment-toggle-{$feedbackId}">
				//   {articlefeedbackv5-comment-more}
				// </a>
				. Html::element( 'a', array(
					'href'  => '#more',
					'class' => 'articleFeedbackv5-comment-toggle',
					'id'    => "articleFeedbackv5-comment-toggle-$feedbackId"
				), wfMessage( 'articlefeedbackv5-comment-more' )->text() );
		}

		// </blockquote>
		$rv .= Html::closeElement( 'blockquote' );

		return $rv;
	}

	/**
	 * Returns the footer links
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered footer
	 */
	private function renderFooter( $record ) {
		global $wgLang;
		$id = $record[0]->af_id;

		// Start the footer
		$footer =
			// <div class="articleFeedbackv5-vote-wrapper">
			Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-vote-wrapper'
			) )
				// <div class="articleFeedbackv5-comment-foot">
				. Html::openElement( 'div', array( 'class' => 'articleFeedbackv5-comment-foot' ) );

		// Add helpful/unhelpful voting links
		if ( $this->hasPermission( 'can_vote' ) ) {
			$footer .=
				// <span class="articleFeedbackv5-helpful-caption">
				//   {msg:articlefeedbackv5-form-helpful-label}
				// </span>
				Html::element( 'span', array(
					'class' => 'articleFeedbackv5-helpful-caption'
				), wfMessage( 'articlefeedbackv5-form-helpful-label' )->text()
				)
				// <a id="articleFeedbackv5-helpful-link-{$id}"
				//   class="articleFeedbackv5-helpful-link">
				//   {msg:articlefeedbackv5-form-helpful-yes-label}
				// </a>
				. Html::element( 'a', array(
					'id'    => "articleFeedbackv5-helpful-link-$id",
					'class' => 'articleFeedbackv5-helpful-link'
				), wfMessage( 'articlefeedbackv5-form-helpful-yes-label' )->text() )
				// <a id="articleFeedbackv5-unhelpful-link-{$id}"
				//   class="articleFeedbackv5-unhelpful-link">
				//   {msg:articlefeedbackv5-form-helpful-no-label}
				// </a>
				. Html::element( 'a', array(
					'id'    => "articleFeedbackv5-unhelpful-link-$id",
					'class' => 'articleFeedbackv5-unhelpful-link'
				), wfMessage( 'articlefeedbackv5-form-helpful-no-label' )->text() );
		}

		// Add helpful voting percentage for editors
		if ( $this->hasPermission( 'can_feature' ) ) {
			$percent = wfMessage( 'articlefeedbackv5-form-helpful-votes' )
				->rawParams( wfMessage( 'percent',
						ApiArticleFeedbackv5Utils::percentHelpful(
							$record[0]->af_helpful_count,
							$record[0]->af_unhelpful_count
						)
					)->escaped() )
				->escaped();
			$counts = wfMessage( 'articlefeedbackv5-form-helpful-votes-count',
					$record[0]->af_helpful_count,
					$record[0]->af_unhelpful_count )->text();
			$votesClass = 'articleFeedbackv5-helpful-votes';
			if ( ( $record[0]->af_helpful_count + $record[0]->af_unhelpful_count ) > 0 ) {
				$votesClass .= ' articleFeedbackv5-has-votes';
			}
			$footer .=
				// <span class="articleFeedbackv5-helpful-votes"
				//   id="articleFeedbackv5-helpful-votes-{$id}">
				//   {msg:articlefeedbackv5-form-helpful-votes}
				// </span>
				Html::rawElement( 'span', array(
					'class' => $votesClass,
					'id'    => "articleFeedbackv5-helpful-votes-$id",
					'title' => $counts,
				), $percent );
		}

		// </div>
		$footer .= Html::closeElement( 'div' );

		// Add abuse flagging
		if ( $this->hasPermission( 'can_flag' ) ) {
			global $wgArticleFeedbackv5AbusiveThreshold;
			// <a id="articleFeedbackv5-abuse-link-{$id}"
			//   class="articleFeedbackv5-abuse-link"
			//   href="#" rel="{abuse count}">
			//   {msg:articlefeedbackv5-form-abuse}
			// </a>
			$footer .= Html::element( 'a', array(
					'id'    => "articleFeedbackv5-abuse-link-$id",
					'class' => 'articleFeedbackv5-abuse-link',
					'href'  => '#',
				), wfMessage(
					'articlefeedbackv5-form-abuse',
					$wgLang->formatNum( $record[0]->af_abuse_count ) )->text()
			);

			// Add count for editors
			if ( $this->hasPermission( 'can_feature' ) ) {
				$aclass = 'articleFeedbackv5-abuse-count';
				if ( $record[0]->af_abuse_count >= $wgArticleFeedbackv5AbusiveThreshold ) {
					$aclass .= ' abusive';
				}
				if ( $record[0]->af_abuse_count > 0 ) {
					$aclass .= ' articleFeedbackv5-has-abuse-flags';
				}
				// <span id="articleFeedbackv5-abuse-count-{$id}"
				//   class="articleFeedbackv5-abuse-link{-abusive?}"
				//   href="#" rel="{abuse count}">
				//   {msg:articlefeedbackv5-form-abuse-count}
				// </span>
				$footer .= Html::element( 'span', array(
						'id'    => "articleFeedbackv5-abuse-count-$id",
						'class' => $aclass,
						'href'  => '#',
					), wfMessage(
						'articlefeedbackv5-form-abuse-count',
						$wgLang->formatNum( $record[0]->af_abuse_count ) )->text()
				);
			}
		}

		// </div>
		$footer .= Html::closeElement( 'div' );

		return $footer;
	}

	/**
	 * Returns the tag block
	 *
	 * @param  $record stdClass the record (from the 0 index)
	 * @return string  the rendered tag block
	 */
	private function renderTagBlock( $record ) {
		global $wgLang;
		$id = $record->af_id;

		// <div class="articleFeedbackv5-comment-tags">
		$html = Html::openElement( 'div', array(
			'class' => 'articleFeedbackv5-comment-tags',
		) );

		if ( $this->isHighlighted ) {
			// <span class="articleFeedbackv5-new-marker">
			//   {msg:articlefeedbackv5-new-marker}
			// </span>
			$html .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-new-marker',
			), wfMessage( 'articlefeedbackv5-new-marker' )->text() );
		} elseif ( $this->hasPermission( 'can_feature' ) && $record->af_is_deleted ) {
			// <span class="articleFeedbackv5-deleted-marker">
			//   {msg:articlefeedbackv5-deleted-marker}
			// </span>
			$html .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-deleted-marker',
			), wfMessage( 'articlefeedbackv5-deleted-marker' )->text() );
		} elseif ( $this->hasPermission( 'can_feature' ) && $record->af_is_hidden ) {
			// <span class="articleFeedbackv5-hidden-marker">
			//   {msg:articlefeedbackv5-hidden-marker}
			// </span>
			$html .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-hidden-marker',
			), wfMessage( 'articlefeedbackv5-hidden-marker' )->text() );
		} elseif ( $record->af_is_resolved ) {
			// <span class="articleFeedbackv5-resolved-marker">
			//   {msg:articlefeedbackv5-resolved-marker}
			// </span>
			$html .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-resolved-marker',
			), wfMessage( 'articlefeedbackv5-resolved-marker' )->text() );
		} elseif ( $record->af_is_featured ) {
			// <span class="articleFeedbackv5-featured-marker">
			//   {msg:articlefeedbackv5-featured-marker}
			// </span>
			$html .= Html::element( 'span', array(
				'class' => 'articleFeedbackv5-featured-marker',
			), wfMessage( 'articlefeedbackv5-featured-marker' )->text() );
		}

		// </div>
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * Returns the toolbox
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered toolbox
	 */
	private function renderToolbox( $record ) {
		global $wgLang;
		$id = $record[0]->af_id;

		// Don't render the toolbox if they can't do anything with it.
		if ( !$this->hasToolbox() ) {
			return '';
		}

		// Begin toolbox
		$tools =
			// <div class="articleFeedbackv5-feedback-tools"
			//   id="articleFeedbackv5-feedback-tools-{$id}">
			Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-feedback-tools',
				'id'    => 'articleFeedbackv5-feedback-tools-' . $id
			) )
				// <ul id="articleFeedbackv5-feedback-tools-list-{$id}">
				. Html::openElement( 'ul', array(
					'id' => 'articleFeedbackv5-feedback-tools-list-' . $id
				) );
		$toolsFeature = '';
		$toolsDelete = '';
		$toolsActivity = '';

		// Feature/unfeature and mark/unmark resolved
		if ( $this->hasPermission( 'can_feature' ) ) {
			// Message can be:
			//  * articlefeedbackv5-form-feature
			//  * articlefeedbackv5-form-unfeature
			if ( $record[0]->af_is_featured ) {
				$msg = 'unfeature';
				$class = 'unfeature';
			} else {
				$msg = 'feature';
				$class = 'feature';
			}
			// <li>
			//   <a id="articleFeedbackv5-{feature|unfeature}-link-{$id}"
			//     class="articleFeedbackv5-{feature|unfeature}-link" href="#">
			//     {msg:articlefeedbackv5-form-{feature|unfeature}}
			//   </a>
			// </li>
			$toolsFeature .= Html::rawElement( 'li', array(), Html::element( 'a', array(
				'id'    => "articleFeedbackv5-$class-link-$id",
				'class' => "articleFeedbackv5-$class-link",
				'href' => '#',
			), wfMessage( "articlefeedbackv5-form-" . $msg )->text() ) );

			// Message can be:
			//  * articlefeedbackv5-form-resolve
			//  * articlefeedbackv5-form-unresolve
			if ( $record[0]->af_is_resolved ) {
				$type = 'unresolve';
			} else {
				$type = 'resolve';
			}
			$class = '';
			if ( !$record[0]->af_is_featured && !$record[0]->af_is_resolved ) {
				$class = 'articleFeedbackv5-tool-hidden';
			}
			// <li>
			//   <a id="articleFeedbackv5-{resolve|unresolve}-link-{$id}"
			//     class="articleFeedbackv5-{resolve|unresolve}-link" href="#">
			//     {msg:articlefeedbackv5-form-{resolve|unresolve}}
			//   </a>
			// </li>
			$toolsFeature .= Html::rawElement( 'li', array( 'class' => $class ),
				Html::element( 'a', array(
					'id'    => "articleFeedbackv5-$type-link-$id",
					'class' => "articleFeedbackv5-$type-link",
					'href' => '#',
				), wfMessage( "articlefeedbackv5-form-$type" )->text() )
			);
		}

		// Hide/unhide
		if ( $this->hasPermission( 'can_hide' ) ) {
			// Message can be:
			//  * articlefeedbackv5-form-hide
			//  * articlefeedbackv5-form-unhide
			if ( $record[0]->af_is_hidden ) {
				$msg = 'unhide';
				$class = 'show';
			} else {
				$msg = 'hide';
				$class = 'hide';
			}
			// <li>
			//   <a id="articleFeedbackv5-{hide|unhide}-link-{$id}"
			//     class="articleFeedbackv5-{hide|unhide}-link" href="#">
			//     {msg:articlefeedbackv5-form-{hide|unhide}}
			//   </a>
			// </li>
			$toolsDelete .= Html::rawElement( 'li', array(), Html::element( 'a', array(
				'id'    => "articleFeedbackv5-$class-link-$id",
				'class' => "articleFeedbackv5-$class-link",
				'href' => '#',
			), wfMessage( "articlefeedbackv5-form-" . $msg )->text() ) );
		}

		// Request oversight
		if ( $this->hasPermission( 'can_hide' ) && !$this->hasPermission( 'can_delete' ) ) {
			// Message can be:
			//  * articlefeedbackv5-form-oversight
			//  * articlefeedbackv5-form-unoversight
			if ( $record[0]->af_oversight_count > 0 ) {
				$msg = 'unoversight';
				$class = 'unrequestoversight';
			} else {
				$msg = 'oversight';
				$class = 'requestoversight';
			}
			// <li>
			//   <a id="articleFeedbackv5-{requestoversight|unrequestoversight}-link-{$id}"
			//     class="articleFeedbackv5-{requestoversight|unrequestoversight}-link" href="#">
			//     {msg:articlefeedbackv5-form-{oversight|unoversight}}
			//   </a>
			// </li>
			$toolsDelete .= Html::rawElement( 'li', array(), Html::element( 'a', array(
				'id'    => "articleFeedbackv5-$class-link-$id",
				'class' => "articleFeedbackv5-$class-link",
				'href' => '#',
			), wfMessage( "articlefeedbackv5-form-" . $msg )->text() ) );
		}

		// Delete (a.k.a. oversight)
		if ( $this->hasPermission( 'can_delete' ) ) {
			// if we have oversight requested, add "decline oversight" link
			if ( $record[0]->af_oversight_count > 0 ) {
				// <li>
				//   <a id="articleFeedbackv5-declineoversight-link-{$id}"
				//     class="articleFeedbackv5-declineoversight-link" href="#">
				//     {msg:articlefeedbackv5-form-decline}
				//   </a>
				// </li>
				$toolsDelete .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-declineoversight-link-$id",
					'class' => "articleFeedbackv5-declineoversight-link",
					'href' => '#',
					), wfMessage( "articlefeedbackv5-form-decline" )->text() ) );
			}

			// Message can be:
			//  * articlefeedbackv5-form-delete
			//  * articlefeedbackv5-form-undelete
			if ( $record[0]->af_is_deleted > 0 ) {
				$msg = 'undelete';
				$class = 'unoversight';
			} else {
				$msg = 'delete';
				$class = 'oversight';
			}
			// <li>
			//   <a id="articleFeedbackv5-{oversight|unoversight}-link-{$id}"
			//     class="articleFeedbackv5-{oversight|unoversight}-link" href="#">
			//     {msg:articlefeedbackv5-form-{delete|undelete}}
			//   </a>
			// </li>
			$toolsDelete .= Html::rawElement( 'li', array(), Html::element( 'a', array(
				'id'    => "articleFeedbackv5-$class-link-$id",
				'class' => "articleFeedbackv5-$class-link",
				'href' => '#',
			), wfMessage( "articlefeedbackv5-form-" . $msg )->text() ) );
		}

		// View Activity
		if ( $this->hasPermission( 'can_delete' ) || $this->hasPermission( 'can_hide' ) ) {
			// if no activity has been logged yet, add the "inactive" class so we can display it accordingly
			$activityClass = "articleFeedbackv5-activity-link";
			if ( $this->getActivityCount( $record[0] ) < 1 ) {
				$activityClass .= " inactive";
			}

			// <li>
			//   <a id="articleFeedbackv5-activity-link-{$id}"
			//     class="articleFeedbackv5-activity-link" href="#">
			//     {msg:articlefeedbackv5-form-activity}
			//   </a>
			// </li>
			$toolsActivity.= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-activity-link-$id",
					'class' => $activityClass,
					'href' => '#',
				), wfMessage( "articlefeedbackv5-viewactivity" )->text() ) );
		}

		// create containers for 3 toolbox-groups
		$tools .= Html::rawElement( 'li', array(
			'class' => 'tools_feature'
		), Html::rawElement( 'ul', array(), $toolsFeature));
		$tools .= Html::rawElement( 'li', array(
			'class' => 'tools_delete'
		), Html::rawElement( 'ul', array(), $toolsDelete));
		$tools .= Html::rawElement( 'li', array(
			'class' => 'tools_activity'
		), Html::rawElement( 'ul', array(), $toolsActivity));

		// Close
		$tools .=
				// </ul>
				Html::closeElement( 'ul' )
			// </div>
			. Html::closeElement( 'div' );

		return $tools;
	}

	/**
	 * Returns the permalink info section
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered info section
	 */
	private function renderPermalinkInfo( $record ) {
		global $wgLang, $wgArticleFeedbackv5LinkBuckets;

		if ( !$this->hasPermission( 'see_hidden' ) ) {
			return '';
		}

		$id = $record[0]->af_id;
		$title = Title::newFromRow($record[0])->getPrefixedDBkey();

		// Metadata section
		$utype = $record[0]->af_user_id > 0 ? 'editor' : 'reader';
		$metadata =
			// <div class="articleFeedbackv5-feedback-permalink-meta">
			Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-feedback-permalink-meta'
			) )
				// <p>Written by a registered user <span>using feedback form 1 and link E</span></p>
				// Possible messages:
				//  {msg:articlefeedbackv5-permalink-written-by-editor}
				//  {msg:articlefeedbackv5-permalink-written-by-reader}
				.Html::rawElement( 'p', array(),
					wfMessage( "articlefeedbackv5-permalink-written-by-$utype" )
						->params( $record[0]->af_experiment )
						->parse()
				)
				// <p>{msg:articlefeedbackv5-permalink-info-posted}</p>
				. Html::rawElement( 'p', array(),
					wfMessage( 'articlefeedbackv5-permalink-info-posted' )
						->params( $wgLang->date( $record[0]->af_created ), $wgLang->time( $record[0]->af_created ) )
						->escaped()
				)
				// <p class="articleFeedbackv5-old-revision">
				. Html::openElement( 'p', array(
						'class' => 'articleFeedbackv5-old-revision'
				) )
					// <a href="{previous rev}">{msg:articlefeedbackv5-permalink-info-revision-link}</a></p>
					.  Linker::link(
						Title::newFromRow( $record[0] ),
						wfMessage( 'articlefeedbackv5-permalink-info-revision-link' )->escaped(),
						array(),
						array( 'oldid'  => $record[0]->af_revision_id )
					)
				// </p>
				. Html::closeElement( 'p' )
			// </div>
			. Html::closeElement( 'div' );

		// Stats section
		$stats =
			// <dl class="articleFeedbackv5-feedback-permalink-stats">
			Html::openElement( 'dl', array(
				'class' => 'articleFeedbackv5-feedback-permalink-stats'
			) );
		if ( isset( $record['comment'] ) ) {
			$stats .=
				// <dt>{msg:articlefeedbackv5-permalink-info-stats-title-length}</dt>
				Html::rawElement( 'dt', array(),
					wfMessage( 'articlefeedbackv5-permalink-info-stats-title-length' )->escaped()
				)
				// <dd>
				. Html::openElement( 'dd' )
					// {msg:articlefeedbackv5-permalink-info-length-words}
					. wfMessage(
						'articlefeedbackv5-permalink-info-length-words',
						str_word_count( $record['comment']->aa_response_text )
					)->escaped()
					// &nbsp;
					. '&nbsp;'
					// <span>{msg:articlefeedbackv5-permalink-info-length-characters}</span>
					. Html::rawElement( 'span', array(),
						wfMessage(
							'articlefeedbackv5-permalink-info-length-characters',
							strlen( $record['comment']->aa_response_text )
						)->escaped()
					)
				// </dd>
				. Html::closeElement( 'dd' );
		}
		$relevance = $record[0]->af_relevance_score;
		$helpfulness = $record[0]->af_net_helpfulness;
		$stats .=
				// <dt>{msg:articlefeedbackv5-permalink-info-stats-title-scores}</dt>
				Html::rawElement( 'dt', array(),
					wfMessage( 'articlefeedbackv5-permalink-info-stats-title-scores' )->escaped()
				)
				// <dd class="articleFeedbackv5-feedback-permalink-scores">
				. Html::openElement( 'dd', array(
					'class' => 'articleFeedbackv5-feedback-permalink-scores',
				) )
					// <dl>
					. Html::openElement( 'dl' )
						// <dt>{msg:articlefeedbackv5-permalink-info-stats-subtitle-relevance}</dt>
						. Html::rawElement( 'dt', array(),
							wfMessage( 'articlefeedbackv5-permalink-info-stats-subtitle-relevance' )->escaped()
						)
						// <dd>{relvance score}</dd>
						. Html::element( 'dd', array(),
							$relevance > 0 ? '+' . $relevance : $relevance )
						// <dt>{msg:articlefeedbackv5-permalink-info-stats-subtitle-helpfulness}</dt>
						. Html::rawElement( 'dt', array(),
							wfMessage( 'articlefeedbackv5-permalink-info-stats-subtitle-helpfulness' )->escaped()
						)
						// <dd>{net helpfulness score}</dd>
						. Html::element( 'dd', array(),
							$helpfulness > 0 ? '+' . $helpfulness : $helpfulness )
					// </dl>
					. Html::closeElement( 'dl' )
				// </dd>
				. Html::closeElement( 'dd' )
			// </dl>
			. Html::closeElement( 'dl' );

		// Activity section
		$activity = '';
		if ( $record[0]->af_last_status && $record[0]->af_last_status_timestamp ) {
			$activity .=
				// <p class="articleFeedbackv5-feedback-permalink-activity-status">
				Html::openElement( 'p', array(
						'class' => 'articleFeedbackv5-feedback-permalink-activity-status'
					) )
					// <span class="articleFeedbackv5-feedback-permalink-status">
					//   One of:
					//   {msg:articlefeedbackv5-permalink-status-hidden}
					//   {msg:articlefeedbackv5-permalink-status-unhidden}
					//   {msg:articlefeedbackv5-permalink-status-request}
					//   {msg:articlefeedbackv5-permalink-status-unrequest}
					//   {msg:articlefeedbackv5-permalink-status-declined}
					//   {msg:articlefeedbackv5-permalink-status-autohide}
					//   {msg:articlefeedbackv5-permalink-status-deleted}
					//   {msg:articlefeedbackv5-permalink-status-undeleted}
					//   {msg:articlefeedbackv5-permalink-status-autoflag}
					//   {msg:articlefeedbackv5-permalink-status-featured}
					//   {msg:articlefeedbackv5-permalink-status-unfeatured}
					//   {msg:articlefeedbackv5-permalink-status-resolved}
					//   {msg:articlefeedbackv5-permalink-status-unresolved}
					// </span>
					. Html::rawElement( 'span', array(
						'class' => 'articleFeedbackv5-feedback-permalink-status ' .
							'articleFeedbackv5-laststatus-' . $record[0]->af_last_status
						),
						wfMessage( 'articlefeedbackv5-permalink-status-' . $record[0]->af_last_status )
							->rawParams( ApiArticleFeedbackv5Utils::getUserLink( $record[0]->af_last_status_user_id ) )
							->rawParams( ApiArticleFeedbackv5Utils::renderTimeAgo( $record[0]->af_last_status_timestamp ) )
							->parse()
						)
				// </p>
				. Html::closeElement( 'p' );
			if ( $record[0]->af_last_status_notes ) {
				$activity .=
					// <p class="articleFeedbackv5-feedback-permalink-activity-note">
					//   {activity note}
					// </p>
					Html::element( 'p', array(
							'class' => 'articleFeedbackv5-feedback-permalink-activity-status'
						),
						$record[0]->af_last_status_notes
					);
			}
			$activity .=
				// <p class="articleFeedbackv5-feedback-permalink-activity-more">
				Html::openElement( 'p', array(
						'class' => 'articleFeedbackv5-feedback-permalink-activity-more',
					) )
					// <a href="#" class="articleFeedbackv5-activity-link">
					//   {msg:articlefeedbackv5-permalink-activity-more}
					// </a>
					. Html::rawElement( 'a', array(
							'href'  => '#',
							'class' => 'articleFeedbackv5-activity2-link'
						),
						wfMessage( 'articlefeedbackv5-permalink-activity-more' )->escaped()
					)
				// </p>
				. Html::closeElement( 'p' )
				// <div id="articleFeedbackv5-activity-log">
				. Html::openElement( 'div', array(
						'id' => 'articleFeedbackv5-permalink-activity-log'
					) )
				// </div>
				. Html::closeElement( 'div' );
		} else {
			$activity .=
				// <p class="articleFeedbackv5-feedback-permalink-activity-note">
				//   {msg:articlefeedbackv5-permalink-activity-none}
				// </p>
				Html::rawElement( 'p', array(
						'class' => 'articleFeedbackv5-feedback-permalink-activity-none',
					),
					wfMessage( 'articlefeedbackv5-permalink-activity-none')->escaped()
				);
		}

		// Frame and return
		return
			// <div id="articleFeedbackv5-feedback-permalink-info">
			Html::openElement( 'div', array(
				'id' => 'articleFeedbackv5-feedback-permalink-info'
			) )
				// <div class="articleFeedbackv5-feedback-permalink-about">
				. Html::openElement( 'div', array(
					'class' => 'articleFeedbackv5-feedback-permalink-about'
				) )
					// <h4>
					. Html::openElement( 'h4' )
						// {msg:articlefeedbackv5-permalink-info-title}
						. wfMessage( 'articlefeedbackv5-permalink-info-title' )->escaped()
						// <span>{msg:articlefeedbackv5-permalink-info-subtitle}</span>
						. Html::rawElement( 'span', array(),
							wfMessage( 'articlefeedbackv5-permalink-info-subtitle' )
								->params( $id )
								->escaped()
						)
					// </h4>
					. Html::closeElement( 'h4' )
					// {metadata section}
					. $metadata
					// {stats section}
					. $stats
				// </div>
				. Html::closeElement( 'div' )
				// <div class="articleFeedbackv5-feedback-permalink-activity">
				. Html::openElement( 'div', array(
					'class' => 'articleFeedbackv5-feedback-permalink-activity'
				) )
					// <h4>
					. Html::openElement( 'h4', array() )
						// {msg:articlefeedbackv5-permalink-activity-title}
						. wfMessage( 'articlefeedbackv5-permalink-activity-title' )->escaped()
						// <span>{msg:articlefeedbackv5-permalink-activity-subtitle}</span>
						. Html::rawElement( 'span', array(),
							wfMessage( 'articlefeedbackv5-permalink-activity-subtitle' )
								->params( $this->getActivityCount( $record[0] ) )
								->escaped()
						)
					// </h4>
					. Html::closeElement( 'h4' )
					// {activity section}
					. $activity
				// </div>
				. Html::closeElement( 'div' )

			// </div>
			. Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * Returns whether this thing has a toolbox
	 *
	 * @return bool
	 */
	public function hasToolbox() {
		if ( !$this->hasPermission( 'can_feature' )
			&& !$this->hasPermission( 'can_hide' )
			&& !$this->hasPermission( 'can_delete' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Returns the appropriate activity count
	 *
	 * @param  $record stdClass the record
	 * @return int the activity count
	 */
	public function getActivityCount( stdClass $record ) {
		$count = $record->af_activity_count;
		if ( $this->hasPermission( 'can_delete' ) ) {
			$count += $record->af_suppress_count;
		}
		return $count;
	}

}

