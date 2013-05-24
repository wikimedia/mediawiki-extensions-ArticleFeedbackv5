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
 * This class allows you to performs a certain action (e.g. resolve,
 * mark as useful) to feedback.
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

		$results = array();

		// get important values from our parameters
		$params     = $this->extractRequestParams();
		$feedbackId = $params['feedbackid'];
		$flag       = $params['flagtype'];
		$notes      = $params['note'];
		$toggle     = $params['toggle'];
		$source     = $params['source'];

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
		if ( !$pageObj->exists() ) {
			$this->dieUsage(
				$this->msg( 'articlefeedbackv5-invalid-page-id' )->escaped(),
				'notanarticle'
			);
		} else {
			$pageId = $pageObj->getId();
		}

		// Fire up the flagging object
		$flagger = new ArticleFeedbackv5Flagging( $wgUser, $feedbackId, $pageId );
		$status = $flagger->run( $flag, $notes, $toggle, $source );

		$feedback = ArticleFeedbackv5Model::get( $feedbackId, $pageId );
		if ( $feedback ) {
			// re-render feedback entry
			$permalink = $source == 'permalink';
			$central = $source == 'central';
			$renderer = new ArticleFeedbackv5Render( $permalink, $central );
			$results['render'] = $renderer->run( $feedback );
		}

		if ( !$status ) {
			$this->dieUsage(
				$this->msg( $flagger->getError() )->text(),
				'flagerror',
				0,
				$results
			);
		} else {
			$results['log_id'] = $flagger->getLogId();
		}

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
			'title' => null,
			'pageid' => array(
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'feedbackid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
			),
			'flagtype' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array_keys( ArticleFeedbackv5Activity::$actions ),
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
		$p = $this->getModulePrefix();
		return array(
			'title' => "Title of the page to flag feedback for. Cannot be used together with {$p}pageid",
			'pageid' => "ID of the page to flag feedback for. Cannot be used together with {$p}title",
			'feedbackid' => 'FeedbackID to flag',
			'flagtype' => 'Type of flag to apply',
			'note' => 'Information on why the feedback activity occurred',
			'toggle' => 'The flag is being toggled atomically, only useful for (un)helpful',
			'source' => 'The origin of the flag: article (page), central (feedback page), watchlist (page), permalink',
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
			'api.php?action=articlefeedbackv5-flag-feedback&feedbackid=1&pageid=1&flagtype=helpful'
		);
	}

	/**
	 * Gets the version info
	 *
	 * @return string the SVN version info
	 */
	public function getVersion() {
		return __CLASS__ . ': version 1.5';
	}

	public function isWriteMode() { return true; }

	public function mustBePosted() { return true; }

}
