<?php
/**
 * ArticleFeedbackv5_RefreshFilterCounts class
 *
 * @package    ArticleFeedbackv5
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id$
 */

require_once( dirname( __FILE__ ) . '/../../../maintenance/Maintenance.php' );

/**
 * Refresh the filter counts
 *
 * @package    ArticleFeedbackv5
 */
class ArticleFeedbackv5_RefreshFilterCounts extends Maintenance {

	/**
	 * Filter info
	 *
	 * @var array
	 */
	public static $filters = array(
		'visible-relevant'       => array( 'where' => '((af_is_featured IS TRUE OR af_has_comment IS TRUE OR af_net_helpfulness > 0) AND af_relevance_score > -5)' ),
		'visible-featured'       => array( 'where' => 'af_is_featured IS TRUE' ),
		'visible-unfeatured'     => array( 'where' => 'af_is_unfeatured IS TRUE' ),
		'visible-resolved'       => array( 'where' => 'af_is_resolved IS TRUE' ),
		'visible-unresolved'     => array( 'where' => 'af_is_unresolved IS TRUE' ),
		'visible-comment'        => array( 'where' => 'af_has_comment IS TRUE' ),
		'visible-helpful'        => array( 'where' => 'af_net_helpfulness > 0' ),
		'visible-unhelpful'      => array( 'where' => 'af_net_helpfulness > 0' ),
		'visible-abusive'        => array( 'where' => 'af_abuse_count > 0' ),
		'notdeleted-hidden'      => array(),
		'all-hidden'             => array(),
		'notdeleted-unhidden'    => array( 'where' => 'af_is_unhidden IS TRUE' ),
		'all-unhidden'           => array( 'where' => 'af_is_unhidden IS TRUE' ),
		'notdeleted-requested'   => array( 'where' => 'af_oversight_count > 0' ),
		'all-requested'          => array( 'where' => 'af_oversight_count > 0' ),
		'notdeleted-unrequested' => array( 'where' => 'af_is_unrequested IS TRUE' ),
		'all-unrequested'        => array( 'where' => 'af_is_unrequested IS TRUE' ),
		'notdeleted-declined'    => array( 'where' => 'af_is_declined IS TRUE' ),
		'all-declined'           => array( 'where' => 'af_is_declined IS TRUE',),
		'all-oversighted'        => array( 'where' => 'af_is_deleted IS TRUE' ),
		'all-unoversighted'      => array( 'where' => 'af_is_undeleted IS TRUE' ),
		'visible'                => array(),
		'notdeleted'             => array(),
		'all'                    => array(),
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->mDescription = 'Refresh the filter counts for all feedback pages';
	}

	/**
	 * Execute the script
	 */
	public function execute() {
		$this->output( "Refreshing filter counts...\n" );
		$dbw = wfGetDB( DB_MASTER );

		$dbw->delete( 'aft_article_filter_count', '*', __METHOD__ );

		foreach ( self::$filters as $filter => $info ) {
			$where = array( '( af_form_id = 1 OR af_form_id = 6 )' );
			if ( isset( $info['where'] ) ) {
				$where[] = $info['where'];
			}
			if ( substr( $filter, 0, 7 ) == 'visible' ) {
				$where[] = 'af_is_deleted IS FALSE';
				$where[] = 'af_is_hidden IS FALSE';
			} elseif ( substr( $filter, 0, 10 ) == 'notdeleted' ) {
				$where[] = 'af_is_deleted IS FALSE';
			}
			// Page filters
			$dbw->insertSelect(
				'aft_article_filter_count',
				'aft_article_feedback',
				array(
					'afc_page_id' => 'af_page_id',
					'afc_filter_name' => $dbw->addQuotes( $filter ),
					'afc_filter_count' => 'COUNT(*)',
				),
				$where,
				__METHOD__,
				array(),
				array(
					'GROUP BY' => 'af_page_id',
				)
			);
			// Central filters
			$dbw->insertSelect(
				'aft_article_filter_count',
				'aft_article_feedback',
				array(
					'afc_page_id' => 0,
					'afc_filter_name' => $dbw->addQuotes( $filter ),
					'afc_filter_count' => 'COUNT(*)',
				),
				$where,
				__METHOD__,
				array(),
				array()
			);
			// Wait for slaves before moving on
			wfWaitForSlaves();
		}

		$this->output( "done.\n" );
	}

}

$maintClass = "ArticleFeedbackv5_RefreshFilterCounts";
require_once( RUN_MAINTENANCE_IF_MAIN );

