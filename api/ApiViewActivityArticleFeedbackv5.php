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
		global $wgUser; // we need to check permissions in here
		global $wgLang; // timestamp formats

		// If we can't hide, we can't see activity, return an empty string
		// front-end should never let you get here, but just in case
		if ( !$wgUser->isAllowed( 'aftv5-hide-feedback' ) ) {
			$this->dieUsage( "You don't have permission to hide feedback", 'permissiondenied' );
		}

		// These are our valid activity log actions
		$valid = array( 'oversight', 'unoversight', 'hidden', 'unhidden',
				'decline', 'request', 'unrequest', 'flag', 'unflag' );

		// get our parameter information
		$params = $this->extractRequestParams();
		$feedbackId        = $params['feedbackid'];
		$limit = $params['limit'];
		$continue = $params['continue'];
		$result = $this->getResult();

		// fetch our activity database information
		$feedback    = $this->fetchFeedback( $feedbackId );
		// if this is false, this is bad feedback - move along
		if ( !$feedback ) {
			$this->dieUsage( "Feedback does not exist", 'invalidfeedbackid' );
		}

		// get the string title for the page
		$page = Title::newFromID( $feedback->af_page_id );
		if ( !$page ) {
			$this->dieUsage( "Page for feedback does not exist", 'invalidfeedbackid' );
		}
		$title = $page->getDBKey();

		// get our activities
		$activities = $this->fetchActivity( $title, $feedbackId, $limit, $continue );

		// generate our html
		$html = '';

		// only do this if continue is not null
		if ( !$continue ) {
			$result->addValue( $this->getModuleName(), 'hasHeader', true );

			// <div class="articleFeedbackv5-activity-pane">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-pane'
			) );

			// <div class="articleFeedbackv5-activity-feedback">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-feedback'
			) );

			// <div>Feedback Post #{$feedbackid} by {$user_link}</div>
			$html .= Html::openElement( 'div', array() );
			$html .= wfMessage( 'articlefeedbackv5-activity-feedback-info',
						array( $feedback->af_id ) )
					->rawParams( ApiArticleFeedbackv5Utils::getUserLink( $feedback->af_user_id, $feedback->af_user_ip ) )
					->text();
			$html .= Html::closeElement( 'div' );

			// <div>Posted on {$date} (UTC)</div>
			$html .= Html::element( 'div', array(),
				wfMessage( 'articlefeedbackv5-activity-feedback-date',
						array( $wgLang->timeanddate( $feedback->af_created ) ) )->text() );

			// <div class="articleFeedbackv5-activity-feedback-permalink">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-feedback-permalink'
			) );

			// <a href="{$permalink}">permalink</a>
			$html .= Linker::link(
				SpecialPage::getTitleFor( 'ArticleFeedbackv5', $title . '/' . $feedback->af_id ),
				wfMessage( 'articlefeedbackv5-activity-permalink' )->text() );

			// </div> for class="articleFeedbackv5-activity-feedback-permalink"
			$html .= Html::closeElement( 'div' );

			// </div> for class="articleFeedbackv5-activity-feedback"
			$html .= Html::closeElement( 'div' );

			// <div class="articleFeedbackv5-activity-count">$n actions on this post</div>
			if ( $wgUser->isAllowed( 'aftv5-delete-feedback' ) ) {
				$activity_count = $feedback->af_activity_count + $feedback->af_suppress_count;
			} else {
				$activity_count = $feedback->af_activity_count;
			}
			$html .= Html::element( 'div', array( 'class' => 'articleFeedbackv5-activity-count' ),
					wfMessage( 'articlefeedbackv5-activity-count' )->numParams( $activity_count )->text() );

			// </div> for class="articleFeedbackv5-activity-pane"
			$html .= Html::closeElement( 'div' );

			// <div class="articleFeedbackv5-activity-log-items">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-log-items'
			) );
		}

		$count = 0;

		// divs of activity items
		foreach ( $activities as $item ) {

			// if we do not have a valid action, skip this item
			if ( !in_array( $item->log_action, $valid ) ) {
				continue;
			}

			$count++;

			// figure out if we have more if we have another row past our limit
			if ( $count > $limit ) {
				break;
			}

			// <div class="articleFeedbackv5-activity-item">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-item'
			) );

			// so because concatenation is evil, I have to figure out which format to use
			// either the $user $did_something_on $date
			// or the $user $did_something_on $date : $comment
			// because the colon hanging around would look utterly stupid

			if ( $item->log_comment == '' ) {
				$html .= wfMessage( 'articlefeedbackv5-activity-item' )
					->rawParams(
						ApiArticleFeedbackv5Utils::getUserLink( $item->log_user, $item->log_user_text ),
						Html::element( 'span', array(
							'class' => 'articleFeedbackv5-activity-item-action'
							),
							wfMessage( 'articlefeedbackv5-activity-' . $item->log_action,
								array() )->text() ),
						$wgLang->timeanddate( $item->log_timestamp ) )
					->text();
			} else {
				$html .= wfMessage( 'articlefeedbackv5-activity-item-comment' )
					->rawParams(
						ApiArticleFeedbackv5Utils::getUserLink( $item->log_user, $item->log_user_text ),
						Html::element( 'span', array(
						'class' => 'articleFeedbackv5-activity-item-action'
							),
							wfMessage( 'articlefeedbackv5-activity-' . $item->log_action,
								array() )->text() ),
						$wgLang->timeanddate( $item->log_timestamp ),
						Html::element( 'span',
							array( 'class' => 'articlefeedbackv5-activity-notes' ),
							$item->log_comment ) )
					->text();
			}

			// </div> for class="articleFeedbackv5-activity-item"
			$html .= Html::closeElement( 'div' );
		}

		// optional <a href="#" class="articleFeedbackv5-activity-more">Show more Activity</a>
		if ( $count > $limit ) {
			$html .= Html::element( 'a', array(
					'class' => "articleFeedbackv5-activity-more",
					'href' => '#',
				), wfMessage( "articlefeedbackv5-activity-more" )->text() );
		}

		// </div> for class="acticleFeedbackv5-activity-log-items"
		$html .= Html::closeElement( 'div' );

		// finally add our generated html data
		$result->addValue( $this->getModuleName(), 'limit', $limit );
		$result->addValue( $this->getModuleName(), 'activity', $html );

		// continue only goes in if it's not empty
		if ( $count > $limit ) {
			$this->setContinueEnumParameter( 'continue', $this->getContinue( $item ) );
		}
	}

	/**
	 * Gets some base feedback information
	 *
	 * @param int $feedbackId identifier for the feedback item we are fetching activity for
	 * @return int total number of activity items for feedback item
	 */
	protected function fetchFeedback( $feedbackId ) {
		$dbr   = wfGetDB( DB_SLAVE );

		$feedback = $dbr->selectRow(
			array( 'aft_article_feedback' ),
			array( 'af_id',
				'af_page_id',
				'af_user_id',
				'af_user_ip',
				'af_created',
				'af_activity_count',
				'af_suppress_count' ),
			array(
				'af_id'     => $feedbackId,
			),
			__METHOD__,
			array(
				'LIMIT'    => 1
			)
		);

		return $feedback;
	}

	/**
	 * Gets the last 25 (or a requested continuance) of activity rows taken
	 * from the log table
	 *
	 * @param string $title the title of the page
	 * @param int $feedbackId identifier for the feedback item we are fetching activity for
	 * @param int $limit total limit number
	 * @param mixed $continue used for offsets
	 * @return array db record rows
	 */
	protected function fetchActivity( $title, $feedbackId, $limit = 25, $continue = null ) {
		global $wgUser; // we need to check permissions in here for suppressionlog stuff


		// get afv5 log items PLUS suppress log
		if ( $wgUser->isAllowed( 'aftv5-delete-feedback' ) ) {
			$where = array (
				0 => "(log_type = 'articlefeedbackv5')
					OR (log_type = 'suppress' AND
					(log_action = 'oversight' OR
					 log_action = 'unoversight' OR
					 log_action = 'decline' OR
					 log_action = 'request' OR
					 log_action = 'unrequest'))",
				'log_namespace' => NS_SPECIAL,
				'log_title' => "ArticleFeedbackv5/$title/$feedbackId"
			);
		// get only afv5 log items
		} else {
			$where = array (
				'log_type' => 'articlefeedbackv5',
				'log_namespace' => NS_SPECIAL,
				'log_title' => "ArticleFeedbackv5/$title/$feedbackId"
			);
		}

		$where = $this->applyContinue( $continue, $where );

		$dbr   = wfGetDB( DB_SLAVE );
		$activity = $dbr->select(
			array( 'logging' ),
			array( 'log_id',
				'log_action',
				'log_timestamp',
				'log_user',
				'log_user_text',
				'log_title',
				'log_comment' ),
			$where,
			__METHOD__,
			array(
				'LIMIT'    => $limit + 1,
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
			'limit' => array(
				ApiBase::PARAM_DFLT => 25,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			),
			'continue' => null,
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
			'limit' => 'How many activity results to return',
			'continue' => 'When more results are available, use this to continue',
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
}

