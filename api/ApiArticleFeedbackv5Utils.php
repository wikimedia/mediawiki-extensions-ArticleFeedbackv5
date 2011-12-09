<?php
/**
 * ApiViewRatingsArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Api
 * @author     Greg Chiasson <greg@omniti.com>
 * @author     Reha Sterbin <reha@omniti.com>
 * @version    $Id: ApiViewRatingsArticleFeedbackv5.php 103335 2011-11-16 16:25:53Z gregchiasson $
 */

/**
 * Utility methods used by api calls
 *
 * ApiArticleFeedback and ApiQueryArticleFeedback don't descend from the same
 * parent, which is why these are all static methods instead of just a parent
 * class with inheritable methods. I don't get it either.
 *
 * @package    ArticleFeedback
 * @subpackage Api
 */
class ApiArticleFeedbackv5Utils {
	/**
	 * Returns whether feedback is enabled for this page
	 *
	 * @param  $params array the params
	 * @return bool
	 */
	public static function isFeedbackEnabled( $params ) {
		global $wgArticleFeedbackv5Namespaces;
		$title = Title::newFromID( $params['pageid'] );
		if (
			// not an existing page?
			is_null( $title )
			// Namespace not a valid ArticleFeedback namespace?
			|| !in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces )
			// Page a redirect?
			|| $title->isRedirect()
		) {
			// ...then feedback disabled
			return false;
		}
		return true;
	}

	/**
	 * Returns the revision limit for a page
	 *
	 * @param  $pageId int the page id
	 * @return int the revision limit
	 */
	public static function getRevisionLimit( $pageId ) {
		global $wgArticleFeedbackv5RatingLifetime;
		$dbr      = wfGetDB( DB_SLAVE );
		$revision = $dbr->selectField(
			'revision', 'rev_id',
			array( 'rev_page' => $pageId ),
			__METHOD__,
			array(
				'ORDER BY' => 'rev_id DESC',
				'LIMIT'    => 1,
				'OFFSET'   => $wgArticleFeedbackv5RatingLifetime - 1
			)
		);
		return $revision ? intval( $revision ) : 0;
	}

	/**
	 * Gets the known feedback fields
	 *
	 * @return ResultWrapper the rows in the aft_article_field table
	 */
	public static function getFields() {
		global $wgMemc;

		$key    = wfMemcKey( 'articlefeedbackv5', 'getFields' );
		$cached = $wgMemc->get( $key );

		if( $cached != '' ) {
			return $cached;
		} else {
			$dbr = wfGetDB( DB_SLAVE );
			$rv  = $dbr->select(
				'aft_article_field',
				array( 
					'afi_name', 
					'afi_id', 
					'afi_data_type', 
					'afi_bucket_id' 
				),
				null, 
				__METHOD__
			);
			// An hour? That might be reasonable for a cache time.
			$wgMemc->set( $key, $rv, 60 * 60 );
		}

		return $rv;
	}

	/**
	 * Gets the known feedback options
	 *
	 * Pulls all the rows in the aft_article_field_option table, then 
	 * arranges them like so:
	 *   {field id} => array(
	 *       {option id} => {option name},
	 *   ),
	 *
	 * @return array the rows in the aft_article_field_option table
	 */
	public static function getOptions() {
                global $wgMemc;

                $key    = wfMemcKey( 'articlefeedbackv5', 'getOptions' );
                $cached = $wgMemc->get( $key );

                if( $cached != '' ) {
                        return $cached;
                } else {
			$rv   = array();
			$dbr  = wfGetDB( DB_SLAVE );
			$rows = $dbr->select(
				'aft_article_field_option',
				array( 
					'afo_option_id', 
					'afo_field_id', 
					'afo_name' 
				),
				null, 
				__METHOD__
			);
			foreach ( $rows as $row ) {
				$rv[$row->afo_field_id][$row->afo_option_id] = $row->afo_name;
			}
                        // An hour? That might be reasonable for a cache time.
                        $wgMemc->set( $key, $rv, 60 * 60 );
                }
		return $rv;
	}
}

