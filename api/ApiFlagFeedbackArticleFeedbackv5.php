<?php
/**
 * ApiFlagFeedbackArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
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
	 * Execute the API call
	 *
	 * This single api call covers all cases where flags are being applied to
	 * a piece of feedback
	 */
	public function execute() {
		wfProfileIn( __METHOD__ );

		global $wgUser;

		// get important values from our parameters
		$params     = $this->extractRequestParams();
		$feedbackId = $params['feedbackid'];
		$pageId     = $params['pageid'];
		$flag       = $params['flagtype'];
		$notes      = $params['note'];
		$toggle     = $params['toggle'];
		$source     = $params['source'];

		/*
		 * fallback when dealing with outdated javascript:
		 * a "decrease" direction used to be passed on for the reverse action,
		 * rather than kist calling the reverse action name itself (as is now)
		 */
		if ( isset( $params['direction'] ) && $params['direction'] == 'decrease' ) {
			switch ( $flag ) {
				case 'helpful':
				case 'unhelpful':
					$flag = "undo-$flag";
					break;
				default:
					$flag = "un$flag";
					break;
			}
		}

		// Fire up the flagging object
		$flagger = new ArticleFeedbackv5Flagging( $wgUser, $feedbackId, $pageId );
		$results = $flagger->run( $flag, $notes, $toggle, $source );

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			$results
		);

		wfProfileOut( __METHOD__ );
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
				 'flag', 'hide', 'helpful', 'unhelpful', 'oversight', 'request', 'decline', 'resolve', 'feature' )
			),
			'note' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			),
			'toggle' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'boolean'
			),
			'source' => array(
				ApiBase::PARAM_REQUIRED => false,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array( 'article', 'central', 'watchlist', 'permalink', 'unknown' )
			),
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
			'note'        => 'Information on why the feedback activity occurred',
			'toggle'      => 'The flag is being toggled atomically, only useful for (un)helpful',
			'source'      => 'The origin of the flag: article (page), central (feedback page), watchlist (page), permalink',
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
