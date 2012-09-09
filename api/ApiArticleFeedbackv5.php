<?php
/**
 * ApiArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <reha@omniti.com>
 * @version    $Id$
 */

// @todo: this contains some code that can most likely be removed ;)

/**
 * This saves the ratings
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiArticleFeedbackv5 extends ApiBase {
	// Allow auto-flagging of feedback
	private $autoFlag = array();

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
		wfProfileIn( __METHOD__ );

		$params = $this->extractRequestParams();
		$user = $this->getUser();

		// Blocked users are, well, blocked.
		if ( $user->isBlocked() ) {
			$this->getResult()->addValue( null, 'error', 'articlefeedbackv5-error-blocked' );
			wfProfileOut( __METHOD__ );
			return;
		}

		// Is feedback enabled on this page check?
		if ( !ApiArticleFeedbackv5Utils::isFeedbackEnabled( $params ) ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( 'ArticleFeedback is not enabled on this page', 'invalidpage' );
		}

		// Build feedback entry
		$feedback = new ArticleFeedbackv5Feedback();
		$feedback->aft_page = $params['pageid'];
		$feedback->aft_page_revision = $params['revid'];
		$feedback->aft_user = $user->getId();
		$feedback->aft_user_text = $user->getName();
		$feedback->aft_form = $params['bucket'];
		$feedback->aft_cta = $params['cta'];
		$feedback->aft_link = $params['link'];
		$feedback->aft_rating = $params['found'];
		$feedback->aft_comment = $params['comment'];

		/**
		 * Check for abusive comment in the following sequence (cheapest
		 * processing to most expensive, returning if we get a hit):
		 * 1) Respect $wgSpamRegex
		 * 2) Check SpamBlacklist
		 * 3) Check AbuseFilter
		 */
		global $wgArticleFeedbackv5AbuseFiltering;
		if ( $wgArticleFeedbackv5AbuseFiltering ) {
			if ( ApiArticleFeedbackv5Utils::validateSpamRegex( $this->aft_comment ) ) {
				$this->dieUsage( "Comment was flagged as abusive by SpamRegex", 'articlefeedbackv5-error-abuse' );
			} elseif ( ApiArticleFeedbackv5Utils::validateSpamBlacklist( $this->aft_comment, $this->aft_page ) ) {
				$this->dieUsage( "Comment was flagged as abusive by SpamBlacklist", 'articlefeedbackv5-error-abuse' );
			} else {
				$error = ApiArticleFeedbackv5Utils::validateAbuseFilter(
					$this->aft_comment,
					$this->aft_page,
					array( $this, 'callbackAbuseActionFlag' )
				);

				if ( $error !== false ) {
					$this->dieUsage( $error, 'articlefeedbackv5-error-abuse' );
				}
			}
		}

		// Save feedback
		try {
			$feedback->save();
		} catch ( MWException $e ) {
			$this->dieUsage( $e->getMessage(), 'inserterror' );
		}

		// Are we set to auto-flag?
		$flagger = new ArticleFeedbackv5Flagging( 0, $feedback->aft_id );
		foreach ( $this->autoFlag as $flag => $rule_desc ) {
			$notes = wfMessage(
				"articlefeedbackv5-abusefilter-note-aftv5$flag",
				array( $rule_desc )
			)->parse();

			$res = $flagger->run( $flag, $notes );
			if ( 'Error' == $res['result'] ) {
				// TODO: Log somewhere?
			}
		}

		// build url to permalink and special page
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( "Page for feedback does not exist", "invalidfeedbackid" );
		}
		$specialTitle = Title::newFromText( "ArticleFeedbackv5/$page", NS_SPECIAL );
		$aftUrl = $specialTitle->getLinkUrl( array( 'ref' => 'cta' ) );
		$permalinkTitle = Title::newFromText( "ArticleFeedbackv5/$page/$feedback->aft_id", NS_SPECIAL );
		$permalink = $permalinkTitle->getLinkUrl( array( 'ref' => 'cta' ) );

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array(
				'result'      => 'Success',
				'feedback_id' => $feedback->aft_id,
				'aft_url'     => $aftUrl,
				'permalink'   => $permalink,
			)
		);

		wfProfileOut( __METHOD__ );
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
	public function callbackAbuseActionFlag( $action, $parameters, $title, $vars, $rule_desc ) {
		switch ( $action ) {
			case 'aftv5flagabuse':
				$this->autoFlag['flagabuse'] = $rule_desc;
				break;
			case 'aftv5hide':
				$this->autoFlag['hide'] = $rule_desc;
				break;
			case 'aftv5requestoversight':
				$this->autoFlag['requestoversight'] = $rule_desc;
				break;
			default:
				// Fall through silently
				break;
		}
	}

	// @todo: method obsolete? still has useful data?
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
 	 * Creates a new feedback record and inserts the user's rating
	 * for a specific revision
	 *
	 * @todo: method obsolete? still has useful data?
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
				ApiBase::PARAM_MIN      => 0
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
				ApiBase::PARAM_MIN      => 0,
				ApiBase::PARAM_MAX      => 4,
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

