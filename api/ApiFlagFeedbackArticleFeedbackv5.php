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
		$params = $this->extractRequestParams();
		$error  = null;
		$dbr    = wfGetDB( DB_SLAVE );

		if ( !isset( $params['feedbackid'] )
		 || !preg_match( '/^\d+$/', $params['feedbackid'] ) ) {
			$error = 'articlefeedbackv5-invalid-feedback-id';
		}

		# load feedback record, bail if we don't have one
		$record = $dbr->selectRow(
			'aft_article_feedback',
			array( 'af_id', 'af_abuse_count', 'af_hide_count' ),
			array( 'af_id' => $params['feedbackid'] )
		);

		# TODO: 
		if ( !$record->af_id ) {
			// no-op, because this is already broken
			$error = 'articlefeedbackv5-invalid-feedback-id';
		} elseif ( $params['flagtype'] == 'abuse' ) {
			$update['af_abuse_count'] = $record->af_abuse_count + 1;
		} elseif ( $params['flagtype'] == 'hide' ) {
			$update['af_hide_count'] = $record->af_hide_count + 1;
		} elseif ( $params['flagtype'] == 'helpful' ) {
			$update['af_helpful_count'] = $record->af_helpful_count + 1;
		} elseif ( $params['flagtype'] == 'delete' ) {
			$update['af_delete_count'] = $record->af_delete_count + 1;
		} else {
			$error = 'articlefeedbackv5-invalid-feedback-flag';
		}

		if ( !$error ) {
			$dbw = wfGetDB( DB_MASTER );
			$dbw->update(
				'aft_article_feedback',
				$update,
				array( 'af_id' => $params['feedbackid'] )
			);
		}

		if ( $error ) {
			$result = 'Error';
			$reason = $error;
		} else {
			$result = 'Success';
			$reason = null;
		}

		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array(
				'result' => $result,
				'reason' => $reason,
			)
		);
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
				 'abuse', 'hide', 'helpful', 'delete' )
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
			'type'        => 'Type of flag to apply - hide or abuse'
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
