<?php
/**
 * ApiArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

/**
 * This saves the ratings
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiArticleFeedbackv5 extends ApiBase {
	// Cache this, so we don't have to look it up every time.
	private $revision_limit = null;

	// Allow auto-flagging of feedback
	private $autoFlag = array();

	// Warn for abuse?
	private $warnForAbuse = false;

	// filters incremented on creation
	protected $filters = array( 'visible' => 1, 'notdeleted' => 1, 'all' => 1);

	/**
	 * Constructor
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	/**
	 * Execute the API call: Save the form values
	 */
	public function execute() {
		global $wgUser, $wgArticleFeedbackv5SMaxage,
			$wgArticleFeedbackv5AbuseFiltering;

		$params = $this->extractRequestParams();

		wfProfileIn( __METHOD__ );

		// Blocked users are, well, blocked.
		if ( $wgUser->isBlocked() ) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-error-blocked' )->escaped(),
				'userblocked'
			);
		}

		// Check if feedback is enabled on this page
		if ( !ApiArticleFeedbackv5Utils::isFeedbackEnabled( $params ) ) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-page-disabled' )->escaped(),
				'invalidpage'
			);
		}

		$dbw          = wfGetDB( DB_MASTER );
		$pageId       = $params['pageid'];
		$bucket       = $params['bucket'];
		$revisionId   = $params['revid'];
		$userAnswers  = array();
		$fields       = ApiArticleFeedbackv5Utils::getFields();
		$emailData    = array(
			'ratingData' => array(),
			'pageID'     => $pageId,
			'bucketId'   => $bucket
		);

		// Validate the response data
		foreach ( $fields as $field ) {
			$field_name = $field['afi_name'];
			if ( $field['afi_bucket_id'] != $bucket ) {
				continue;
			}
			if ( isset( $params[$field['afi_name']] ) ) {
				$value = $params[$field_name];
				$type  = $field['afi_data_type'];
				if ( $value === '' ) {
					continue;
				}
				if ( !$this->validateParam( $value, $type, $field['afi_id'], $pageId ) ) {
					$this->dieUsage(
						$this->msg( 'articlefeedbackv5-error-validation' )->escaped(),
						'paramvalidationfailed'
					);
				}
				if ( $wgArticleFeedbackv5AbuseFiltering && 'text' == $type && $this->findAbuse( $value, $pageId ) ) {
					if ( $this->warnForAbuse ) {
						$this->dieUsage(
							$this->warnForAbuse,
							'afwarn'
						);
					} else {
						$target = $this->msg( 'articlefeedbackv5-error-abuse-link' )->inContentLanguage()->plain();

						$this->dieUsage(
							$this->msg( 'articlefeedbackv5-error-abuse', $target )->parse(),
							'afreject'
						);
					}
				}
				$data = array( 'aa_field_id' => $field['afi_id'] );
				foreach ( array( 'rating', 'text', 'boolean', 'option_id' ) as $t ) {
					$data["aa_response_$t"] = $t == $type ? $value : null;
				}
				$userAnswers[] = $data;
				$emailData['ratingData'][$field_name] = $value;
			}
		}

		// all write actions should be done under the same transaction
		// or counts will be messed up
		$dbw->begin();

		// Save the response data
		$ratingIds  = $this->saveUserRatings( $dbw, $userAnswers, $bucket, $params );
		$feedbackId = $ratingIds['feedback_id'];
		$this->saveUserProperties( $feedbackId );

		// Per Fabrice 1/25, FeedbackPage only cares about options 1 and six,
		// so don't bother updating the rollups if this is a different one.
		if ( $bucket == 1 || $bucket == 6 ) {
			$this->updateRollupTables( $pageId, $revisionId, $userAnswers );
			$this->updateFilterCounts( $dbw, $pageId, $userAnswers );
		}

		// at this point we're done doing db writes, if we haven't rolled back, commit
		$dbw->commit();

		// If we have an email address, capture it
		if ( $params['email'] ) {
			$this->captureEmail ( $params['email'], FormatJson::encode(
				$emailData
			) );
		}

		// @todo wfAppendQuery() is deprecated. Use Uri class.
		$squidUpdate = new SquidUpdate( array(
			wfAppendQuery( wfScript( 'api' ), array(
				'action'       => 'query',
				'format'       => 'json',
				'list'         => 'articlefeedbackv5-view-ratings',
				'afpageid'     => $pageId,
				'maxage'       => 0,
				'smaxage'      => $wgArticleFeedbackv5SMaxage
			) )
		) );
		$squidUpdate->doUpdate();

		wfRunHooks( 'ArticleFeedbackChangeRating', array( $params ) );

		// Are we set to auto-flag?
		$flagger = new ArticleFeedbackv5Flagging( null, $feedbackId );
		foreach ( $this->autoFlag as $flag => $rule_desc ) {
			$msg = 'articlefeedbackv5-abusefilter-note-aftv5';
			if ( $flag == 'abuse' ) {
				$msg .= 'flagabuse';
			} elseif ( $flag == 'hide' ) {
				$msg .= 'hide';
			} elseif ( $flag == 'oversight' ) {
				$msg .= 'requestoversight';
			} else {
				continue;
			}
			$notes = $this->msg( $msg, array( $rule_desc ) )->parse();
			$res = $flagger->run( $flag, $notes );
			if ( 'Error' == $res['result'] ) {
				// TODO: Log somewhere?
			}
		}

		// build url to permalink and special page
		$page = Title::newFromID( $pageId );
		$stitle = Title::newFromText( "ArticleFeedbackv5/$page", NS_SPECIAL );
		$aftUrl = $stitle->getLinkUrl( array( 'ref' => 'cta' ) );
		$ptitle = Title::newFromText( "ArticleFeedbackv5/$page/$feedbackId", NS_SPECIAL );
		$permalink = $ptitle->getLinkUrl( array( 'ref' => 'cta' ) );

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array(
				'result'      => 'Success',
				'feedback_id' => $feedbackId,
				'aft_url'     => $aftUrl,
				'permalink'   => $permalink,
			)
		);

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Option 5 only: Capture the user's email address
	 *
	 * @param $email string the email address
	 * @param $json  string the info to send with it, as JSON
	 */
	protected function captureEmail( $email, $json ) {
		# http://www.mediawiki.org/wiki/API:Calling_internally
		$params = new FauxRequest( array(
			'action' => 'emailcapture',
			'format' => 'json',
			'email'  => $email,
			'info'   => $json
		) );
		$api = new ApiMain( $params, true );
		$api->execute();
	}

	/**
	 * Validates a value against a field type
	 *
	 * @param  $value    mixed  the value (reference, as option_id switches out
	 *                          text for the id)
	 * @param  $type     string the field type
	 * @param  $field_id int    the field id
	 * @param  $pageId   int    the page id  (needed by abuse filter)
	 * @return bool      whether this is okay
	 */
	protected function validateParam( &$value, $type, $field_id, $pageId ) {
		# rating: int between 1 and 5 (inclusive)
		# boolean: 1 or 0
		# option_id: option exists
		# text: none
		switch ( $type ) {
			case 'rating':
				if ( preg_match( '/^(1|2|3|4|5)$/', $value ) ) {
					return true;
				}
				break;
			case 'boolean':
				if ( preg_match( '/^(1|0)$/', $value ) ) {
					return true;
				}
				break;
			case 'option_id':
				$options = ApiArticleFeedbackv5Utils::getOptions();
				if ( !isset( $options[$field_id] ) ) {
					return false;
				}
				$flip = array_flip( $options[$field_id] );
				if ( isset( $flip[$value] ) ) {
					$value = $flip[$value];
					return true;
				}
				break;
			case 'text':
				# Not actually a requirement, but I can see this being a thing,
				# not letting people post the entire text of 1984 in a comment
				# or something like that.
				global $wgArticleFeedbackv5MaxCommentLength;
				if ( $wgArticleFeedbackv5MaxCommentLength > 0
					&& strlen( $value ) > $wgArticleFeedbackv5MaxCommentLength ) {
					return false;
				}
				return true;
			default:
				return false;
		}
		return false;
	}

	/**
	 * Check for abusive or spammy content
	 *
	 * Check the following in sequence (cheapest processing to most expensive,
	 * returning if we get a hit):
	 *  1) Respect $wgSpamRegex
	 *  2) Check SpamBlacklist
	 *  3) Check AbuseFilter
	 *
	 * @param $value  string the text to check
	 * @param $pageId int    the page ID
	 */
	private function findAbuse( &$value, $pageId ) {
		// Respect $wgSpamRegex
		global $wgSpamRegex;
		if ( ( is_array( $wgSpamRegex ) && count( $wgSpamRegex ) > 0 )
			|| ( is_string( $wgSpamRegex ) && strlen( $wgSpamRegex ) > 0 ) ) {
			// In older versions, $wgSpamRegex may be a single string rather than
			// an array of regexes, so make it compatible.
			$regexes = ( array ) $wgSpamRegex;
			foreach ( $regexes as $regex ) {
				if ( preg_match( $regex, $value ) ) {
					return true;
				}
			}
		}

		// Check SpamBlacklist, if installed
		if ( function_exists( 'wfSpamBlacklistObject' ) ) {
			$spam = wfSpamBlacklistObject();
		} elseif ( class_exists( 'BaseBlacklist' ) ) {
			$spam = BaseBlacklist::getInstance( 'spam' );
		}
		if ( $spam ) {
			$title = Title::newFromText( 'ArticleFeedbackv5_' . $pageId );
			$ret = $spam->filter( $title, $value, '' );
			if ( $ret !== false ) {
				return true;
			}
		}

		// Check AbuseFilter, if installed
		if ( class_exists( 'AbuseFilter' ) ) {
			global $wgUser;

			// Set up variables
			$title = Title::newFromID( $pageId );
			$vars = new AbuseFilterVariableHolder;
			$vars->addHolder( AbuseFilter::generateUserVars( $wgUser ) );
			$vars->addHolder( AbuseFilter::generateTitleVars( $title , 'ARTICLE' ) );
			$vars->setVar( 'SUMMARY', 'Article Feedback 5' );
			$vars->setVar( 'ACTION', 'feedback' );
			$vars->setVar( 'new_wikitext', $value );
			$vars->setLazyLoadVar( 'new_size', 'length', array( 'length-var' => 'new_wikitext' ) );

			// Add custom action handlers
			global $wgAbuseFilterCustomActionsHandlers;
			$flagCallback = array( $this, 'callbackAbuseActionFlag' );
			$wgAbuseFilterCustomActionsHandlers['aftv5flagabuse'] = $flagCallback;
			// Not for this release
			// $wgAbuseFilterCustomActionsHandlers['aftv5hide'] = $flagCallback;
			// $wgAbuseFilterCustomActionsHandlers['aftv5requestoversight'] = $flagCallback;

			// Check the filters (mimics AbuseFilter::filterAction)
			global $wgArticleFeedbackv5AbuseFilterGroup;
			$vars->setVar( 'context', 'filter' );
			$vars->setVar( 'timestamp', time() );
			$results = AbuseFilter::checkAllFilters( $vars, $wgArticleFeedbackv5AbuseFilterGroup );
			if ( count( array_filter( $results ) ) == 0 ) {
				return false;
			}

			// Abuse filter consequences
			$matched = array_keys( array_filter( $results ) );
			list( $actions_taken, $error_msg ) = AbuseFilter::executeFilterActions(
				$matched, $title, $vars );

			// Send to the abuse filter log
			$dbr = wfGetDB( DB_SLAVE );
			global $wgRequest;
			$log_template = array(
				'afl_user' => $wgUser->getId(),
				'afl_user_text' => $wgUser->getName(),
				'afl_timestamp' => $dbr->timestamp( wfTimestampNow() ),
				'afl_namespace' => $title->getNamespace(),
				'afl_title' => $title->getDBkey(),
				'afl_ip' => $wgRequest->getIP()
			);
			$action = $vars->getVar( 'ACTION' )->toString();
			AbuseFilter::addLogEntries( $actions_taken, $log_template, $action, $vars );

			// Local consequences (right now: disallow only)
			$disallow = false;
			$warn = false;
			foreach ( $actions_taken as $id => $actions ) {
				foreach ( $actions as $action ) {
					if ( 'disallow' == $action) {
						$disallow = true;
					}
					if ( 'warn' == $action ) {
						$warn = true;
					}
				}
			}
			if ( $warn ) {
				$this->warnForAbuse = $error_msg;
				return true;
			}
			if ( $disallow ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * AbuseFilter callback: flag feedback (abuse, oversight, hide, etc.)
	 *
	 * @param string                    $action     the action name (AF)
	 * @param array                     $parameters the action parameters (AF)
	 * @param Title                     $title      the title passed in
	 * @param AbuseFilterVariableHolder $vars       the variables passed in
	 * @param string                    $rule_desc  the rule description
	 */
	public function callbackAbuseActionFlag( $action, $parameters,
		$title, $vars, $rule_desc ) {
		switch ( $action ) {
			case 'aftv5flagabuse':
				$this->autoFlag['abuse'] = $rule_desc;
				break;
			case 'aftv5hide':
				$this->autoFlag['hide'] = $rule_desc;
				break;
			case 'aftv5requestoversight':
				$this->autoFlag['oversight'] = $rule_desc;
				break;
			default:
				// Fall through silently
				break;
		}
	}

	public function updateFilterCounts( $dbw, $pageId, $answers ) {

		$filters = $this->filters;

		// if this record has a comment attached then increment comment as well
		// notice we do not need to walk the entire array, since any one hit
		// counts - aa_response_text is "comment" in the values
		foreach ( $answers as $a ) {
			if ( $a['aa_response_text'] !== null ) {
				$filters['visible-comment'] = 1;
				// having a comment makes a new feedback item "relevant"
				$filters['visible-relevant'] = 1;
				break;
			}
		}

		ApiArticleFeedbackv5Utils::updateFilterCounts( $dbw, $pageId, $filters );
	}

	/**
	 * Update the rollup tables
	 *
	 * @param $page     int   the page id
	 * @param $revision int   the revision id
	 * @param $data     array the user's validated feedback answers
	 */
	public function updateRollupTables( $page, $revision, $data ) {
		foreach ( array( 'rating', 'boolean', 'option_id' ) as $type ) {
			$this->updateRollup( $page, $revision, $type, $data );
		}
	}

	/**
	 * Cache result of ApiArticleFeedbackv5Utils::getRevisionLimit to avoid
	 * multiple fetches.
	 *
	 * @param  $pageID int the page id
	 * @return int     the oldest revision to still count
	 */
	public function getRevisionLimit( $pageId ) {
		if ( $this->revision_limit === null ) {
			$this->revision_limit = ApiArticleFeedbackv5Utils::getRevisionLimit( $pageId );
		}
		return $this->revision_limit;
	}

	/**
	 * Update the rollup tables
	 *
	 * @param $page     int    the page id
	 * @param $revision int    the revision id
	 * @param $type     string the type (rating, select, or boolean)
	 * @param $raw_data array  the user's validated feedback answers
	 *
	 * This should:
	 * 0. Attempt to insert a blank revision rollup row for each $data of type $type, based on revId, fieldId.
	 * 1. Increment said revision rollup for each $data of type $type, based on revId, fieldId, and value
	 * 2. Re-calculate the page value, across the last [X] revisions (an old revision, or more, may have moved outside of the wgArticleFeedbackv5RatingLifetime window, so we can't just increment the page level rollups - revision-level, absolutely)
	 *
	 */
	private function updateRollup( $pageId, $revId, $type, $raw_data ) {
		wfProfileIn( __METHOD__ );

		# sanity check
		if ( $type != 'rating' && $type != 'option_id' && $type != 'boolean' ) {
			wfProfileOut( __METHOD__ );
			return;
		}

		// Strip out the data not of this type.
		foreach ( $raw_data as $row ) {
			if ( $row["aa_response_$type"] != null ) {
				$this->updateRollupRow( $pageId, $revId, $type, $row );
			}
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Update the rollup tables
	 *
	 * @param $page     int    the page id
	 * @param $revision int    the revision id
	 * @param $type     string the type (rating, select, or boolean)
	 * @param $row      array  a user's validated feedback answer
	 *
	 * This should:
	 * 0. Attempt to insert a blank revision rollup row, based on revId, fieldId.
	 * 1. Increment said revision rollup, based on revId, fieldId, and value
	 * 2. Re-calculate the page rolup value, across the last [X] revisions (an old revision, or more, may have moved outside of the wgArticleFeedbackv5RatingLifetime window, so we can't just increment the page level rollups - revision-level, absolutely)
	 *
	 */
	private function updateRollupRow( $pageId, $revId, $type, $row ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$dbw   = wfGetDB( DB_MASTER );
		$limit = $this->getRevisionLimit( $pageId );
		$field = $row['aa_field_id'];
		$value = $row["aa_response_$type"];

		if ( $type == 'option_id' ) {
			// Selects are kind of a odd bird. We store one row
			// per option per field, and each one has the number
			// of times that option was chosen, and the number of
			// times the question was shown in total. So, you'd
			// have 1/10, 2/10, 7/10, eg. We increment the times
			// chosen on the one that was chosen, and the times
			// shown on all of them.

			// Fetch all the options for this field.
			$options = $dbr->select(
				'aft_article_field_option',
				array( 'afo_option_id' ),
				array( 'afo_field_id' => $field ),
				__METHOD__
			);

			// For each option this field has, make sure we have
			// a row by inserting one - will fail silently if the
			// row already exists.
			foreach ( $options as $option ) {
				// These inserts could possibly fail or succeed
				// individually, so we can't use the multiple-
				// insert functionality of the insert class.
				$dbw->insert(
					'aft_article_revision_feedback_select_rollup',
					array(
						'arfsr_page_id'     => $pageId,
						'arfsr_revision_id' => $revId,
						'arfsr_field_id'    => $field,
						'arfsr_option_id'   => $option->afo_option_id,
						'arfsr_total'       => 0,
						'arfsr_count'       => 0,
					),
					__METHOD__,
					array( 'IGNORE' )
				);
			}

			// Increment number of picks for this option.
			$dbw->update(
				'aft_article_revision_feedback_select_rollup',
				array(
					'arfsr_total = arfsr_total + 1',
				),
				array(
					'arfsr_page_id'     => $pageId,
					'arfsr_revision_id' => $revId,
					'arfsr_field_id'    => $field,
					'arfsr_option_id'   => $value,
				),
				__METHOD__
			);
			// Increment count for ALL options on this field.
			$dbw->update(
				'aft_article_revision_feedback_select_rollup',
				array(
					'arfsr_count = arfsr_count + 1',
				),
				array(
					'arfsr_page_id'     => $pageId,
					'arfsr_revision_id' => $revId,
					'arfsr_field_id'    => $field,
				),
				__METHOD__
			);
		} else {
			// Make sure we have a row by inserting one - will fail
			// silently if the row already exists.
			$dbw->insert(
				'aft_article_revision_feedback_ratings_rollup',
				array(
					'afrr_page_id'     => $pageId,
					'afrr_revision_id' => $revId,
					'afrr_field_id'    => $field,
					'afrr_total'       => 0,
					'afrr_count'       => 0,
				),
				__METHOD__,
				array( 'IGNORE' )
			);

			// Update total rating, and increment count.
			$dbw->update(
				'aft_article_revision_feedback_ratings_rollup',
				array(
					"afrr_total = afrr_total + " . intval( $value ),
					"afrr_count = afrr_count + 1",
				),
				array(
					'afrr_page_id'     => $pageId,
					'afrr_revision_id' => $revId,
					'afrr_field_id'    => $field,
				),
				__METHOD__
			);
		}

		// Revision rollups being done, we update the page rollups.
		// These are built off of the revision rollups, and only
		// count revisions back to the user-specified limit, so
		// they need to be recalculated every time, since we don't
		// know what revision we're dealing with, or how many times
		// a page has changed since the last feedback.

		// Select rollup data for revisions, grouped up by field, so we
		// can drop it into the page rollups.
		if ( $type == 'option_id' ) {
			$table  = 'aft_article_feedback_select_rollup';
			$prefix = 'afsr_';
			$rows   = $dbr->select(
				'aft_article_revision_feedback_select_rollup',
				array(
					'arfsr_option_id',
					'SUM(arfsr_total) AS total',
					'SUM(arfsr_count) AS count'
				),
				array(
					'arfsr_page_id'     => $pageId,
					"arfsr_revision_id > " . intval( $limit ),
					'arfsr_field_id'    => $field
				),
				__METHOD__,
				array( 'GROUP BY' => 'arfsr_option_id' )
			);

			$page_data = array();
			foreach ( $rows as $row ) {
				$page_data[] = array(
					'afsr_page_id'   => $pageId,
					'afsr_field_id'  => $field,
					'afsr_option_id' => $row->arfsr_option_id,
					'afsr_total'     => $row->total,
					'afsr_count'     => $row->count
				);
			}
		} else {
			$table  = 'aft_article_feedback_ratings_rollup';
			$prefix = 'arr_';
			$row    = $dbr->selectRow(
				'aft_article_revision_feedback_ratings_rollup',
				array(
					'SUM(afrr_total) AS total',
					'SUM(afrr_count) AS count'
				),
				array(
					'afrr_page_id'     => $pageId,
					"afrr_revision_id > " . intval( $limit ),
					'afrr_field_id'    => $field
				),
				__METHOD__
			);

			$page_data = array(
				'arr_page_id'  => $pageId,
				'arr_field_id' => $field,
				'arr_total'    => $row->total,
				'arr_count'    => $row->count
			);
		}

		$dbw->begin();
		// Delete the existing page rollup rows.
		$dbw->delete( $table, array(
			$prefix . 'page_id'     => $pageId,
			$prefix . 'field_id'    => $field
		), __METHOD__ );

		// Insert the new ones.
		$dbw->insert( $table, $page_data, __METHOD__, array( 'IGNORE' ) );
		$dbw->commit();

		// One way to speed this up would be to purge old rows from
		// the revision_rollup tables, as soon as they're out of the
		// window in which we count them. 30 revisions per page is still
		// a lot, but it'd be better than this, which has no limit and
		// will only get larger over time.
	}

	/**
 	 * Creates a new feedback record and inserts the user's rating
	 * for a specific revision
	 *
	 * @param  array $data       the data
	 * @param  int   $feedbackId the feedback id
	 * @param  int   $bucket     the bucket id
	 * @return int   the cta id
	 */
	private function saveUserRatings( $dbw, $data, $bucket, $params ) {
		global $wgUser, $wgArticleFeedbackv5LinkBuckets, $wgLanguageCode;
		$lang = Language::factory( $wgLanguageCode );

		$ctaId      = $params['cta'];
		$revId      = $params['revid'];
		$bucket     = $params['bucket'];
		$experiment = $params['experiment'];
		$linkName   = $params['link'];
		$token      = $this->getAnonToken( $params );
		$timestamp  = $dbw->timestamp();
		$ip         = null;

		if ( !$wgUser ) {
			$this->dieUsage( 'User info is missing', 'missinguser' );
		}

		wfProfileIn( __METHOD__ );

		// Only save IP address if the user isn't logged in.
		if ( !$wgUser->isLoggedIn() ) {
			global $wgRequest;
			$ip = $wgRequest->getIP();
		}

		// Make sure we have a page ID
		if ( !$params['pageid'] ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( 'Page ID is missing or invalid', 'invalidpageid' );
		}

		// Fetch this if it wasn't passed in
		if ( !$revId ) {
			$title = Title::newFromID( $params['pageid'] );
			if ( !$title ) {
				wfProfileOut( __METHOD__ );
				$this->dieUsage( 'Page ID is missing or invalid', 'invalidpageid' );
			}
			$revId = $title->getLatestRevID();
		}

		// Find the link ID using the order of the link buckets ('X' = 0, 'A' = 1,
		// 'B' = 2, etc.)
		$links = array_flip( array_keys( $wgArticleFeedbackv5LinkBuckets['buckets'] ) );
		$linkId = isset( $links[$linkName] ) ? $links[$linkName] : 0;

		$has_comment = false;
		foreach ( $data as $a ) {
			if ( $a['aa_response_text'] !== null ) {
				$has_comment = true;
				break;
			}
		}


		$dbw->insert( 'aft_article_feedback', array(
			'af_page_id'         => $params['pageid'],
			'af_revision_id'     => $revId,
			'af_created'         => $timestamp,
			'af_user_id'         => $wgUser->getId(),
			'af_user_ip'         => $ip,
			'af_user_anon_token' => $token,
			'af_form_id'         => $bucket,
			'af_experiment'      => $experiment,
			'af_link_id'         => $linkId,
			'af_has_comment'     => $has_comment,
		) );

		$feedbackId = $dbw->insertID();

		foreach ( $data as $key => $item ) {
			$data[$key]['aa_feedback_id'] = $feedbackId;
			$data[$key]['aat_id'] = null;

			// move large texts to another table
			if ( strlen( $data[$key]['aa_response_text'] ) > 255 ) {
				// save full text in other table
				$dbw->insert( 'aft_article_answer_text', array(
					'aat_response_text' => $data[$key]['aa_response_text']
				) );
				$aatId = $dbw->insertId();

				// save first 255 chars of long text in main table
				$data[$key]['aa_response_text'] = $lang->truncate( $data[$key]['aa_response_text'], 255 );
				$data[$key]['aat_id'] = $aatId;
			}
		}

		$dbw->insert( 'aft_article_answer', $data, __METHOD__ );
		$dbw->update(
			'aft_article_feedback',
			array( 'af_cta_id' => $ctaId ),
			array( 'af_id'     => $feedbackId ),
			__METHOD__
		);

		wfProfileOut( __METHOD__ );

		return array(
			'feedback_id' => ( $feedbackId ? $feedbackId : 0 )
		);
	}

	/**
	 * Inserts or updates properties for a specific rating
	 * @param $revisionId int    Revision ID
	 */
	private function saveUserProperties( $feedbackId ) {
		wfProfileIn( __METHOD__ );

		global $wgUser;
		$dbw  = wfGetDB( DB_MASTER );
		$rows = array();

		// Only save data for logged-in users.
		if ( !$wgUser->isLoggedIn() ) {
			wfProfileOut( __METHOD__ );
			return null;
		}

		// Total edits by this user
		$rows[] = array(
			'afp_feedback_id' => $feedbackId,
			'afp_key'         => 'contribs-lifetime',
			'afp_value_int'   => ( integer ) $wgUser->getEditCount()
		);

		// Use the UserDailyContribs extension if it's present. Get
		// edit counts for last 6 months, last 3 months, and last month.
		if ( function_exists( 'getUserEditCountSince' ) ) {
			$now = time();

			$rows[] = array(
				'afp_feedback_id' => $feedbackId,
				'afp_key'         => 'contribs-6-months',
				'afp_value_int'   => getUserEditCountSince( $now - ( 60 * 60 * 24 * 365 / 2 ) )
			);

			$rows[] = array(
				'afp_feedback_id' => $feedbackId,
				'afp_key'         => 'contribs-3-months',
				'afp_value_int'   => getUserEditCountSince( $now - ( 60 * 60 * 24 * 365 / 4 ) )
			);

			$rows[] = array(
				'afp_feedback_id' => $feedbackId,
				'afp_key'         => 'contribs-1-months',
				'afp_value_int'   => getUserEditCountSince( $now - ( 60 * 60 * 24 * 30 ) )
			);
		}

		$dbw->insert(
			'aft_article_feedback_properties',
			$rows,
			__METHOD__
		);

		wfProfileOut( __METHOD__ );
	}

	/**
	* Gets the anonymous token from the params
	*
	* @param  $params array the params
	* @return string  the token, or null if the user is not anonymous
	*/
	public function getAnonToken( $params ) {
		global $wgUser;
		$token = null;
		if ( $wgUser->isAnon() ) {
			if ( !isset( $params['anontoken'] ) ) {
				$this->dieUsageMsg( array( 'missingparam', 'anontoken' ) );
			} elseif ( strlen( $params['anontoken'] ) != 32 ) {
				$this->dieUsage( 'The anontoken is not 32 characters', 'invalidtoken' );
			}
			$token = $params['anontoken'];
		} else {
			$token = '';
		}
		return $token;
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		global $wgArticleFeedbackv5DisplayBuckets, $wgArticleFeedbackv5CTABuckets;
		$formIds = array_keys( $wgArticleFeedbackv5DisplayBuckets['buckets'] );
		$ctaIds = array_keys( $wgArticleFeedbackv5CTABuckets['buckets'] );

		$ret = array(
			'pageid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'revid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'anontoken' => null,
			'bucket' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_MIN      => min( $formIds ),
				ApiBase::PARAM_MAX      => max( $formIds )
			),
			'link' => array(
				ApiBase::PARAM_TYPE     => 'string',
				ApiBase::PARAM_REQUIRED => true,
			),
			'experiment' => array(
				ApiBase::PARAM_TYPE     => 'string',
			),
			'email' => array(
				ApiBase::PARAM_TYPE     => 'string',
			),
			'cta' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_MIN      => min( $ctaIds ),
				ApiBase::PARAM_MAX      => max( $ctaIds ),
			)
		);

		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach ( $fields as $field ) {
			$ret[$field['afi_name']] = array(
				ApiBase::PARAM_TYPE     => 'string',
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
			);
		}

		return $ret;
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		$ret = array(
			'pageid'     => 'Page ID to submit feedback for',
			'revid'      => 'Revision ID to submit feedback for',
			'anontoken'  => 'Token for anonymous users',
			'bucket'     => 'Which feedback form was shown to the user',
			'experiment' => 'Which experiment was shown to the user',
			'link'       => 'Which link the user clicked on to get to the widget',
		);
		$fields = ApiArticleFeedbackv5Utils::getFields();
		foreach ( $fields as $f ) {
			$ret[$f['afi_name']] = 'Optional feedback field, only appears on certain "buckets".';
		}
		return $ret;
	}

	/**
	 * Returns whether this API call is post-only
	 *
	 * @return bool
	 */
	public function mustBePosted() { return true; }

	/**
	 * Returns whether this is a write call
	 *
	 * @return bool
	 */
	public function isWriteMode() { return true; }

	/**
	 * TODO
	 * Gets a list of possible errors
	 *
	 * @return bool
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'anontoken' ),
			array( 'code' => 'invalidtoken', 'info' => 'The anontoken is not 32 characters' ),
			array( 'code' => 'invalidpage', 'info' => 'ArticleFeedback is not enabled on this page' ),
			array( 'code' => 'invalidpageid', 'info' => 'Page ID is missing or invalid' ),
			array( 'code' => 'missinguser', 'info' => 'User info is missing' ),
		) );
	}

	/**
	 * Gets a description
	 *
	 * @return string
	 */
	public function getDescription() {
		return array(
			'Submit article feedback'
		);
	}

	/**
	 * TODO
	 * Gets a list of examples
	 *
	 * @return array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=articlefeedbackv5'
		);
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

}

