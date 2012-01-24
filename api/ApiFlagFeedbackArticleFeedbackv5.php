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
		$pageId = $params['pageid'];
		$error  = null;
		$dbr    = wfGetDB( DB_SLAVE );
		$counts = array( 'increment' => array(), 'decrement' => array() );

		# load feedback record, bail if we don't have one
		$record = $dbr->selectRow(
			'aft_article_feedback',
			array( 'af_id', 'af_abuse_count', 'af_hide_count', 'af_helpful_count', 'af_unhelpful_count', 'af_delete_count' ),
			array( 'af_id' => $params['feedbackid'] )
		);

		$flags  = array( 'abuse', 'hide', 'helpful', 'unhelpful', 'delete' );
		$flag   = $params['flagtype'];

		if ( !$record->af_id ) {
			// no-op, because this is already broken
			$error = 'articlefeedbackv5-invalid-feedback-id';
		} elseif( $params['flagtype'] == 'unhide' ) {
			// remove the hidden status
			$update[] = 'af_hide_count = 0';
		} elseif( $params['flagtype'] == 'unoversight' ) {
			// remove the oversight flag 
			$update[] = 'af_needs_oversight = FALSE';
		} elseif( $params['flagtype'] == 'undelete' ) {
			// remove the deleted status, and clear oversight flag
			$update[] = 'af_delete_count = 0';
			$update[] = 'af_needs_oversight = FALSE';
		} elseif( $params['flagtype'] == 'oversight' ) {
			// flag for oversight
			$update[] = 'af_needs_oversight = TRUE';
		} elseif( in_array( $params['flagtype'], $flags ) ) {
			// Probably this doesn't need validation, since the API
			// will handle it, but if it's getting interpolated into
			// the SQL, I'm really wary not re-validating it.
			$field = 'af_'.$params['flagtype'].'_count';
			$update[] = "$field = $field + 1";
		} else {
			$error = 'articlefeedbackv5-invalid-feedback-flag';
		}

		// Newly abusive record
		if( $flag == 'abuse' && $record->af_abuse_count == 0 ) {
			$counts['increment'][] = 'abusive';
		}

		if( $flag == 'oversight' ) {
			$counts['increment'][] = 'needsoversight';
		}
		if( $flag == 'unoversight' ) {
			$counts['decrement'][] = 'needsoversight';
		}


		// Newly hidden record
		if( $flag == 'hide' && $record->af_hide_count == 0 ) {
			$counts['increment'][] = 'invisible';
			$counts['decrement'][] = 'visible';
		}
		// Unhidden record
		if( $flag == 'unhide') {
			$counts['increment'][] = 'visible';
			$counts['decrement'][] = 'invisible';
		}

		// Newly deleted record
		if( $flag == 'delete' && $record->af_delete_count == 0 ) {
			$counts['increment'][] = 'deleted';
			$counts['decrement'][] = 'visible';
		}
		// Undeleted record
		if( $flag == 'undelete' ) {
			$counts['increment'][] = 'visible';
			$counts['decrement'][] = 'deleted';
		}
		
		// Newly helpful record
		if( $flag == 'helpful' && $record->af_helpful_count == 0 ) {
			$counts['increment'][] = 'helpful';
		}
		// Newly unhelpful record (IE, unhelpful has overtaken helpful)
		if( $flag == 'unhelpful' 
		 && ( ( $record->af_helpful_count - $record->af_unhelpful_count ) == 1 ) ) {
			$counts['decrement'][] = 'helpful';
		}

		if ( !$error ) {
			$dbw = wfGetDB( DB_MASTER );
			$dbw->update(
				'aft_article_feedback',
				$update,
				array( 'af_id' => $params['feedbackid'] ),
				__METHOD__
			);

			 ApiArticleFeedbackv5Utils::incrementFilterCounts( $pageId, $counts['increment'] );
			 ApiArticleFeedbackv5Utils::decrementFilterCounts( $pageId, $counts['decrement'] );
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
				 'abuse', 'hide', 'helpful', 'unhelpful', 'delete', 'undelete', 'unhide', 'oversight', 'unoversight' )
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
