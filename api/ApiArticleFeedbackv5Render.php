<?php
/**
 * A more structured, machine-readable version of the ArticleFeedbackv5Render class
 *
 * @package    ArticleFeedback
 * @author     Jack Phoenix
 * @date       10 October 2023
 * @see        https://phabricator.wikimedia.org/T53296
 */

/**
 * Handles rendering of a submitted feedback entry in the MediaWiki action API
 *
 * @package    ArticleFeedback
 */
class ApiArticleFeedbackv5Render extends ArticleFeedbackv5Render {

	/**
	 * Returns the structured array for the given feedback entry
	 *
	 * @suppress PhanParamSignatureMismatch The signature mismatch here vs. parent class
	 *  (different return types) is intentional
	 * @param ArticleFeedbackv5Model $record the record
	 * @return array the rendered row
	 */
	public function run( $record ) {
		if ( !$record instanceof ArticleFeedbackv5Model ) {
			return [];
		}

		try {
			$record->validate();
		} catch ( Exception ) {
			return [];
		}

		// Special cases: when the record is deleted/hidden/inappropriate,
		// but the user doesn't have permission to see it
		if (
			( $record->isOversighted() && !ArticleFeedbackv5Activity::canPerformAction( 'oversight', $this->getUser() ) ) ||
			( $record->isHidden() && !ArticleFeedbackv5Activity::canPerformAction( 'hide', $this->getUser() ) ) ||
			( $record->isInappropriate() && !ArticleFeedbackv5Activity::canPerformAction( 'inappropriate', $this->getUser() ) )
		) {
			// Called via permalink: show an empty gray mask
			if ( $this->isPermalink ) {
				$error = '';
				if ( $record->isOversighted() ) {
					$error = 'oversight';
				} elseif ( $record->isHidden() ) {
					$error = 'hide';
				} elseif ( $record->isInappropriate() ) {
					$error = 'inappropriate';
				}

				$last = $record->getLastEditorActivity();
				if ( !$last ) {
					// if this happens, some data is corrupt
					return [ 'error' => $error ];
				}

				return [
					'error' => $error
				];
			} else {
				return [];
			}
		}

		// Get the status
		$status = [];
		$oversighted = $hidden = $featured = $resolved = $nonActionable = $inappropriate = $archived =
			$highlighted = false;
		if ( $record->isOversighted() ) {
			$status[] = 'oversighted';
			$oversighted = true;
		}
		if ( $record->isHidden() ) {
			$status[] = 'hidden';
			$hidden = true;
		}
		if ( $record->isFeatured() ) {
			$status[] = 'featured';
			$featured = true;
		}
		if ( $record->isResolved() ) {
			$status[] = 'resolved';
			$resolved = true;
		}
		if ( $record->isNonActionable() ) {
			$status[] = 'noaction';
			$nonActionable = true;
		}
		if ( $record->isInappropriate() ) {
			$status[] = 'inappropriate';
			$inappropriate = true;
		}
		if ( $record->isArchived() ) {
			$status[] = 'archived';
			$archived = true;
		}
		if ( $this->isHighlighted ) {
			$status[] = 'highlighted';
			$highlighted = true;
		}

		// get details on last editor action
		$last = false;
		$reviewStatus = [];
		if ( $this->isAllowed( 'aft-editor' ) ) {
			$last = $record->getLastEditorActivity();
			if ( $last ) {
				$reviewStatus = [
					// The user:
					'actioned-by-uid' => $last->log_user,
					'actioned-by-username' => $last->log_user_text,
					// took this action on the post
					// (one of the following: new, oversight, autohide, hide, feature, resolve, noaction, inappropriate, archive)
					'action' => $last->log_action,
					'actioned-timestamp' => $last->log_timestamp,
					'actioned-comment' => $last->log_comment
				];
			}
		}

		$retVal = [
			// Should not need these two...
			# 'id' => $record->aft_id,
			# 'page-id' => $record->aft_page
			'review-details' => ( $this->isAllowed( 'aft-editor' ) && $last ) ? $reviewStatus : [],
			'status' => implode( ' ', $status ),
			'oversighted' => $oversighted,
			'hidden' => $hidden,
			'featured' => $featured,
			'resolved' => $resolved,
			'nonActionable' => $nonActionable,
			'inappropriate' => $inappropriate,
			'archived' => $archived,
			'highlighted' => $highlighted,
			'oldid' => $record->aft_page_revision,
			// no special handling for IPs here...
			// @todo Should there be such handling?
			'username' => $record->aft_user_text,
			'timestamp' => $record->aft_timestamp,
			'mood' => $this->getMood( $record ),
			// Intentionally skipping the truncation etc. done by parent::renderComment()
			'text' => $record->aft_comment
		];

		// Permission check per renderPermalinkInfo()
		// @todo FIXME: this->isPermalink is ALWAYS false because that's how ApiViewFeedbackArticleFeedbackv5
		// initializes it in its constructor!
		// But surely we want to show this if an afvffeedbackid param is set and the user is
		// privileged enough? Unless we want to modify the isPermalink definition *for this subclass*...
		if ( /*$this->isPermalink &&*/ $this->isAllowed( 'aft-editor' ) ) {
			$retVal['helpful-votes'] = [
				'yes' => $record->aft_helpful,
				'no' => $record->aft_unhelpful,
				'percentage' => ArticleFeedbackv5Utils::percentHelpful(
					$record->aft_helpful,
					$record->aft_unhelpful
				)
			];
			$retVal['abuse-count'] = $record->aft_flag;
			$retVal['relevance'] = $record->aft_relevance_score;
		}

		return $retVal;
	}

}
