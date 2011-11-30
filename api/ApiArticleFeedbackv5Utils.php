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
	 * Gets the anonymous token from the params
	 *
	 * @param  $params array the params
	 * @return string the token, or null if the user is not anonymous
	 */
	public static function getAnonToken( $params ) {
		global $wgUser;
		$token = null;
		if ( $wgUser->isAnon() ) {
# TODO: error handling
			if ( !isset( $params['anontoken'] ) ) {
#                                $this->dieUsageMsg( array( 'missingparam', 'anontoken' ) );
			} elseif ( strlen( $params['anontoken'] ) != 32 ) {
#                                $this->dieUsage( 'The anontoken is not 32 characters', 'invalidtoken' );
			}
			$token = $params['anontoken'];
		} else {
			$token = '';
		}
		return $token;
	}

	/**
	 * Returns whether feedback is enabled for this page
	 *
	 * @param  $params array the params
	 * @return bool
	 */
	public static function isFeedbackEnabled( $params ) {
		global $wgArticleFeedbackNamespaces;
		$title = Title::newFromID( $params['pageid'] );
		if (
			// not an existing page?
			is_null( $title )
			// Namespace not a valid ArticleFeedback namespace?
			|| !in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces )
			// Page a redirect?
			|| $title->isRedirect()
		) {
			// ...then feedback diabled
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
	 * Gets the most recent revision id for a page id
	 *
	 * @param  $pageId int the page id
	 * @return int the revision id
	 */
	public static function getRevisionId( $pageId ) {
		$dbr   = wfGetDB( DB_SLAVE );
		$revId = $dbr->selectField(
			'revision', 'rev_id',
			array( 'rev_page' => $pageId ),
			__METHOD__,
			array(
				'ORDER BY' => 'rev_id DESC',
				'LIMIT'    => 1
			)
		);

		return $revId;
	}

	/**
	 * Gets the known feedback fields
	 *
	 * TODO: use memcache
	 *
	 * @return array the rows in the aft_article_field table
	 */
	public static function getFields() {
		$dbr = wfGetDB( DB_SLAVE );
		$rv  = $dbr->select(
			'aft_article_field',
			array( 'afi_name', 'afi_id', 'afi_data_type', 'afi_bucket_id' )
		);
		return $rv;
	}

	/**
	 * Gets the known feedback options
	 *
	 * Pulls all the rows in the aft_article_field_option table, then arranges
	 * them like so:
	 *   {field id} => array(
	 *       {option id} => {option name},
	 *   ),
	 *
	 * TODO: use memcache
	 *
	 * @return array the rows in the aft_article_field_option table
	 */
	public static function getOptions() {
		$dbr  = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			'aft_article_field_option',
			array( 'afo_option_id', 'afo_field_id', 'afo_name' )
		);
		$rv = array();
		foreach ( $rows as $row ) {
			if ( !isset( $rv[$row->afo_field_id] ) ) {
				$rv[$row->afo_field_id] = array();
			}
			$rv[$row->afo_field_id][$row->afo_option_id] = $row->afo_name;
		}
		return $rv;
	}

}

