<?php
/**
 * ApiViewActivityArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 */

use MediaWiki\Api\ApiBase;
use MediaWiki\Api\ApiQueryBase;
use MediaWiki\Html\Html;
use MediaWiki\Language\RawMessage;
use MediaWiki\MediaWikiServices;
use MediaWiki\SpecialPage\SpecialPage;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;
use Wikimedia\ParamValidator\TypeDef\IntegerDef;

/**
 * This class pulls the aggregated ratings for display in Bucket #5
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiViewActivityArticleFeedbackv5 extends ApiQueryBase {

	/**
	 * @param MediaWiki\Api\ApiQuery $query
	 * @param string $moduleName
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	/**
	 * Execute the API call: Pull max 25 activity log items for page
	 */
	public function execute() {
		/*
		 * To bust caches, this GET value may be added to the querystring. Codewise,
		 * we won't really use it for anything, but we don't want it to output a
		 * "Unrecognized parameter" warning either, so let's make sure ApiMain
		 * considers it used ;)
		 */
		$this->getMain()->getVal( '_' );

		$user = $this->getUser();
		$lang = $this->getLanguage();

		if ( !$user->isAllowed( 'aft-editor' ) ) {
			$this->dieWithError(
				'articlefeedbackv5-error-permission-denied',
				'permissiondenied'
			);
		}

		// get our parameter information
		$params = $this->extractRequestParams();
		$limit = $params['limit'];
		$continue = $params['continue'];
		$result = $this->getResult();

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
		if ( !$pageObj->exists() ) {
			$this->dieWithError( 'notanarticle' );
		}

		// fetch our activity database information
		/** @var ArticleFeedbackv5Model $feedback */
		$feedback = ArticleFeedbackv5Model::get( $params['feedbackid'], $pageObj->getId() );
		// if this is false, this is bad feedback - move along
		if ( !$feedback ) {
			$this->dieWithError(
				'articlefeedbackv5-error-nonexistent-feedback',
				'invalidfeedbackid'
			);
		}

		// get the string title for the page
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			$this->dieWithError(
				'articlefeedbackv5-error-nonexistent-page',
				'invalidfeedbackid'
			);
		}

		// get our activities
		try {
			$activities = ArticleFeedbackv5Activity::getList( $feedback, $user, $limit, $continue );
		} catch ( Exception $e ) {
			$this->dieWithError( ( new RawMessage( '$1' ) )->plaintextParams( $e->getMessage() ), $e->getCode() );
		}

		// generate our HTML
		$html = '';

		$services = MediaWikiServices::getInstance();

		// only do this if continue is not null
		if ( !$continue && !$params['noheader'] ) {
			$result->addValue( $this->getModuleName(), 'hasHeader', true );

			$html .=
				Html::rawElement(
					'div',
					[ 'class' => 'articleFeedbackv5-activity-pane' ],
					Html::rawElement(
						'div',
						[ 'class' => 'articleFeedbackv5-activity-feedback' ],
						Html::rawElement(
							'div',
							[],
							wfMessage( 'articlefeedbackv5-activity-feedback-info' )
								->params( $feedback->aft_id )
								->rawParams( ArticleFeedbackv5Utils::getUserLink( $feedback->aft_user, $feedback->aft_user_text ) )
								->params( $feedback->aft_user_text ) // username or ip
								->escaped()
						) .
						Html::element(
							'div',
							[],
							wfMessage( 'articlefeedbackv5-activity-feedback-date' )
								->params(
									$lang->userTimeAndDate( $feedback->aft_timestamp, $user ),
									$lang->userDate( $feedback->aft_timestamp, $user ),
									$lang->userTime( $feedback->aft_timestamp, $user )
								)->text()
						) .
						Html::rawElement(
							'div',
							[ 'class' => 'articleFeedbackv5-activity-feedback-permalink' ],
							$services->getLinkRenderer()->makeLink(
								SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedback->aft_id ),
								wfMessage( 'articlefeedbackv5-activity-permalink' )->text()
							)
						)
					) .
					Html::element(
						'div',
						[ 'class' => 'articleFeedbackv5-activity-count' ],
						wfMessage( 'articlefeedbackv5-activity-count' )->numParams( ArticleFeedbackv5Activity::getActivityCount( $feedback, $user ) )->text()
					)
				);

			$html .=
				Html::openElement(
					'div',
					[ 'class' => 'articleFeedbackv5-activity-log-items' ]
				);
		}

		$count = 0;
		$commentFormatter = $services->getCommentFormatter();

		// divs of activity items
		foreach ( $activities as $item ) {
			// skip item if user is not permitted to see it
			if ( !ArticleFeedbackv5Activity::canPerformAction( $item->log_action, $user ) ) {
				continue;
			}

			// figure out if we have more if we have another row past our limit
			$count++;
			if ( $count > $limit ) {
				break;
			}

			$sentiment = ArticleFeedbackv5Activity::$actions[$item->log_action]['sentiment'];

			// Give grep a chance to find the usages:
			// articlefeedbackv5-activity-item-request, articlefeedbackv5-activity-item-unrequest,
			// articlefeedbackv5-activity-item-decline, articlefeedbackv5-activity-item-flag,
			// articlefeedbackv5-activity-item-unflag, articlefeedbackv5-activity-item-autoflag,
			// articlefeedbackv5-activity-item-oversight, articlefeedbackv5-activity-item-unoversight,
			// articlefeedbackv5-activity-item-feature, articlefeedbackv5-activity-item-unfeature,
			// articlefeedbackv5-activity-item-resolve, articlefeedbackv5-activity-item-unresolve,
			// articlefeedbackv5-activity-item-noaction, articlefeedbackv5-activity-item-unnoaction,
			// articlefeedbackv5-activity-item-inappropriate, articlefeedbackv5-activity-item-uninappropriate,
			// articlefeedbackv5-activity-item-hide, articlefeedbackv5-activity-item-unhide,
			// articlefeedbackv5-activity-item-autohide, articlefeedbackv5-activity-item-archive,
			// articlefeedbackv5-activity-item-unarchive, articlefeedbackv5-activity-item-helpful,
			// articlefeedbackv5-activity-item-unhelpful, articlefeedbackv5-activity-item-undo-helpful,
			// articlefeedbackv5-activity-item-undo-unhelpful, articlefeedbackv5-activity-item-clear-flags
			$html .=
				Html::rawElement(
					'div',
					[ 'class' => 'articleFeedbackv5-activity-item' ],
					Html::rawElement(
						'span',
						[ 'class' => "articleFeedbackv5-activity-item-action articleFeedbackv5-activity-item-action-$sentiment" ],
						wfMessage( 'articlefeedbackv5-activity-item-' . $item->log_action )
							->rawParams(
								ArticleFeedbackv5Utils::getUserLink( $item->log_user, $item->log_user_text ),
								$commentFormatter->formatBlock( $item->log_comment ),
								Html::element( 'span', [], $lang->userTimeAndDate( $item->log_timestamp, $user ) ),
								Html::element( 'span', [], $lang->userDate( $item->log_timestamp, $user ) ),
								Html::element( 'span', [], $lang->userTime( $item->log_timestamp, $user ) )
							)
							->params( $item->log_user_text )
							->escaped()
					)
				);
		}

		if ( $count > $limit ) {
			$html .=
				Html::element(
					'a',
					[
						'class' => "articleFeedbackv5-activity-more",
						'href' => '#',
					],
					wfMessage( "articlefeedbackv5-activity-more" )->text()
				);
		}

		$html .= Html::closeElement( 'div' );

		// finally add our generated html data
		$result->addValue( $this->getModuleName(), 'limit', $limit );
		$result->addValue( $this->getModuleName(), 'activity', $html );

		// continue only goes in if it's not empty
		if ( $count > $limit && isset( $item ) ) {
			$this->setContinueEnumParameter( 'continue', ArticleFeedbackv5Activity::getContinue( $item ) );
		}
	}

	/**
	 * Gets the allowed parameters
	 *
	 * @return array the params info, indexed by allowed key
	 */
	public function getAllowedParams() {
		return [
			'feedbackid' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE     => 'string',
			],
			'title' => null,
			'pageid' => [
				ParamValidator::PARAM_TYPE     => 'integer',
			],
			'limit' => [
				ParamValidator::PARAM_DEFAULT => 25,
				ParamValidator::PARAM_TYPE => 'limit',
				IntegerDef::PARAM_MIN => 1,
				IntegerDef::PARAM_MAX => ApiBase::LIMIT_BIG1,
				IntegerDef::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			],
			'continue' => null,
			'noheader' => [
				ParamValidator::PARAM_TYPE => 'boolean',
			],
		];
	}

	/**
	 * @deprecated since MediaWiki 1.25
	 *
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return [
			'api.php?action=query&list=articlefeedbackv5-view-activity&aafeedbackid=429384108662e9d4e41ab6e275d0392e&aapageid=1',
		];
	}
}
