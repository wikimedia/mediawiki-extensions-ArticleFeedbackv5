<?php
/**
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

use MediaWiki\Api\ApiBase;
use Wikimedia\ParamValidator\ParamValidator;

/**
 * This class allows you to performs a certain action (e.g. resolve,
 * mark as useful) to feedback.
 *
 * @package    ArticleFeedback
 */
class ApiFlagFeedbackArticleFeedbackv5 extends ApiBase {
	/**
	 * @param MediaWiki\Api\ApiMain $query
	 * @param string $moduleName
	 */
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
		$user = $this->getUser();
		$results = [];

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
			$this->dieWithError(
				'articlefeedbackv5-invalid-page-id',
				'notanarticle'
			);
		} else {
			$pageId = $pageObj->getId();
		}

		// Fire up the flagging object
		$flagger = new ArticleFeedbackv5Flagging( $user, $feedbackId, $pageId );
		$status = $flagger->run( $flag, $notes, $toggle, $source );

		$feedback = ArticleFeedbackv5Model::get( $feedbackId, $pageId );
		if ( $feedback ) {
			// re-render feedback entry
			$permalink = $source == 'permalink';
			$central = $source == 'central';
			$renderer = new ArticleFeedbackv5Render( $user, $permalink, $central );
			$results['render'] = $renderer->run( $feedback );
		}

		if ( !$status ) {
			$this->dieWithError(
				$flagger->getError(),
				'flagerror',
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
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return [
			'title' => null,
			'pageid' => [
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'integer'
			],
			'feedbackid' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'string'
			],
			'flagtype' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => array_keys( ArticleFeedbackv5Activity::$actions ),
			],
			'note' => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => 'string'
			],
			'toggle' => [
				ParamValidator::PARAM_TYPE     => 'boolean'
			],
			'source' => [
				ParamValidator::PARAM_REQUIRED => false,
				ParamValidator::PARAM_ISMULTI  => false,
				ParamValidator::PARAM_TYPE     => [ 'article', 'central', 'watchlist', 'permalink', 'unknown' ]
			],
		];
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return [
			'api.php?action=articlefeedbackv5-flag-feedback&feedbackid=1&pageid=1&flagtype=helpful'
		];
	}

	/** @inheritDoc */
	public function isWriteMode() {
		return true;
	}

	/** @inheritDoc */
	public function needsToken() {
		return 'csrf';
	}

}
