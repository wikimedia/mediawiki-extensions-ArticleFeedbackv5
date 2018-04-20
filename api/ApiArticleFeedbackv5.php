<?php
/**
 * ApiArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

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
		// Blocked users are, well, blocked.
		$user = $this->getUser();
		if ( $user->isBlocked() ) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-error-blocked' )->escaped(),
				'userblocked'
			);
		}

		$params = $this->extractRequestParams();

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
		if ( !$pageObj->exists() ) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-invalid-page-id' )->escaped(),
				'notanarticle'
			);
		}

		// Check if feedback is enabled on this page
		if ( !ArticleFeedbackv5Utils::isFeedbackEnabled( $pageObj->getId() ) ) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-page-disabled' )->escaped(),
				'invalidpage'
			);
		}

		// Build feedback entry
		$feedback = new ArticleFeedbackv5Model();
		$feedback->aft_page = $pageObj->getId();
		$feedback->aft_page_revision = $params['revid'];
		$feedback->aft_user = $user->getId();
		$feedback->aft_user_text = $user->getName();
		$feedback->aft_user_token = $params['anontoken'];
		$feedback->aft_claimed_user = $user->getId();
		$feedback->aft_form = $params['bucket'];
		$feedback->aft_cta = $params['cta'];
		$feedback->aft_link = $params['link'];
		$feedback->aft_rating = $params['found'];
		$feedback->aft_comment = trim( $params['comment'] );

		/*
		 * Check submission against last entry: do not allow duplicates.
		 *
		 * The $user->mRights manipulation is a bit nasty. ArticleFeedbackModel's
		 * getList will check if a certain user's permissions suffice to see a
		 * certain list. To make sure that we get the absolute latest entry,
		 * we'll request a list that has no conditions at all - a list that should
		 * otherwise not be accessible. The solution here is to add a non-existing
		 * permission to the list and pretend to have that permission when attempting
		 * to fetch that list here. Now we can leave the permission-safeguard in place.
		 * Afterwards, clean up the rights by removing the bogus one.
		 */
		$user->mRights[] = 'aft-noone';
		$list = ArticleFeedbackv5Model::getList( '*', $feedback->aft_page, 0, 'age', 'DESC' );
		$user->mRights = array_diff( $user->mRights, array( 'aft-noone' ) );

		$old = $list->fetchObject();
		if (
			$old &&
			$old->aft_user == $feedback->aft_user &&
			$old->aft_comment == $feedback->aft_comment &&
			$old->aft_timestamp > wfTimestamp( TS_MW, strtotime( '1 minute ago' ) )
		) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-error-duplicate' )->escaped(),
				'duplicate'
			);
		}

		/**
		 * Check for abusive comment in the following sequence (cheapest
		 * processing to most expensive, returning if we get a hit):
		 * 1) Respect $wgSpamRegex
		 * 2) Check SpamBlacklist
		 * 3) Check AbuseFilter
		 */
		global $wgArticleFeedbackv5AbuseFiltering;
		if ( $wgArticleFeedbackv5AbuseFiltering ) {
			if ( ArticleFeedbackv5Utils::validateSpamRegex( $feedback->aft_comment ) ) {
				$this->dieUsage( "Comment was flagged as abusive by SpamRegex", 'articlefeedbackv5-error-abuse' );
			} elseif ( ArticleFeedbackv5Utils::validateSpamBlacklist( $feedback->aft_comment, $feedback->aft_page ) ) {
				$this->dieUsage( "Comment was flagged as abusive by SpamBlacklist", 'articlefeedbackv5-error-abuse' );
			} else {
				$error = ArticleFeedbackv5Utils::validateAbuseFilter(
					$feedback->aft_comment,
					$feedback->aft_page,
					array( $this, 'callbackAbuseActionFlag' )
				);

				if ( $error !== false ) {
					$messages = array();
					foreach ( $error as $message ) {
						$messages[] = $message[1];
					}

					$this->dieUsage(
						$this->msg(
							'articlefeedbackv5-error-abuse',
							$this->msg( 'articlefeedbackv5-error-abuse-link' )->inContentLanguage()->plain(),
							count( $messages ),
							$this->getLanguage()->listToText( $messages )
						)->parse(),
						'afreject'
					);
				}
			}
		}

		// Save feedback
		try {
			$feedback->insert();

			ArticleFeedbackv5Log::log(
				'create',
				$feedback->aft_page,
				$feedback->aft_id,
				'', // just like creation of page, no comment in logs
				$user,
				array()
			);
		} catch ( MWException $e ) {
//			$this->dieUsage( $e->getMessage(), 'inserterror' ); // easier when debugging: show exact exception message
			$this->dieUsage( $this->msg( 'articlefeedbackv5-error-submit' ), 'inserterror' );
		}

		// Are we set to auto-flag?
		$flagger = new ArticleFeedbackv5Flagging( null, $feedback->aft_id, $feedback->aft_page );
		foreach ( $this->autoFlag as $flag => $rule_desc ) {
			$notes = wfMessage(
				"articlefeedbackv5-abusefilter-note-aftv5$flag",
				array( $rule_desc )
			)->parse();

			$res = $flagger->run( $flag, $notes, false, 'abusefilter' );
			if ( 'Error' == $res['result'] ) {
				// TODO: Log somewhere?
			}
		}

		// build url to permalink and special page
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
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
				'feedback_id' => $feedback->aft_id,
				'aft_url'     => $aftUrl,
				'permalink'   => $permalink,
			)
		);
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
			case 'aftv5resolve':
				$this->autoFlag['resolve'] = $rule_desc;
				break;
			case 'aftv5flagabuse':
				$this->autoFlag['flag'] = $rule_desc;
				break;
			case 'aftv5hide':
				$this->autoFlag['hide'] = $rule_desc;
				break;
			case 'aftv5request':
				$this->autoFlag['request'] = $rule_desc;
				break;
			default:
				// Fall through silently
				break;
		}
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		global $wgArticleFeedbackv5DisplayBuckets, $wgArticleFeedbackv5CTABuckets, $wgArticleFeedbackv5LinkBuckets;
		$formIds = array_map( 'strval', array_keys( $wgArticleFeedbackv5DisplayBuckets['buckets'] ) );
		$ctaIds = array_map( 'strval', array_keys( $wgArticleFeedbackv5CTABuckets['buckets'] ) );
		$linkIds = array_map( 'strval', array_keys( $wgArticleFeedbackv5LinkBuckets['buckets'] ) );

		$ret = array(
			'title' => null,
			'pageid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
			),
			'revid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'anontoken' => array(
				ApiBase::PARAM_TYPE     => 'string',
				ApiBase::PARAM_REQUIRED => false,
			),
			'bucket' => array(
				ApiBase::PARAM_TYPE     => $formIds,
				ApiBase::PARAM_REQUIRED => true,
			),
			'cta' => array(
				ApiBase::PARAM_TYPE     => $ctaIds,
				ApiBase::PARAM_REQUIRED => true,
			),
			'link' => array(
				ApiBase::PARAM_TYPE     => $linkIds,
				ApiBase::PARAM_REQUIRED => true,
			),
			'found' => array(
				ApiBase::PARAM_TYPE     => array( '0', '1' ),
				ApiBase::PARAM_REQUIRED => false,
			),
			'comment' => array(
				ApiBase::PARAM_TYPE     => 'string',
				ApiBase::PARAM_REQUIRED => false,
			)
		);

		return $ret;
	}

	/**
	 * Gets the parameter descriptions
	 *
	 * @return array the descriptions, indexed by allowed key
	 */
	public function getParamDescription() {
		$p = $this->getModulePrefix();
		return array(
			'title'      => "Title of the page to submit feedback for. Cannot be used together with {$p}pageid",
			'pageid'     => "ID of the page to submit feedback for. Cannot be used together with {$p}title",
			'revid'      => 'Revision ID to submit feedback for',
			'anontoken'  => 'Token for anonymous users',
			'bucket'     => 'Which feedback form was shown to the user',
			'cta'        => 'CTA displayed after form submission',
			'link'       => 'Which link the user clicked on to get to the widget',
			'found'      => 'Yes/no feedback answering the question if the page was helpful',
			'comment'    => 'the fee-form textual feedback',
		);
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
}
