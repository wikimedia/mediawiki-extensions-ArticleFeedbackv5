<?php
/**
 * ApiFlagFeedbackArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 */

/**
 * This class pulls the individual ratings/comments for the feedback page.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiFlagFeedbackArticleFeedbackv5 extends ApiBase {
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, '' );
	}

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		$params    = $this->extractRequestParams();
		$pageId    = $params['pageid'];
		$feedbackId = $params['feedbackid'];
		$flag      = $params['flagtype'];
		$notes = $params['note'];
		$direction = isset( $params['direction'] ) ? $params['direction'] : 'increase';
		$counts    = array( 'increment' => array(), 'decrement' => array() );
		$counters  = array( 'abuse', 'helpful', 'unhelpful' );
		$flags     = array( 'oversight', 'hide', 'delete' );
		$results   = array();
		$helpful   = null;
		$error     = null;
		$where     = array( 'af_id' => $feedbackId );

		# load feedback record, bail if we don't have one
		$record = $this->fetchRecord( $feedbackId );

		if ( $record === false || !$record->af_id ) {
			// no-op, because this is already broken
			$error = 'articlefeedbackv5-invalid-feedback-id';
		} elseif ( in_array( $flag, $flags ) ) {

			$count = null;	
			switch( $flag ) {
				case 'hide':      
					$field = 'af_is_hidden';
					$count = 'invisible';
					break;
				case 'oversight':
					$field = 'af_needs_oversight';
					$count = 'needsoversight';
					break;
				case 'delete':
					$field = 'af_is_deleted';
					$count = 'deleted';
					break;
				default: return; # return error, ideally.
			}
			if( $direction == 'increase' ) {
				$update[] = "$field = TRUE";
			} else {
				$update[] = "$field = FALSE";
			}
			// Increment or decrement whichever flag is being set.
			$countDirection = $direction == 'increase' ? 'increment' : 'decrement';
			$counts[$countDirection][] = $count;
			// If this is hiding/deleting, decrement the visible count.
			if( ( $flag == 'hide' || $flag == 'delete' )
			 && $direction == 'increase' ) {
				$counts['decrement'][] = 'visible';
			}
			// If this is unhiding/undeleting, increment the visible count.
			if( ( $flag == 'hide' || $flag == 'delete' )
			 && $direction == 'decrease' ) {
				$counts['increment'][] = 'visible';
			}
		} elseif ( in_array( $flag, $counters ) ) {
			// Probably this doesn't need validation, since the API
			// will handle it, but if it's getting interpolated into
			// the SQL, I'm really wary not re-validating it.
			$field = 'af_' . $flag . '_count';

			// Add another where condition to confirm that 
			// the new flag value is at or above 0 (we use 
			// unsigned ints, so negatives cause errors.
			if( $direction == 'increase' ) {
				$update[] = "$field = $field + 1";
				// If this is already less than 0, 
				// don't do anything - it'll just 
				// throw a SQL error, so don't bother.  
				// Incrementing from 0 is still valid.
				$where[] = "$field >= 0";
			} else {
				$update[] = "$field = $field - 1";
				// If this is already 0 or less, 
				// don't decrement it, that would
				// throw an error. 
				// Decrementing from 0 is not allowed.
				$where[] = "$field > 0";
			}

			// Adding a new abuse flag: abusive++
			if( $flag == 'abuse' && $direction == 'increase'
			 && $record->af_abuse_count == 0 ) {
				$counts['increment'][] = 'abusive';
				// Auto-hide after 5 abuse flags.
				if( $record->af_abuse_count > 4 ) {
					$counts['increment'][] = 'invisible';
					$counts['decrement'][] = 'visible';
				}
			}
			// Removing the last abuse flag: abusive--
			if( $flag == 'abuse' && $direction == 'decrease'
			 && $record->af_abuse_count == 1 ) {
				$counts['decrement'][] = 'abusive';
				// Un-hide if we don't have 5 flags anymore
				if( $record->af_abuse_count == 5 ) {
					$counts['increment'][] = 'visible';
					$counts['decrement'][] = 'invisible';
				}
			}

			// note that a net helpfulness of 0 is neither helpful nor unhelpful
			$netHelpfulness = $record->af_net_helpfulness;

			// increase helpful OR decrease unhelpful
			if( ( ($flag == 'helpful' && $direction == 'increase' )
			 || ($flag == 'unhelpful' && $direction == 'decrease' ) )
			) {
				// net was -1: no longer unhelpful
				if( $netHelpfulness == -1 ) {
					$counts['decrement'][] = 'unhelpful';
				}

				// net was 0: now helpful
				if( $netHelpfulness == 0 ) {
					$counts['increment'][] = 'helpful';
				}
			}

			// increase unhelpful OR decrease unhelpful
			if( ( ($flag == 'unhelpful' && $direction == 'increase' )
			 || ($flag == 'helpful' && $direction == 'decrease' ) )
			) {
				// net was 1: no longer helpful
				if( $netHelpfulness == 1 ) {
					$counts['decrement'][] = 'helpful';
				}

				// net was 0: now unhelpful
				if( $netHelpfulness == 0 ) {
					$counts['increment'][] = 'unhelpful';
				}
			}
		} else {
			$error = 'articlefeedbackv5-invalid-feedback-flag';
		}

		if ( !$error ) {
			$dbw     = wfGetDB( DB_MASTER );
			$success = $dbw->update(
				'aft_article_feedback',
				$update,
				$where,
				__METHOD__
			);

			// If the query worked...
			if( $success ) {

				// Log the feedback activity entry via the utils method
				$activity = $this->getActivity( $flag, $direction );

				// Make sure our notes are not too long - we won't error, just hard substr it
				global $wgArticleFeedbackv5MaxCommentLength;

				// for some reason, 0 means no length checking (ARGH) - should be -1!
				if ($wgArticleFeedbackv5MaxCommentLength > 0) {
					$notes = substr($notes, 0, $wgArticleFeedbackv5MaxCommentLength);
				}

				ApiArticleFeedbackv5Utils::logActivity( $activity , $pageId, $feedbackId, $notes );

>>>>>>> .r111511
				// Update the filter count rollups.
				ApiArticleFeedbackv5Utils::incrementFilterCounts( $pageId, $counts['increment'] );
				ApiArticleFeedbackv5Utils::decrementFilterCounts( $pageId, $counts['decrement'] );

				// Update helpful/unhelpful display count after submission.
				if ( $flag == 'helpful' || $flag == 'unhelpful' ) {
					$helpful   = $record->af_helpful_count;
					$unhelpful = $record->af_unhelpful_count;

					if( $flag == 'helpful' && $direction == 'increase' ) {
						$helpful++;
					} elseif ( $flag == 'helpful' && $direction == 'decrease' ) {
						$helpful--;
					} elseif ( $flag == 'unhelpful' && $direction == 'increase' ) {
						$unhelpful++;
					} elseif ( $flag == 'unhelpful' && $direction == 'decrease' ) {
						$unhelpful--;
					}

					$results['helpful'] = wfMessage( 
						'articlefeedbackv5-form-helpful-votes',
						$helpful, $unhelpful
					)->escaped();

					// Update net_helpfulness after flagging as helpful/unhelpful.
					$dbw->update(
						'aft_article_feedback',
						array( 'af_net_helpfulness = CONVERT(af_helpful_count, SIGNED) - CONVERT(af_unhelpful_count, SIGNED)' ),
						array(
							'af_id' => $params['feedbackid'],
						),
						__METHOD__
					);
				}

			}

			// Conditional formatting for abuse flag
			global $wgArticleFeedbackv5AbusiveThreshold,
				$wgArticleFeedbackv5HideAbuseThreshold;

			$results['abuse_count'] = $record->af_abuse_count;

			if( $flag == 'abuse' ) {
				// Make the abuse count in the result reflect this vote.
				if( $direction == 'increase' ) {
					$results['abuse_count']++; 
				} else { 
					$results['abuse_count']--; 
				}

				// Return a flag in the JSON, that turns the link red.
				if( $results['abuse_count'] >= $wgArticleFeedbackv5AbusiveThreshold ) {
					$results['abusive'] = 1;
				}

				// Return a flag in the JSON, that knows to kill the row
				if( $results['abuse_count'] >= $wgArticleFeedbackv5HideAbuseThreshold ) {
					$results['abuse-hidden'] = 1;
				}	

				// Hide the row if the abuse count is above our threshhold
				$dbw->update(
					'aft_article_feedback',
					array( 'af_is_hidden = TRUE' ),
					array( 
						'af_id' => $params['feedbackid'],
						"af_abuse_count >= ". intval( $wgArticleFeedbackv5HideAbuseThreshold )
					),
					__METHOD__
				);
			}
		}

		if ( $error ) {
			$results['result'] = 'Error';
			$results['reason'] = $error;
		} else {
			$results['result'] = 'Success';
			$results['reason'] = null;
		}

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			$results
		);
	}

	private function fetchRecord( $id ) {
		$dbr    = wfGetDB( DB_SLAVE );
		$record = $dbr->selectRow(
			'aft_article_feedback',
			array( 'af_id', 'af_abuse_count', 'af_is_hidden', 'af_helpful_count', 'af_unhelpful_count', 'af_is_deleted', 'af_net_helpfulness' ),
			array( 'af_id' => $id )
		);
		return $record;
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return array(
			'pageid'     => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'feedbackid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'flagtype'   => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'abuse', 'hide', 'helpful', 'unhelpful', 'delete', 'undelete', 'unhide', 'oversight', 'unoversight' )
			),
			'direction' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array(
				 'increase', 'decrease' )
			),
			'note' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			)
		);
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		return array(
			'feedbackid'  => 'FeedbackID to flag',
			'type'        => 'Type of flag to apply - hide or abuse',
			'note'        => 'Information on why the feedback activity occurred'
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'Flag a feedbackID as abusive or hidden.'
		);
	}

	/**
	 * Gets any possible errors
	 *
	 * @return array the errors
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
				array( 'missingparam', 'anontoken' ),
				array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			)
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?list=articlefeedbackv5-view-feedback&affeedbackid=1&aftype=abuse',
		);
	}

	/**
	 * Figures out which activity happened so it can be logged correctly
	 *
	 * @param $flag      string type of flag sent to the form
	 * @param $direction string type of direction sent to the form
	 * @return string name of activity to log
	 */
	protected function getActivity($flag, $direction) {

		// handle flag as abuse / remove abuse flag
		if ( 'abuse' == $flag && 'increase' == $direction) {
			return 'flag';
		} elseif ( 'abuse' == $flag && 'decrease' == $direction) {
			return 'unflag';
		}

		// handle hide as hidden, unhidden
		if ( 'hide' == $flag && 'increase' == $direction) {
			return 'hidden';
		} elseif ( 'hide' == $flag && 'decrease' == $direction) {
			return 'unhidden';
		}

		// handle delete as oversight, unoversight
		if ( 'delete' == $flag && 'increase' == $direction) {
			return 'oversight';
		} elseif ( 'delete' == $flag && 'decrease' == $direction) {
			return 'unoversight';
		}

		// handle oversight as request and unrequest oversighting
		if ( 'oversight' == $flag && 'increase' == $direction) {
			return 'request';
		} elseif ( 'oversight' == $flag && 'decrease' == $direction) {
			return 'unrequest';
		}

		// TODO: how is "decline oversight" handled?
		// how should fall out the bottom here be handled?  a simple "feedback altered"?
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

	public function isWriteMode() { return true; }
	public function mustBePosted() { return true; }
}
