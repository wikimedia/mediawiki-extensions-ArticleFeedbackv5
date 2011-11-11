<?php
# ApiArticleFeedback and ApiQueryArticleFeedback don't descend from the same
# parent, which is why these are all static methods instead of just a parent
# class with inheritable methods. I don't get it either.
class ApiArticleFeedbackv5Utils {
	public static function getAnonToken($params) {
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

		public static function isFeedbackEnabled($params) {
return 1;
				global $wgArticleFeedbackNamespaces;
				$title = Title::newFromID( $params['pageid'] );
error_log("page $title is ".$title->getNamespace());
				# TODO check user permissions
				if (
						// not an existing page?
						is_null( $title )
						// Namespace not a valid ArticleFeedback namespace?
						|| !in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces )
						// Page a redirect?
						|| $title->isRedirect()
				) {
						// ...then feedback diabled
						return 0;
				}

				return 1;
		}

		public static function getRevisionId($pageId) {
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

	# TODO: Find a way to cache this, instesd of hitting the DB every pageload.
	public static function getFields() {
		$dbr = wfGetDB( DB_SLAVE );
		$rv  = $dbr->select(
			'aft_article_field',
			array( 'aaf_name', 'aaf_id', 'aaf_data_type' )
		);

		return $rv;
	}
}
