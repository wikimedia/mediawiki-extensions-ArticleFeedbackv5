<?php
/**
 * ApiAddFlagNoteArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

/**
 * This class allows one to add a note describing activity, after the action
 * has been performed already.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiAddFlagNoteArticleFeedbackv5 extends ApiBase {
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

		$affected = 0;

		global $wgUser;
		if ( $wgUser->getId() ) {
			// get important values from our parameters
			$params     = $this->extractRequestParams();
			$logId      = $params['logid'];
			$action     = $params['flagtype'];
			$notes      = $params['note'];
			$feedbackId = $params['feedbackid'];
			$pageId     = $params['pageid'];
			$source     = $params['source'];

			// update log entry in database
			$dbw = ArticleFeedbackv5Utils::getDB( DB_MASTER );
			$affected = $dbw->update(
				'logging',
				array( 'log_comment' => $notes ),
				array(
					'log_id' => $logId,
					// failsafe, making sure this can't be gamed to add comments to anything other than AFTv5 entries
					'log_type' => ArticleFeedbackv5Activity::$actions[$action]['log_type'],
					'log_action' => $action,
					// failsafe, making sure this can't be gamed to add comments to other users' feedback
					'log_user' => $wgUser->getId(),
				)
			);

			/**
			 * ManualLogEntry will have written to database. To make sure that subsequent
			 * reads are up-to-date, I'll set a flag to know that we've written data, so
			 * DB_MASTER will be queried.
			 */
			ArticleFeedbackv5Utils::$written = true;
		}

		$results = array();
		if ( $affected > 0 ) {
			/*
			 * While we're at it, since activity has occurred, the editor activity
			 * data in cache may be out of date.
			 */
			global $wgMemc;
			$key = wfMemcKey( 'ArticleFeedbackv5Activity', 'getLastEditorActivity', $feedbackId );
			$wgMemc->delete( $key );

			$results['result'] = 'Success';
			$results['reason'] = null;
		} else {
			$results['result'] = 'Error';
			$results['reason'] = 'articlefeedbackv5-invalid-log-update';
		}

		$feedback = ArticleFeedbackv5Model::get( $feedbackId, $pageId );
		if ( $feedback ) {
			// re-render feedback entry
			$permalink = $source == 'permalink';
			$central = $source == 'central';
			$renderer = new ArticleFeedbackv5Render( $permalink, $central );
			$results['render'] = $renderer->run( $feedback );
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
			'logid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_MIN => 1
			),
			'flagtype' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => array_keys( ArticleFeedbackv5Activity::$actions )
			),
			'note' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_TYPE => 'string'
			),
			'pageid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'integer'
			),
			'feedbackid' => array(
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI  => false,
				ApiBase::PARAM_TYPE     => 'string'
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
			'logid' => 'Log ID to update',
			'flagtype' => 'Type of flag to apply',
			'note'   => 'Information on why the feedback activity occurred',
			'pageid' => 'PageID of feedback',
			'feedbackid' => 'FeedbackID to flag',
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
			'Add a note describing activity, after the action has been performed already.'
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=articlefeedbackv5-add-flag-note&logid=1&note=text&flagtype=resolve&feedbackid=1&pageid=1'
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
