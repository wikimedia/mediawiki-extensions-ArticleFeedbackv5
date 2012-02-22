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
		parent::__construct( $query, $moduleName, 'af' );
	}

	/**
	 * Execute the API call: Pull max 25 activity log items for page
	 */
	public function execute() {
		global $wgUser; // we need to check permissions in here
		global $wgLang; // timestamp formats

		// If we can't hide, we can't see activity, return an empty string
		// front-end should never let you get here, but just in case
		if( !$wgUser->isAllowed( 'aftv5-hide-feedback' )) {
			return;
		}

		// These are our valid activity log actions
		$valid = array( 'oversight', 'unoversight', 'hidden', 'unhidden',
				'decline', 'request', 'unrequest','flag','unflag' );

		// get our parameter information
		$params = $this->extractRequestParams();
		$feedbackId        = $params['feedbackid'];
		$limit = $params['limit'];
		$continue = $params['continue'];

		// fetch our activity database information
		$feedback    = $this->fetchFeedback( $feedbackId );
		// if this is false, this is bad feedback - move along
		if( !$feedback) {
			return;
		}

		// get the string title for the page
		$page = Title::newFromID( $feedback->af_page_id );
		$title = $page->getPartialURL();

		// get our activities
		$activities = $this->fetchActivity( $title, $feedbackId, $limit, $continue);
		$old_continue = $continue;

		// overwrite previous continue for new value
		$continue = null;

		// generate our html
		$html = '';

		// only do this if continue < 1
		if ($old_continue < 1) {
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
						array($feedback->af_id))->text()
				. $this->getUserLink($feedback->af_user_id, $feedback->af_user_ip);
			$html .= Html::closeElement( 'div' );
	
			//<div>Posted on {$date} (UTC)</div>
			$html .= Html::element( 'div', array(),
				wfMessage( 'articlefeedbackv5-activity-feedback-date',
						array( $wgLang->timeanddate( $feedback->af_created ) ))->text() );
	
			// <div class="articleFeedbackv5-activity-feedback-permalink">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-feedback-permalink'
			) );
	
			// <a href="{$permalink}">permalink</a>
			$html .= Linker::link(
				SpecialPage::getTitleFor( 'ArticleFeedbackv5', $title . '/'. $feedback->af_id ),
				wfMessage( 'articlefeedbackv5-activity-permalink' )->text());
	
			// </div> for class="articleFeedbackv5-activity-feedback-permalink"
			$html .= Html::closeElement( 'div' );
	
			// </div> for class="articleFeedbackv5-activity-feedback"
			$html .= Html::closeElement( 'div' );
	
			//<div class="articleFeedbackv5-activity-count">$n actions on this post</div>
			$html .= Html::element( 'div', array('class' => 'articleFeedbackv5-activity-count'),
				wfMessage( 'articlefeedbackv5-activity-count',
						array( $feedback->af_activity_count ))->text() );
			
			// </div> for class="articleFeedbackv5-activity-pane"
			$html .= Html::closeElement( 'div' );
	
			//<div class="articleFeedbackv5-activity-log-items">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-log-items'
			) );
		}

		// divs of activity items
		foreach($activities as $item) {

			// if we do not have a valid action, skip this item
			if ( !in_array( $item->log_action, $valid )) {
				continue;
			}

			// <div class="articleFeedbackv5-activity-item">
			$html .= Html::openElement( 'div', array(
				'class' => 'articleFeedbackv5-activity-item'
			) );

			// $user $did_something_on $date
			$html .= $this->getUserLink($item->log_user, $item->log_user_text)
				. Html::element( 'span', array(
						'class' => 'articleFeedbackv5-activity-item-action'
					),
					wfMessage( 'articlefeedbackv5-activity-' . $item->log_action,
						array())->text() )
				. $wgLang->timeanddate( $item->log_timestamp );

			// optional: <div class="articleFeedbackv5-activity-notes">$notes</div>
			if (!empty($item->log_comment)) {
				$html .= Html::element( 'span',
							array('class' => 'articlefeedbackv5-activity-notes'),
							': ' . $item->log_comment);
			}

			// </div> for class="articleFeedbackv5-activity-item"
			$html .= Html::closeElement( 'div' );

			// the last item's log_id should be the continue;
			$continue = $item->log_id;
		}

		// figure out if we have more based on our new continue value
		$more = $this->fetchHasMore($title, $feedbackId, $continue);

		//optional <a href="#" class="articleFeedbackv5-activity-more">Show more Activity</a>
		if ($more) {
			$html .= Html::element( 'a', array(
					'class' => "articleFeedbackv5-activity-more",
					'href' => '#',
				), wfMessage( "articlefeedbackv5-activity-more" )->text() );
		}

		// </div> for class="acticleFeedbackv5-activity-log-items"
		$html .= Html::closeElement( 'div' );
		
		// finally add our generated html data
		$result = $this->getResult();
		$result->addValue( $this->getModuleName(), 'limit', $limit );
		$result->addValue( $this->getModuleName(), 'activity', $html );

		// continue only goes in if it's not empty
		if ($continue > 0) {
			$result->addValue( $this->getModuleName(), 'continue', $continue );
		}

		// more only goes in if there are more entries
		if ($more) {
			$result->addValue( $this->getModuleName(), 'more', $more );
		}
	}

	/**
	 * Sees if there are additional activity rows to view
	 *
	 * @param string $title the title of the page
	 * @param int $feedbackId identifier for the feedback item we are fetching activity for
	 * @param mixed $continue used for offsets
	 * @return bool true if there are more rows, or false
	 */
	protected function fetchHasMore( $title, $feedbackId, $continue = null ) {
		$dbr   = wfGetDB( DB_SLAVE );

		$feedback = $dbr->selectField(
			array( 'logging' ),
			array( 'log_id'),
			array(
				'log_type' => 'articlefeedbackv5',
				'log_title' => "ArticleFeedbackv5/$title/$feedbackId",
				'log_id < ' . intval($continue)
			),
			__METHOD__,
			array(
				'LIMIT'    => 1
			)
		);

		return ( (bool) $feedback );
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
				'af_activity_count'),
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
	protected function fetchActivity( $title, $feedbackId, $limit = 25, $continue = null) {

		$where = array (
				'log_type' => 'articlefeedbackv5',
				'log_title' => "ArticleFeedbackv5/$title/$feedbackId"
			);

		if ( null !== $continue ) {
			$where[] = 'log_id < ' . intval($continue);
		}

		$dbr   = wfGetDB( DB_SLAVE );
		$activity = $dbr->select(
			array( 'logging' ),
			array( 'log_id',
				'log_action',
				'log_timestamp',
				'log_user',
				'log_user_text',
				'log_title',
				'log_comment'),
			$where,
			__METHOD__,
			array(
				'LIMIT'    => $limit,
				'ORDER BY' => 'log_timestamp DESC, log_id ASC'
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
	 * Creates a user link for a log row
	 *
	 * @param stdClass $item row from log table db
	 * @return string the SVN version info
	 */
	protected function getUserLink($user_id, $user_ip) {
		$userId = (int) $user_id;
		if ( $userId !== 0 ) { // logged-in users
			$user = User::newFromId( $userId );
		} else { // IP users
			$userText = $user_ip;
			$user = User::newFromName( $userText, false );
		}

		$element = Linker::userLink(
				$user->getId(),
				$user->getName()
			);
		return $element;
	}
}

