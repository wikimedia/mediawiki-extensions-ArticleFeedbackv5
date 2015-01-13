<?php
/**
 * ApiViewActivityArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @version    $Id$
 */

/**
 * This class pulls the aggregated ratings for display in Bucket #5
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiViewActivityArticleFeedbackv5 extends ApiQueryBase {

	/**
	 * Constructor
	 */
	public function __construct( $query, $moduleName ) {
		parent::__construct( $query, $moduleName, 'aa' );
	}

	/**
	 * Execute the API call: Pull max 25 activity log items for page
	 */
	public function execute() {
		wfProfileIn( __METHOD__ );

		/*
		 * To bust caches, this GET value may be added to the querystring. Codewise,
		 * we won't really use it for anything, but we don't want it to output a
		 * "Unrecognized parameter" warning either, so let's make sure ApiMain
		 * considers it used ;)
		 */
		$this->getMain()->getVal( '_' );

		global $wgUser, $wgLang;

		if ( !$wgUser->isAllowed( 'aft-editor' ) ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( "You don't have permission to view feedback activity", 'permissiondenied' );
		}

		// get our parameter information
		$params = $this->extractRequestParams();
		$limit = $params['limit'];
		$continue = $params['continue'];
		$result = $this->getResult();

		// get page object
		$pageObj = $this->getTitleOrPageId( $params, 'fromdb' );
		if ( !$pageObj->exists() ) {
			$this->dieUsageMsg( 'notanarticle' );
		}

		// fetch our activity database information
		$feedback = ArticleFeedbackv5Model::get( $params['feedbackid'], $pageObj->getId() );
		// if this is false, this is bad feedback - move along
		if ( !$feedback ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( "Feedback does not exist", 'invalidfeedbackid' );
		}

		// get the string title for the page
		$page = Title::newFromID( $feedback->aft_page );
		if ( !$page ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( "Page for feedback does not exist", 'invalidfeedbackid' );
		}

		// get our activities
		try {
			$activities = ArticleFeedbackv5Activity::getList( $feedback, $wgUser, $limit, $continue );
		} catch ( Exception $e ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( $e->getMessage(), $e->getCode() );
		}

		// generate our html
		$html = '';

		// only do this if continue is not null
		if ( !$continue && !$params['noheader'] ) {
			$result->addValue( $this->getModuleName(), 'hasHeader', true );

			$html .=
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-activity-pane' ),
					Html::rawElement(
						'div',
						array( 'class' => 'articleFeedbackv5-activity-feedback' ),
						Html::rawElement(
							'div',
							array(),
							wfMessage( 'articlefeedbackv5-activity-feedback-info' )
								->params( $feedback->aft_id )
								->rawParams( ArticleFeedbackv5Utils::getUserLink( $feedback->aft_user, $feedback->aft_user_text ) )
								->params( $feedback->aft_user_text ) // username or ip
								->text()
						) .
						Html::element(
							'div',
							array(),
							wfMessage( 'articlefeedbackv5-activity-feedback-date' )
								->params(
									$wgLang->userTimeAndDate( $feedback->aft_timestamp, $wgUser ),
									$wgLang->userDate( $feedback->aft_timestamp, $wgUser ),
									$wgLang->userTime( $feedback->aft_timestamp, $wgUser )
								)->text()
						) .
						Html::rawElement(
							'div',
							array( 'class' => 'articleFeedbackv5-activity-feedback-permalink' ),
							Linker::link(
								SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getPrefixedDBkey() . '/' . $feedback->aft_id ),
								wfMessage( 'articlefeedbackv5-activity-permalink' )->text()
							)
						)
					) .
					Html::element(
						'div',
						array( 'class' => 'articleFeedbackv5-activity-count' ),
						wfMessage( 'articlefeedbackv5-activity-count' )->numParams( ArticleFeedbackv5Activity::getActivityCount( $feedback ) )->text()
					)
				);

			$html .=
				Html::openElement(
					'div',
					array( 'class' => 'articleFeedbackv5-activity-log-items' )
				);
		}

		$count = 0;

		// divs of activity items
		foreach ( $activities as $item ) {
			// skip item if user is not permitted to see it
			if ( !ArticleFeedbackv5Activity::canPerformAction( $item->log_action, $wgUser ) ) {
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
					array( 'class' => 'articleFeedbackv5-activity-item' ),
					Html::rawElement(
						'span',
						array( 'class' => "articleFeedbackv5-activity-item-action articleFeedbackv5-activity-item-action-$sentiment" ),
						wfMessage( 'articlefeedbackv5-activity-item-' . $item->log_action )
							->rawParams(
								ArticleFeedbackv5Utils::getUserLink( $item->log_user, $item->log_user_text ),
								Linker::commentBlock( $item->log_comment ),
								Html::element( 'span', array(), $wgLang->userTimeAndDate( $item->log_timestamp, $wgUser ) ),
								Html::element( 'span', array(), $wgLang->userDate( $item->log_timestamp, $wgUser ) ),
								Html::element( 'span', array(), $wgLang->userTime( $item->log_timestamp, $wgUser ) )
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
					array(
						'class' => "articleFeedbackv5-activity-more",
						'href' => '#',
					),
					wfMessage( "articlefeedbackv5-activity-more" )->text()
				);
		}

		$html .= Html::closeElement( 'div' );

		// finally add our generated html data
		$result->addValue( $this->getModuleName(), 'limit', $limit );
		$result->addValue( $this->getModuleName(), 'activity', $html );

		// continue only goes in if it's not empty
		if ( $count > $limit ) {
			$this->setContinueEnumParameter( 'continue', ArticleFeedbackv5Activity::getContinue( $item ) );
		}

		wfProfileOut( __METHOD__ );
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
				ApiBase::PARAM_TYPE     => 'string',
			),
			'title' => null,
			'pageid' => array(
				ApiBase::PARAM_TYPE     => 'integer',
			),
			'limit' => array(
				ApiBase::PARAM_DFLT => 25,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			),
			'continue' => null,
			'noheader' => array(
				ApiBase::PARAM_TYPE => 'boolean',
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
			'feedbackid' => 'ID of article feedback to get activity for',
			'title' => "Title of the page the feedback was given for. Cannot be used together with {$p}pageid",
			'pageid' => "ID of the page the feedback was given for. Cannot be used together with {$p}title",
			'limit' => 'How many activity results to return',
			'continue' => 'When more results are available, use this to continue',
			'noheader' => 'Skip the header markup, even if this is the first page',
		);
	}

	/**
	 * Gets the api descriptions
	 *
	 * @return array the description as the first element in an array
	 */
	public function getDescription() {
		return array(
			'List article feedback activity for a specified page'
		);
	}

	/**
	 * Gets an example
	 *
	 * @return array the example as the first element in an array
	 */
	protected function getExamples() {
		return array(
			'api.php?action=query&list=articlefeedbackv5-view-activity&aafeedbackid=429384108662e9d4e41ab6e275d0392e&aapageid=1',
		);
	}
}
