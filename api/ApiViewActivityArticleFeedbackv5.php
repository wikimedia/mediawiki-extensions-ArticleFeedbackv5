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

		// fetch our activity database information
		$feedback = ArticleFeedbackv5Model::get( $params['feedbackid'], $params['pageid'] );
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
							wfMessage( 'articlefeedbackv5-activity-feedback-info', array( $feedback->aft_id ) )
								->rawParams( ArticleFeedbackv5Utils::getUserLink( $feedback->aft_user, $feedback->aft_user_text ) )
								->text()
						) .
						Html::element(
							'div',
							array(),
							wfMessage( 'articlefeedbackv5-activity-feedback-date', array( $wgLang->timeanddate( $feedback->aft_timestamp ) ) )->text()
						) .
						Html::rawElement(
							'div',
							array( 'class' => 'articleFeedbackv5-activity-feedback-permalink' ),
							Linker::link(
								SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getDBKey() . '/' . $feedback->aft_id ),
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
								Html::element( 'span', array(), $wgLang->timeanddate( $item->log_timestamp ) ),
								Html::element( 'span', array(), $wgLang->date( $item->log_timestamp ) ),
								Html::element( 'span', array(), $wgLang->time( $item->log_timestamp ) )
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
			'pageid' => array(
				ApiBase::PARAM_REQUIRED => true,
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
		return array(
			'feedbackid' => 'ID to get feedback activity for',
			'pageid' => 'ID to of the page the feedback was given for',
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
			'api.php?action=query&list=articlefeedbackv5-view-activity&affeedbackid=1',
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
}

