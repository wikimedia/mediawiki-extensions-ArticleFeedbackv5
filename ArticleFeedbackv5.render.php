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
	 */
	public function __construct( $user = null, $permalink = false, $central = false ) {
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
		if  ( $this->isPermalink && (
				( $record[0]->af_is_deleted && $this->hasPermission( 'see_deleted' ) )
				|| ( $record[0]->af_is_hidden && $this->hasPermission( 'see_hidden') )
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

		// Get the status line
		$status_line = '';
		$extra_class = '';
		if ( $record[0]->af_last_status && $record[0]->af_last_status_timestamp ) {
			$status_line = ApiArticleFeedbackv5Utils::renderStatusLine(
					$record[0]->af_last_status,
					$record[0]->af_last_status_user_id,
					$record[0]->af_last_status_timestamp
				);
			$extra_class = ' articleFeedbackv5-h3-push';
		}

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
				// {status line}
				. $status_line
				// <div class="articleFeedbackv5-comment-wrap {$extra_class}"
				. Html::openElement( 'div', array(
					'class' => "articleFeedbackv5-comment-wrap" . $extra_class
				) )
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
		$content = '';

		// hide or oversight?
		if ( $record[0]->af_is_deleted ) {
			$class = 'oversight';
		} else {
			$class = 'hidden';
		}

		// <div class="articleFeedbackv5-feedback
		//     articleFeedbackv5-feedback-{oversight|hidden}
		//     articleFeedbackv5-feedback-emptymask">
		$content .= Html::openElement( 'div',  array(
			'class' => 'articleFeedbackv5-feedback '
				. 'articleFeedbackv5-feedback-' . $class . ' '
				. 'articleFeedbackv5-feedback-emptymask'
			) )
			// <div class="articleFeedbackv5-post-screen">
			. Html::openElement( 'div', array(
					'class' => 'articleFeedbackv5-post-screen'
				) )
				// <div class="articleFeedbackv5-mask-text-wrapper">
				. Html::openElement( 'div', array(
						'class' => 'articleFeedbackv5-mask-text-wrapper'
					) )
					// <span class="articleFeedbackv5-mask-text">
					//  {msg:articlefeedbackv5-mask-text-{oversight|hidden}}
					// </span>
					. Html::element( 'span', array(
						'class' => 'articleFeedbackv5-mask-text'
						), wfMessage( 'articlefeedbackv5-mask-text-' . $class )->text())
					// <span class="articleFeedbackv5-mask-text">
					//  {msg:articlefeedbackv5-mask-text-{oversight|hidden}}
					// </span>
					. Html::element( 'span', array(
						'class' => 'articleFeedbackv5-mask-postid'
						), wfMessage( 'articlefeedbackv5-mask-postnumber' )
							->numParams($record[0]->af_id)
							->text()
						)
				// </div>
				. Html::closeElement( 'div' )
			// </div>
			. Html::closeElement( 'div' )
			// {{STATUS LINE}}
			. ApiArticleFeedbackv5Utils::renderStatusLine(
					$record[0]->af_last_status,
					$record[0]->af_last_status_user_id,
					$record[0]->af_last_status_timestamp
				)
			// <div class="articleFeedbackv5-comment-wrap articleFeedbackv5-h3-push">
			// </div>
			. Html::element( 'div', array(
				'class' => 'articleFeedbackv5-comment-wrap articleFeedbackv5-h3-push'
				) )
		// </div>
		. Html::closeElement( 'div' );

		return $content;
	}

	/**
	 * Returns the feedback head and comment for form #1
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderBucket1( $record ) {
		if ( isset( $record['found'] ) && $record['found']->aa_response_boolean == 1 ) {
			$msg   = 'articlefeedbackv5-form1-header-found';
			$class = 'positive';
		} elseif ( isset( $record['found'] ) && $record['found']->aa_response_boolean !== null ) {
			$msg   = 'articlefeedbackv5-form1-header-not-found';
			$class = 'negative';
		} else {
			$msg   = 'articlefeedbackv5-form1-header-left-comment';
			$class = '';
		}
		return $this->feedbackHead( $msg, $class, $record[0] )
			. $this->renderComment( $record['comment']->aa_response_text, $record[0]->af_id );
	}

	/**
	 * Returns the feedback head and comment for form #2
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderBucket2( $record ) {
		$type  = $record['tag']->afo_name;
		$class = $type == 'problem' ? 'negative' : 'positive';
		// Document for grepping. Uses any of the messages:
		// * articlefeedbackv5-form2-header-praise
		// * articlefeedbackv5-form2-header-problem
		// * articlefeedbackv5-form2-header-question
		// * articlefeedbackv5-form2-header-suggestion
		return $this->feedbackHead( "articlefeedbackv5-form2-header-$type", $class, $record[0], $type )
			. $this->renderComment( $record['comment']->aa_response_text, $record[0]->af_id );
	}

	/**
	 * Returns the feedback head and comment for form #3
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderBucket3( $record ) {
		$class = $record['rating']->aa_response_rating >= 3 ? 'positive' : 'negative';
		return $this->feedbackHead( 'articlefeedbackv5-form3-header', $class, $record[0], $record['rating']->aa_response_rating )
			. $this->renderComment( $record['comment']->aa_response_text, $record[0]->af_id );
	}

	/**
	 * Returns the feedback head and comment when the form is unknown
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderNoBucket( $record ) {
		return $this->feedbackHead( 'articlefeedbackv5-form-invalid', 'negative', $record[0] );
	}

	/**
	 * Returns the feedback head and comment for the central log
	 *
	 * @param  $record array the record, with keys 0 + answers
	 * @return string  the rendered feedback info
	 */
	private function renderCentral( $record ) {
		return $this->feedbackHead( 'articlefeedbackv5-central-header-left-comment', '', $record[0] )
			. $this->renderComment(
				isset( $record['comment'] ) ?  $record['comment']->aa_response_text : '',
				$record[0]->af_id
			);
	}

	/**
	 * Returns the feedback head
	 *
	 * @param  $message string   the message key describing the the nature of
	 *                           the feedback (e.g., "USER found what they were
	 *                           looking for")
	 * @param  $class   string   the class to add to the title
	 *                           (positive|negative), or an empty string
	 * @param  $record  stdClass the record (from the 0 index)
	 * @param  $extra   string   any extra info to send to the message
	 * @return string   the rendered feedback head
	 */
	private function feedbackHead( $message, $class, $record, $extra = '' ) {
		$name = htmlspecialchars( $record->user_name );

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

		// <span class="articleFeedbackv5-comment-details-updates">
		$details = Html::openElement( 'span', array(
			'class' => 'articleFeedbackv5-comment-details-updates'
		) );
		// <a href="{revision}">{msg:articlefeedbackv5-revision-link}</a>
		$details .=  Linker::link(
			Title::newFromRow( $record ),
			wfMessage( 'articlefeedbackv5-revision-link' )->escaped(),
			array(),
			array( 'oldid'  => $record->af_revision_id )
		);
		// </span>
		$details .= Html::closeElement( 'span' );

		if ( $this->isCentral ) {
			$article = Title::newFromRow($record);
			$parsedMsg = wfMessage( $message, $name )->rawParams(
					Linker::link( $title, $name )
				)->rawParams(
					Linker::link( $article )
				)->escaped();
		} else {
			$parsedMsg =
				// <span class="icon"></span>
				Html::element( 'span', array( 'class' => 'icon' ) )
				// <span class="result">{msg:$message}</span>
				. Html::rawElement( 'span',
					array( 'class' => 'result' ),
					wfMessage( $message, $name )->rawParams(
						Linker::link( $title, $name )
					)->escaped()
				);
		}

		return
			// <h3 class="{$class}">
			Html::openElement( 'h3', array( 'class' => $class ) )
				. $parsedMsg
			// </h3>
			. Html::closeElement( 'h3' )
			// {permalink/timestamp}
			. $this->renderPermalinkTimestamp( $record )
			// {link to revision}
			. $details;
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

		$blocks = array(
			array( 'total' => 60 * 60 * 24 * 365, 'name' => 'years' ),
			array( 'total' => 60 * 60 * 24 * 30, 'name' => 'months' ),
			array( 'total' => 60 * 60 * 24 * 7, 'name' => 'weeks' ),
			array( 'total' => 60 * 60 * 24, 'name' => 'days' ),
			array( 'total' => 60 * 60, 'name' => 'hours' ),
			array( 'total' => 60, 'name' => 'minutes' ) );

		$since = wfTimestamp( TS_UNIX ) - wfTimestamp( TS_UNIX, $record->af_created );
		$displayTime = 0;
		$displayBlock = '';

		// get the largest time block, 1 minute 35 seconds -> 2 minutes
		for ( $i = 0, $count = count( $blocks ); $i < $count; $i++ ) {
			$seconds = $blocks[$i]['total'];
			$displayTime = floor( $since / $seconds );

			if ( $displayTime > 0 ) {
				$displayBlock = $blocks[$i]['name'];
				// round up if the remaining time is greater than
				// half of the time unit
				if ( ( $since % $seconds ) >= ( $seconds / 2 ) ) {
					$displayTime++;

					// advance to upper unit if possible, eg, 24 hours to 1 day
					if ( isset( $blocks[$i -1] ) && $displayTime * $seconds ==  $blocks[$i -1]['total'] ) {
						$displayTime = 1;
						$displayBlock = $blocks[$i -1]['name'];
					}
				}
				break;
			}
		}

		$date = '';
		if ( $displayTime > 0 ) {
			if ( in_array( $displayBlock, array( 'years', 'months', 'weeks' ) ) ) {
				$messageKey = 'articlefeedbackv5-timestamp-' . $displayBlock;
			} else {
				$messageKey = $displayBlock;
			}
			$date = wfMessage( $messageKey )->params( $wgLang->formatNum( $displayTime ) )->escaped();
		} else {
			$date = wfMessage( 'articlefeedbackv5-timestamp-seconds' )->escaped();
		}

		$message = wfMessage( 'articleFeedbackv5-comment-ago', $date )->escaped();

		return
			// <span class="articleFeedbackv5-comment-details-date">
			Html::openElement( 'span', array(
				'class' => 'articleFeedbackv5-comment-details-date'
			) )
				// <a href="{permalink}">{msg:articleFeedbackv5-comment-ago}</a>
				. Linker::link(
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', "$title/$id" ),
					$message
				)
			// </span>
			. Html::closeElement( 'span' );
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

		$short = $wgLang->truncate( $text, 500 );

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

		// Add helpful/unhelpful voting counts
		$footer .=
			// <span class="articleFeedbackv5-helpful-votes"
			//   id="articleFeedbackv5-helpful-votes-{$id}">
			//   {msg:articlefeedbackv5-form-helpful-votes}
			// </span>
			Html::element( 'span', array(
				'class' => 'articleFeedbackv5-helpful-votes',
				'id'    => "articleFeedbackv5-helpful-votes-$id"
			), wfMessage( 'articlefeedbackv5-form-helpful-votes',
				$wgLang->formatNum( $record[0]->af_helpful_count ),
				$wgLang->formatNum( $record[0]->af_unhelpful_count )
			)->text() );

		// </div>
		$footer .= Html::closeElement( 'div' );

		// Add abuse flagging
		if ( $this->hasPermission( 'can_flag' ) ) {
			$aclass = 'articleFeedbackv5-abuse-link';
			global $wgArticleFeedbackv5AbusiveThreshold;
			if ( $record[0]->af_abuse_count >= $wgArticleFeedbackv5AbusiveThreshold ) {
				$aclass .= ' abusive';
			}
			// Count masked if user cannot hide comments (as per Fabrice)
			// Message can be:
			//  * articlefeedbackv5-form-abuse
			//  * articlefeedbackv5-form-abuse-masked
			$msg = 'articlefeedbackv5-form-abuse';
			if ( !$this->hasPermission( 'can_hide' ) ) {
				$msg .= '-masked';
			}
			// <a id="articleFeedbackv5-abuse-link-{$id}"
			//   class="articleFeedbackv5-abuse-link{-abusive?}"
			//   href="#" rel="{abuse count}">
			//   {msg:articlefeedbackv5-form-abuse{-masked?}}
			// </a>
			$footer .= Html::element( 'a', array(
				'id'    => "articleFeedbackv5-abuse-link-$id",
				'class' => $aclass,
				'href'  => '#',
				'rel'   => $record[0]->af_abuse_count
			), wfMessage( $msg, $wgLang->formatNum( $record[0]->af_abuse_count ) )->text() );
		}

		// </div>
		$footer .= Html::closeElement( 'div' );

		return $footer;
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
		if ( !$this->hasPermission( 'can_feature' )
			&& !$this->hasPermission( 'can_hide' )
			&& !$this->hasPermission( 'can_delete' ) ) {
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
				// <h3 id="articleFeedbackv5-feedback-tools-header-{$id}">
				//   {msg:articlefeedbackv5-form-tools-label}
				// </h3>
				. Html::element( 'h3', array(
					'id' => 'articleFeedbackv5-feedback-tools-header-' . $id
				), wfMessage( 'articlefeedbackv5-form-tools-label' )->text() )
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

			// Unresolve always appears if the item is resolved
			if ( $record[0]->af_is_resolved ) {
				// <li>
				//   <a id="articleFeedbackv5-{unresolve}-link-{$id}"
				//     class="articleFeedbackv5-{unresolve}-link" href="#">
				//     {msg:articlefeedbackv5-form-{unresolve}}
				//   </a>
				// </li>
				$toolsFeature .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-unresolve-link-$id",
					'class' => "articleFeedbackv5-unresolve-link",
					'href' => '#',
				), wfMessage( "articlefeedbackv5-form-unresolve" )->text() ) );
			}

			// Resolve appears if item is featured and not resolved
			if ( $record[0]->af_is_featured && !$record[0]->af_is_resolved ) {
				// <li>
				//   <a id="articleFeedbackv5-{resolve}-link-{$id}"
				//     class="articleFeedbackv5-{resolve}-link" href="#">
				//     {msg:articlefeedbackv5-form-{resolve}}
				//   </a>
				// </li>
				$toolsFeature .= Html::rawElement( 'li', array(), Html::element( 'a', array(
					'id'    => "articleFeedbackv5-resolve-link-$id",
					'class' => "articleFeedbackv5-resolve-link",
					'href' => '#',
				), wfMessage( "articlefeedbackv5-form-resolve" )->text() ) );
			}
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
		if ( $this->hasPermission( 'can_delete' ) ) {
			// if no activity has been logged yet, add the "inactive" class so we can display it accordingly
			$activityClass = "articleFeedbackv5-activity-link";
			if ( !$record[0]->log_timestamp ) {
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
		global $wgLang;
		$id = $record[0]->af_id;
		$html = '';

		if ( $this->hasPermission( 'see_hidden' ) ) {
			// Info section
			$html .=
				// <div id="articleFeedbackv5-feedback-permalink-info">
				Html::openElement( 'div', array(
					'class' => 'articleFeedbackv5-feedback-permalink-info'
				) )
					// <p>Posted on: {date}</p>
					. Html::rawElement( 'p', array(),
						wfMessage( 'articlefeedbackv5-permalink-info-posted' )
							->params( $wgLang->date( $record[0]->af_created ), $wgLang->time( $record[0]->af_created ) )
							->escaped()
					);
			if ( isset( $record['comment'] ) ) {
				$html .=
					// <p>Length: {count} words - {count} characters</p>
					Html::rawElement( 'p', array(),
						wfMessage( 'articlefeedbackv5-permalink-info-length' )
							->params(
								str_word_count( $record['comment']->aa_response_text ),
								strlen( $record['comment']->aa_response_text )
							)
							->escaped()
					);
			}
			$html .=
				// <p>Scores: {score} relevance, {score} helpfulness</p>
				Html::rawElement( 'p', array(),
					wfMessage( 'articlefeedbackv5-permalink-info-scores' )
						->params( $record[0]->af_relevance_score, $record[0]->af_net_helpfulness )
						->escaped()
					)
				// <p>Post #{id} - Feedback form {experiment}</p>
				. Html::rawElement( 'p', array(),
					wfMessage( 'articlefeedbackv5-permalink-info-number' )
						->params( $record[0]->af_id, $record[0]->af_experiment )
						->escaped()
					)
				// </div>
				. Html::closeElement( 'div' );
		}

		// Link back to article
		$page_title = Title::newFromRow($record[0])->getPrefixedText();
		$html .=
			// <div id="articleFeedbackv5-feedback-permalink-goback">
			//   <a href="{$page_title}">{msg:articlefeedbackv5-special-goback}</a>
			// </div>
			Html::rawElement( 'div', array(
					'id' => 'articleFeedbackv5-feedback-permalink-goback'
				), Linker::link(
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page_title ),
					wfMessage( 'articlefeedbackv5-special-goback' )->escaped()));

		return $html;
	}

}

