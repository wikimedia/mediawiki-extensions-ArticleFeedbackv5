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

		global $wgUser; // we need to check permissions in here
		global $wgLang; // timestamp formats

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
		$page = Title::newFromID( $feedback->page );
		if ( !$page ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( "Page for feedback does not exist", 'invalidfeedbackid' );
		}

		// get our activities
		$activities = $this->fetchActivity( $feedback, $limit, $continue );

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
							wfMessage( 'articlefeedbackv5-activity-feedback-info', array( $feedback->id ) )
								->rawParams( ApiArticleFeedbackv5Utils::getUserLink( $feedback->user, $feedback->user_text ) )
								->text()
						) .
						Html::element(
							'div',
							array(),
							wfMessage( 'articlefeedbackv5-activity-feedback-date', array( $wgLang->timeanddate( $feedback->timestamp ) ) )->text()
						) .
						Html::rawElement(
							'div',
							array( 'class' => 'articleFeedbackv5-activity-feedback-permalink' ),
							Linker::link(
								SpecialPage::getTitleFor( 'ArticleFeedbackv5', $page->getDBKey() . '/' . $feedback->id ),
								wfMessage( 'articlefeedbackv5-activity-permalink' )->text()
							)
						)
					) .
					Html::element(
						'div',
						array( 'class' => 'articleFeedbackv5-activity-count' ),
						wfMessage( 'articlefeedbackv5-activity-count' )->numParams( ApiArticleFeedbackv5Utils::getActivityCount( $feedback ) )->text()
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
			if ( !ArticleFeedbackv5Model::canPerformAction( $item->log_action, $wgUser ) ) {
				continue;
			}

			// figure out if we have more if we have another row past our limit
			$count++;
			if ( $count > $limit ) {
				break;
			}

			$sentiment = ArticleFeedbackv5Model::$actions[$item->log_action]['sentiment'];

			$html .=
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-activity-item' ),
					Html::rawElement(
						'span',
						array( 'class' => "articleFeedbackv5-activity-item-action articleFeedbackv5-activity-item-action-$sentiment" ),
						wfMessage( 'articlefeedbackv5-activity-item-' . $item->log_action )
							->rawParams(
								ApiArticleFeedbackv5Utils::getUserLink( $item->log_user, $item->log_user_text ),
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
			$this->setContinueEnumParameter( 'continue', $this->getContinue( $item ) );
		}

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Gets the last 25 (or a requested continuance) of activity rows taken
	 * from the log table
	 *
	 * @param ArticleFeedbackv5Model $feedback identifier for the feedback item we are fetching activity for
	 * @param int $limit total limit number
	 * @param mixed $continue used for offsets
	 * @return array db record rows
	 */
	protected function fetchActivity( $feedback, $limit = 25, $continue = null ) {
		global $wgLogActionsHandlers;
		$dbr = wfGetDB( DB_SLAVE );

		$feedbackId = $feedback->id;
		$page = Title::newFromID( $feedback->page );
		if ( !$page ) {
			wfProfileOut( __METHOD__ );
			$this->dieUsage( 'Page for feedback does not exist', 'invalidfeedbackid' );
		}
		$title = $page->getDBKey();

		// can only see activity for actions that you have permissions to perform
		$actions = array();
		foreach( ArticleFeedbackv5Model::$actions as $action => $options ) {
			if ( ArticleFeedbackv5Model::canPerformAction( $action ) ) {
				if ( isset( $wgLogActionsHandlers["suppress/$action"] ) ) {
					$type = 'suppress';
				} elseif ( isset( $wgLogActionsHandlers["articlefeedbackv5/$action"] ) ) {
					$type = 'articlefeedbackv5';
				} else {
					continue;
				}
				$actions[] = 'log_type = '.$dbr->addQuotes( $type ).' AND log_action = '.$dbr->addQuotes( $action );
			}
		}

		// nothing to get? return empty resultset
		if ( !$actions ) {
			return new FakeResultWrapper( array() );
		}

		$where[] = implode( ' OR ', $actions );
		$where['log_namespace'] = NS_SPECIAL;
		$where['log_title'] = "ArticleFeedbackv5/$title/$feedbackId";
		$where = $this->applyContinue( $continue, $where );

		$activity = $dbr->select(
			array( 'logging' ),
			array(
				'log_id',
				'log_action',
				'log_timestamp',
				'log_user',
				'log_user_text',
				'log_title',
				'log_comment'
			),
			$where,
			__METHOD__,
			array(
				'LIMIT' => $limit + 1,
				'ORDER BY' => 'log_timestamp DESC',
				// Force the page_time index (on _namespace, _title, _timestamp)
				// We don't expect many if any rows for Special:ArticleFeedbackv5/foo that
				// don't match log_type='articlefeedbackv5' , so we can afford to have that
				// clause be unindexed. The alternative is to have the log_type clause be indexed
				// and the namespace/title clauses unindexed, that would be bad.
				'USE INDEX' => 'page_time'
			)
		);

		return $activity;
	}

	/**
	 * Creates a timestamp/id tuple for continue
	 */
	protected function getContinue( $row ) {
		$ts = wfTimestamp( TS_MW, $row->log_timestamp );
		return "$ts|{$row->log_id}";
	}

	/**
	 * gets timestamp and id pair for continue
	 */
	protected function applyContinue( $continue, $where ) {
		if ( !$continue ) {
			return $where;
		}

		$vals = explode( '|', $continue, 3 );
		if ( count( $vals ) !== 2 ) {
			$this->dieUsage( 'Invalid continue param. You should pass the original value returned by the previous query', 'badcontinue' );
		}

		$db = $this->getDB();
		$ts = $db->addQuotes( $db->timestamp( $vals[0] ) );
		$id = intval( $vals[1] );
		$where[] = '(log_id = ' . $id . ' AND log_timestamp <= ' . $ts . ') OR log_timestamp < ' . $ts;

		return $where;
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
				ApiBase::PARAM_TYPE     => 'integer',
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

