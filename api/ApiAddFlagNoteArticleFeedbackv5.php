<?php
/**
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 */

use MediaWiki\Api\ApiBase;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\ActorMigration;
use MediaWiki\WikiMap\WikiMap;
use Wikimedia\ParamValidator\ParamValidator;
use Wikimedia\ParamValidator\TypeDef\IntegerDef;

/**
 * This class allows one to add a note describing activity, after the action
 * has been performed already.
 */
class ApiAddFlagNoteArticleFeedbackv5 extends ApiBase {
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

		$affected = 0;
		$results = [];
		$services = MediaWikiServices::getInstance();

		// get important values from our parameters
		$params = $this->extractRequestParams();
		$logId = $params['logid'];
		$action = $params['flagtype'];
		$notes = $params['note'];
		$feedbackId = $params['feedbackid'];
		$source = $params['source'];

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
		if ( !$pageObj->exists() ) {
			$this->dieWithError( 'notanarticle' );
		}

		if ( $user->getId() ) {
			// update log entry in database
			$dbw = ArticleFeedbackv5Utils::getDB( DB_PRIMARY );
			$data = [
				'log_id' => $logId,
				// failsafe, making sure this can't be gamed to add comments to anything other than AFTv5 entries
				'log_type' => ArticleFeedbackv5Activity::$actions[$action]['log_type'],
				'log_action' => $action
			];
			// failsafe, making sure this can't be gamed to add comments to other users' feedback
			$data += ActorMigration::newMigration()->getInsertValues( $dbw, 'log_user', $user );
			$logComment = $services->getCommentStore()->insert(
				$dbw,
				'log_comment',
				$notes
			);
			$affected = $dbw->update(
				'logging',
				$logComment,
				$data,
				__METHOD__
			);

			/**
			 * ManualLogEntry will have written to database. To make sure that subsequent
			 * reads are up-to-date, I'll set a flag to know that we've written data, so
			 * DB_PRIMARY will be queried.
			 */
			$wiki = WikiMap::getCurrentWikiId();
			ArticleFeedbackv5Utils::$written[$wiki] = true;

			if ( $affected > 0 ) {
				/*
				 * While we're at it, since activity has occurred, the editor activity
				 * data in cache may be out of date.
				 */
				$cache = $services->getMainWANObjectCache();
				$key = $cache->makeKey(
					'ArticleFeedbackv5Activity-getLastEditorActivity',
					$feedbackId
				);
				$cache->delete( $key );
			}
		}

		$feedback = ArticleFeedbackv5Model::get( $feedbackId, $pageObj->getId() );
		if ( $feedback ) {
			// re-render feedback entry
			$permalink = $source === 'permalink';
			$central = $source === 'central';
			$renderer = new ArticleFeedbackv5Render( $user, $permalink, $central );
			$results['render'] = $renderer->run( $feedback );
		}

		if ( $affected === 0 ) {
			$this->dieWithError(
				'articlefeedbackv5-invalid-log-update',
				'invalidlogid',
				$results
			);
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
			'logid' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => 'integer',
				IntegerDef::PARAM_MIN => 1
			],
			'flagtype' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => array_keys( ArticleFeedbackv5Activity::$actions )
			],
			'note' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => 'string'
			],
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
			'api.php?action=articlefeedbackv5-add-flag-note&logid=1&note=text&flagtype=resolve&feedbackid=1&pageid=1'
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
