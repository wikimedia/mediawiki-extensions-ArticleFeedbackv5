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
                parent::__construct( $query, $moduleName, 'af' );
        }

	/**
	 * Execute the API call: Pull the requested feedback
	 */
	public function execute() {
		$params = $this->extractRequestParams();
		$this->getResult()->addValue(
			null,
			$this->getModuleName(),
			array(
				'result' => 'Success',
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
			'feedbackid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'type'       => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => array( 
				 'abuse', 'hide' )
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
		return __CLASS__ . ': $Id: ApiFlagRatingsArticleFeedbackv5.php 103439 2011-11-17 03:19:01Z gregchiasson $';
	}

	public function isWriteMode() { return true; }
	public function mustBePosted() { return true; }
}
