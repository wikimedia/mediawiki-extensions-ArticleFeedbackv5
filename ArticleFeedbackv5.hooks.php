<?php
/**
 * Hooks for ArticleFeedback
 *
 * @file
 * @ingroup Extensions
 */

class ArticleFeedbackv5Hooks {
	/**
	 * LoadExtensionSchemaUpdates hook
	 *
	 * @param $updater DatabaseUpdater
	 *
	 * @return bool
	 */
	public static function loadExtensionSchemaUpdates( $updater = null ) {
		$updater->addExtensionTable(
			'aft_feedback',
			dirname( __FILE__ ) . '/sql/ArticleFeedbackv5.sql'
		);

		// old schema support
		if ( $updater->getDB()->tableExists( 'aft_article_feedback' ) ) {
			$updater->addExtensionTable(
				'aft_article_answer_text',
				dirname( __FILE__ ) . '/sql/offload_large_feedback.sql'
			);

			$updater->addExtensionIndex(
				'aft_article_feedback',
				'af_user_id_user_ip_created',
				dirname( __FILE__ ) . '/sql/index_user_data.sql'
			);

			$updater->modifyField(
				'aft_article_feedback',
				'af_user_ip',
				dirname( __FILE__ ) . '/sql/userip_length.sql',
				true
			);

			// move all data from old schema to new, sharded, schema
			require_once __DIR__.'/maintenance/legacyToShard.php';
			$updater->addPostDatabaseUpdateMaintenance( 'ArticleFeedbackv5_LegacyToShard' );
			/*
			 * Because this update involves moving data around, the old schema
			 * will not automatically be removed (just to be sure no valuable
			 * data is destroyed by accident). After having verified the update
			 * was successful and if you really want to clean out your database
			 * (you don't have to delete it), you can run sql/remove_legacy.sql
			 */
		}

		$updater->addExtensionField(
			'aft_feedback',
			'aft_noaction',
			dirname( __FILE__ ) . '/sql/noaction.sql'
		);

		$updater->addExtensionField(
			'aft_feedback',
			'aft_archive',
			dirname( __FILE__ ) . '/sql/archive.sql'
		);
		// fix archive dates for existing feedback
		require_once __DIR__.'/maintenance/setArchiveDate.php';
		$updater->addPostDatabaseUpdateMaintenance( 'ArticleFeedbackv5_SetArchiveDate' );

		$updater->addExtensionField(
			'aft_feedback',
			'aft_inappropriate',
			dirname( __FILE__ ) . '/sql/inappropriate.sql'
		);

		$updater->addExtensionIndex(
			'aft_feedback',
			'contribs',
			dirname( __FILE__ ) . '/sql/index_contribs.sql'
		);

		$updater->addExtensionIndex(
			'aft_feedback',
			'relevance_page',
			dirname( __FILE__ ) . '/sql/index_page.sql'
		);

		$updater->addExtensionField(
			'aft_feedback',
			'aft_discuss',
			dirname( __FILE__ ) . '/sql/discuss.sql'
		);

		$updater->addExtensionField(
			'aft_feedback',
			'aft_claimed_user',
			dirname( __FILE__ ) . '/sql/claimed_user.sql'
		);

		return true;
	}

	/**
	 * BeforePageDisplay hook - this hook will determine if and what javascript will be loaded
	 *
	 * @param $out OutputPage
	 * @return bool
	 */
	public static function beforePageDisplay( OutputPage &$out, Skin &$skin ) {
		global $wgArticleFeedbackv5Namespaces;
		$title = $out->getTitle();
		$user = $out->getUser();

		// normal page where form can be displayed
		if ( in_array( $title->getNamespace(), $wgArticleFeedbackv5Namespaces ) ) {
			// check if we actually fetched article content & no error page
			if ( $out->getRevisionId() != null ) {
				// load module
				$out->addJsConfigVars( 'aftv5Article', self::getPageInformation( $title ) );
				$out->addModules( 'ext.articleFeedbackv5.startup' );
			}

		// talk page
		} elseif ( in_array( $title->getSubjectPage()->getNameSpace(), $wgArticleFeedbackv5Namespaces ) ) {
			// load module
			$out->addJsConfigVars( 'aftv5Article', self::getPageInformation( $title->getSubjectPage() ) );
			$out->addModules( 'ext.articleFeedbackv5.talk' );

		// special page
		} elseif ( $title->getNamespace() == NS_SPECIAL) {
			// central feedback page, article feedback page, permalink page & watchlist feedback page
			if ( $out->getTitle()->isSpecial( 'ArticleFeedbackv5' ) ||  $out->getTitle()->isSpecial( 'ArticleFeedbackv5Watchlist' ) ) {
				// fetch the title of the article this special page is related to
				list( /* special */, $mainTitle) = SpecialPageFactory::resolveAlias( $out->getTitle()->getDBkey() );

				// Permalinks: drop the feedback ID
				$mainTitle = preg_replace( '/(\/[0-9]+)$/', '', $mainTitle );
				$mainTitle = Title::newFromDBkey( $mainTitle );

				// Central feedback page
				if ( $mainTitle === null ) {
					$article = array(
						'id' => 0,
						'title' => '',
						'namespace' => '-1',
						'categories' => array(),
						'permissionLevel' => ''
					);

				// Article feedback page
				} else {
					$article = self::getPageInformation( $mainTitle );
				}

				// load module
				$out->addJsConfigVars( 'aftv5Article', $article );
				$out->addModules( 'ext.articleFeedbackv5.dashboard' );
			}

			// watchlist page
			elseif ( $out->getTitle()->isSpecial( 'Watchlist' ) ) {
				global $wgArticleFeedbackv5Watchlist;

				if ( $wgArticleFeedbackv5Watchlist && $user->getId() ) {
					$records = ArticleFeedbackv5Model::getWatchlistList(
						'unreviewed',
						$user
					);

					if ( count( $records ) > 0 ) {
						$out->addModules( 'ext.articleFeedbackv5.watchlist' );
					}
				}
			}
		}

		return true;
	}

	/**
	 * This will fetch some page information: the actual check if AFT can be loaded
	 * will be done JS-side (because PHP output may be cached and thus not completely
	 * up-to-date)
	 * However, not all checks can be performed on JS-side - well, they can only be
	 * performed on the article page, not on the talk page & special page. Since these
	 * pages don't have the appropriate information available for Javascript, this
	 * method will build the relevant info.
	 *
	 * @param  Title $title the article
	 * @return array the article's info, to be exposed to JS
	 */
	public static function getPageInformation( Title $title ) {
		$article = array(
			'id' => $title->getArticleID(),
			'title' => $title->getFullText(),
			'namespace' => $title->getNamespace(),
			'categories' => array(),
			'permissionLevel' => ArticleFeedbackv5Permissions::getRestriction( $title->getArticleID() )->pr_level
		);

		foreach ( $title->getParentCategories() as $category => $page ) {
			// get category title without prefix
			$category = Title::newFromDBkey( $category );
			if ( $category ) {
				$article['categories'][] = str_replace( '_', ' ', $category->getDBkey() );
			}
		}

		return $article;
	}

	/**
	 * ResourceLoaderGetConfigVars hook
	 * @param $vars array
	 * @return bool
	 */
	public static function resourceLoaderGetConfigVars( &$vars ) {
		global
			$wgArticleFeedbackv5Credits,
			$wgArticleFeedbackv5Categories,
			$wgArticleFeedbackv5BlacklistCategories,
			$wgArticleFeedbackv5Debug,
			$wgArticleFeedbackv5DisplayBuckets,
			$wgArticleFeedbackv5CTABuckets,
			$wgArticleFeedbackv5Tracking,
			$wgArticleFeedbackv5LinkBuckets,
			$wgArticleFeedbackv5Namespaces,
			$wgArticleFeedbackv5LearnToEdit,
			$wgArticleFeedbackv5SurveyUrls,
			$wgArticleFeedbackv5ThrottleThresholdPostsPerHour,
			$wgArticleFeedbackv5ArticlePageLink,
			$wgArticleFeedbackv5TalkPageLink,
			$wgArticleFeedbackv5WatchlistLink,
			$wgArticleFeedbackv5Watchlist,
			$wgArticleFeedbackv5DefaultSorts,
			$wgArticleFeedbackv5LotteryOdds,
			$wgArticleFeedbackv5MaxCommentLength;

		$vars['wgArticleFeedbackv5Credits'] = $wgArticleFeedbackv5Credits;
		$vars['wgArticleFeedbackv5Categories'] = $wgArticleFeedbackv5Categories;
		$vars['wgArticleFeedbackv5BlacklistCategories'] = $wgArticleFeedbackv5BlacklistCategories;
		$vars['wgArticleFeedbackv5Debug'] = $wgArticleFeedbackv5Debug;
		$vars['wgArticleFeedbackv5Tracking'] = $wgArticleFeedbackv5Tracking;
		$vars['wgArticleFeedbackv5LinkBuckets'] = $wgArticleFeedbackv5LinkBuckets;
		$vars['wgArticleFeedbackv5Namespaces'] = $wgArticleFeedbackv5Namespaces;
		$vars['wgArticleFeedbackv5LearnToEdit'] = $wgArticleFeedbackv5LearnToEdit;
		$vars['wgArticleFeedbackv5SurveyUrls'] = $wgArticleFeedbackv5SurveyUrls;
		$vars['wgArticleFeedbackv5ThrottleThresholdPostsPerHour'] = $wgArticleFeedbackv5ThrottleThresholdPostsPerHour;
		$vars['wgArticleFeedbackv5SpecialUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getLinkUrl();
		$vars['wgArticleFeedbackv5SpecialWatchlistUrl'] = SpecialPage::getTitleFor( 'ArticleFeedbackv5Watchlist' )->getPrefixedText();
		$vars['wgArticleFeedbackv5ArticlePageLink'] = $wgArticleFeedbackv5ArticlePageLink;
		$vars['wgArticleFeedbackv5TalkPageLink'] = $wgArticleFeedbackv5TalkPageLink;
		$vars['wgArticleFeedbackv5WatchlistLink'] = $wgArticleFeedbackv5WatchlistLink;
		$vars['wgArticleFeedbackv5Watchlist'] = $wgArticleFeedbackv5Watchlist;
		$vars['wgArticleFeedbackv5DefaultSorts'] = $wgArticleFeedbackv5DefaultSorts;
		$vars['wgArticleFeedbackv5LotteryOdds'] = $wgArticleFeedbackv5LotteryOdds;
		$vars['wgArticleFeedbackv5MaxCommentLength'] = $wgArticleFeedbackv5MaxCommentLength;

		// make sure that these keys are being encoded to an object rather than to an array
		$wgArticleFeedbackv5DisplayBuckets['buckets'] = (object) $wgArticleFeedbackv5DisplayBuckets['buckets'];
		$wgArticleFeedbackv5CTABuckets['buckets'] = (object) $wgArticleFeedbackv5CTABuckets['buckets'];
		$vars['wgArticleFeedbackv5DisplayBuckets'] = $wgArticleFeedbackv5DisplayBuckets;
		$vars['wgArticleFeedbackv5CTABuckets'] = (object) $wgArticleFeedbackv5CTABuckets;

		return true;
	}

	/**
	 * MakeGlobalVariablesScript hook - this does pretty much the same as the ResourceLoaderGetConfigVars
	 * hook: it makes these variables accessible through JS. However, these are added on a per-page basis,
	 * on the page itself (also setting us free from potential browser cache issues)
	 * @param $vars array
	 * @return bool
	 */
	public static function makeGlobalVariablesScript( &$vars ) {
		global $wgUser;

		// expose AFT permissions for this user to JS
		$vars['wgArticleFeedbackv5Permissions'] = array();
		foreach ( ArticleFeedbackv5Permissions::$permissions as $permission ) {
			$vars['wgArticleFeedbackv5Permissions'][$permission] = $wgUser->isAllowed( $permission ) && !$wgUser->isBlocked();
		}

		return true;
	}

	/**
	 * Add the preference in the user preferences with the GetPreferences hook.
	 * @param $user User
	 * @param $preferences
	 * @return bool
	 */
	public static function getPreferences( $user, &$preferences ) {
		// need to check for existing key, if deployed simultaneously with AFTv4
		if ( !array_key_exists( 'articlefeedback-disable', $preferences ) ) {
			$preferences['articlefeedback-disable'] = array(
				'type' => 'check',
				'section' => 'rendering/advancedrendering',
				'label-message' => 'articlefeedbackv5-disable-preference',
			);
		}
		return true;
	}

	/**
	 * Pushes fields into the edit page. This will allow us to pass on some parameter(s)
	 * until the submission of a page (at which point we can check for these parameters
	 * with a hook in ArticleSaveComplete)
	 *
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/EditPage::showEditForm:fields
	 * @param $editPage EditPage
	 * @param $output OutputPage
	 * @return bool
	 */
	public static function pushFieldsToEdit( $editPage, $output ) {
		// push AFTv5 values back into the edit page form, so we can pick them up after submitting the form
		foreach ( $output->getRequest()->getValues() as $key => $value ) {
			if ( strpos( $key, 'articleFeedbackv5_' ) === 0 ) {
				$editPage->editFormTextAfterContent .= Html::hidden( $key, $value );
			}
		}

		return true;
	}

	/**
	 * Tracks successful edits
	 *
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/ArticleSaveComplete
	 * @param $article WikiPage
	 * @param $user
	 * @param $text
	 * @param $summary
	 * @param $minoredit
	 * @param $watchthis
	 * @param $sectionanchor
	 * @param $flags
	 * @param $revision
	 * @param $status
	 * @param $baseRevId
	 * @return bool
	 */
	public static function editSuccess( &$article, &$user, $text, $summary, $minoredit, $watchthis, $sectionanchor, &$flags, $revision, &$status, $baseRevId /*, &$redirect */ ) {
		if ( $revision instanceof Revision ) {
			$request = RequestContext::getMain()->getRequest();
			$feedbackId = $request->getVal( 'articleFeedbackv5_discuss_id' );
			$pageId = (int) $request->getVal( 'articleFeedbackv5_discuss_page' );
			$discussType = $request->getVal( 'articleFeedbackv5_discuss_type' );

			if ( $feedbackId && $pageId && $discussType ) {
				$feedback = ArticleFeedbackv5Model::get( $feedbackId, $pageId );

				if ( $feedback ) {
					$feedback->aft_discuss = $discussType;

					/*
					 * Before saving, the AFT data will be validated. If the discuss type
					 * is invalid, an exception will be thrown and the data will not be saved.
					 */
					try {
						$feedback->update();
					} catch ( Exception $e ) {
						/*
						 * It's great that tainted AFT data will not be inserted, but let's
						 * not stop the article edit when some AFT data is wrong.
						 */
					};
				}
			}

			// track successful edit
//			self::trackEvent( 'edit_success', $article->getTitle(), $revision->getID() );
		} else {
			// track unsuccessful edit
//			self::trackEvent( 'edit_norevision', $article->getTitle(), 0 );
		}

		return true;
	}

	/**
	 * Tracks edit attempts
	 *
	 * @see http://www.mediawiki.org/wiki/Manual:Hooks/EditPage::attemptSave
	 * @param $editpage EditPage
	 * @return bool
	 */
	public static function editAttempt( $editpage ) {
//		self::trackEvent( 'edit_attempt', $editpage->getArticle()->getTitle(), $editpage->getArticle()->getRevIdFetched()); // EditPage::getTitle() doesn't exist in 1.18wmf1
		return true;
	}

	/**
	 * Internal use: Tracks an event
	 *
	 * @param $event string the event name
	 * @param $context IContextSource
	 * @return
	 */
	private static function trackEvent( $event, $title, $rev_id ) {
		$request = RequestContext::getMain()->getRequest();

		$tracking = $request->getVal( 'articleFeedbackv5_click_tracking' );
		if ( !$tracking ) {
			return;
		}

		$ctToken    = $request->getVal( 'articleFeedbackv5_ct_cttoken' );
		$userToken  = $request->getVal( 'articleFeedbackv5_ct_usertoken' );
		$ctEvent    = $request->getVal( 'articleFeedbackv5_ct_event' );

		/*
		 * if visitor has old - cached - JS, not all required values will be there
		 * and we should ignore this hit (it won't have the relevant JS-calls either)
		 */
		if ( !$ctToken ) {
			return;
		}

		// @todo: implement EventLogging if/once requested
		// make sure sure to uncomment commented calls to self::trackEvent at that time
	}

	/**
	 * Intercept contribution entries and format those belonging to AFT
	 *
	 * @param $page SpecialPage object for contributions
	 * @param $ret string the HTML line
	 * @param $row Row the DB row for this line
	 * @param $classes the classes to add to the surrounding <li>
	 * @return bool
	 */
	public static function contributionsLineEnding( $page, &$ret, $row, &$classes ) {
		if ( !isset( $row->aft_contribution ) || $row->aft_contribution !== 'AFT' ) {
			return true;
		}

		$pageTitle = Title::newFromId( $row->aft_page );
		if ( $pageTitle === null ) {
			return true;
		}

		$record = ArticleFeedbackv5Model::get( $row->aft_id, $row->aft_page );
		if ( !$record ) {
			return true;
		}

		$lang = $page->getLanguage();
		$user = $page->getUser();
		$feedbackTitle = SpecialPage::getTitleFor( 'ArticleFeedbackv5', $pageTitle->getPrefixedDBkey() . "/$record->aft_id" );
		$centralPageName = SpecialPageFactory::getLocalNameFor( 'ArticleFeedbackv5', $pageTitle->getPrefixedDBkey() );
		$feedbackCentralPageTitle = Title::makeTitle( NS_SPECIAL, $centralPageName, "$record->aft_id" );

		// date
		$date = $lang->userTimeAndDate( $record->aft_timestamp, $user );
		$date = Linker::link(
			$feedbackTitle,
			htmlspecialchars( $date )
		);
		if ( $record->isHidden() || $record->isRequested() || $record->isOversighted() ) {
			$date = '<span class="history-deleted">' . $date . '</span>';
		}

		// show user names for /newbies as there may be different users.
		$userlink = '';
		if ( $page->contribs == 'newbie' ) {
			$username = User::whoIs( $record->aft_user );
			if ( $username !== false ) {
				$userlink = ' . . ' . Linker::userLink( $record->aft_user, $username );
				$userlink .= ' ' . wfMessage( 'parentheses' )->rawParams(
					Linker::userTalkLink( $record->aft_user, $username ) )->escaped() . ' ';
			}
		}

		// feedback (truncated)
		$feedback = '';
		if ( $record->aft_comment != '' ) {
			if ( $record->isHidden() || $record->isRequested() || $record->isOversighted() ) {
				// (probably) abusive comment that has been hidden/oversight-requested/oversighted
				$feedback = Linker::commentBlock( wfMessage( 'articlefeedbackv5-contribs-hidden-feedback' )->escaped() );
			} else {
				$feedback = Linker::commentBlock( $lang->truncate( $record->aft_comment, 75 ) );
			}
		}

		// status (actions taken)
		$actions = array();
		if ( $record->aft_helpful > $record->aft_unhelpful ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-helpful' )->escaped();
		}
		if ( $record->isFlagged() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-flag' )->escaped();
		}
		if ( $record->isFeatured() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-feature' )->escaped();
		}
		if ( $record->isResolved() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-resolve' )->escaped();
		}
		if ( $record->isNonActionable() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-noaction' )->escaped();
		}
		if ( $record->isInappropriate() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-inappropriate' )->escaped();
		}
		if ( $record->isHidden() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-hide' )->escaped();
		}
		if ( $record->isRequested() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-request' )->escaped();
		}
		if ( $record->isOversighted() ) {
			$actions[] = wfMessage( 'articlefeedbackv5-contribs-status-action-oversight' )->escaped();
		}

		$status = '';
		if ( $actions ) {
			$status = wfMessage( 'articlefeedbackv5-contribs-entry-status' )
				->params( $lang->listToText( $actions ) )
				->plain();
		}

		$ret = wfMessage( 'articlefeedbackv5-contribs-entry' )
			->rawParams( $date ) // date + time
			->params(
				ChangesList::showCharacterDifference( 0, strlen( $record->aft_comment ) ), // chardiff
				$feedbackCentralPageTitle->getFullText(), // feedback link
				$pageTitle->getPrefixedText() // article title
			)
			->rawParams( $userlink ) // userlink (for newbies)
			->params(
				$feedback, // comment
				$status // status
			)
			->parse();

		$classes[] = 'mw-aft-contribution';

		return true;
	}

	/**
	 * Adds a user's AFT-contributions to the My Contributions special page
	 *
	 * @param $data array an array of results of all contribs queries, to be merged to form all contributions data
	 * @param $pager ContribsPager object hooked into
	 * @param $offset String: index offset, inclusive
	 * @param $limit Integer: exact query limit
	 * @param $descending Boolean: query direction, false for ascending, true for descending
	 * @return bool
	 */
	public static function contributionsData( &$data, $pager, $offset, $limit, $descending ) {
		if ( $pager->namespace !== '' ) {
			return true;
		}

		$userIds = array();
		if ( $pager->contribs == 'newbie' ) {
			// fetch max user id from cache (if present)
			global $wgMemc;
			$key = wfMemcKey( 'articlefeedbackv5', 'maxUserId' );
			$max = $wgMemc->get( $key );
			if ( $max === false ) {
				// max user id not present in cache; fetch from db & save to cache for 1h
				$max = (int) $pager->getDatabase()->selectField( 'user', 'MAX(user_id)', '', __METHOD__ );
				$wgMemc->set( $key, $max, 60 * 60 );
			}

			// newbie = last 1% of users, without usergroup
			$rows = $pager->getDatabase()->select(
				array( 'user', 'user_groups' ),
				'user_id',
				array(
					'user_id > ' . (int) ( $max - $max / 100 ),
					'ug_group' => null
				),
				__METHOD__,
				array(),
				array(
					'user_groups' => array(
						'LEFT JOIN',
						array(
							'ug_user = user_id'
						)
					)
				)
			);

			$userIds = array();
			foreach ( $rows as $row ) {
				$userIds[] = $row->user_id;
			}

			if ( empty( $userIds ) ) {
				return true;
			}
		}

		$data[] = ArticleFeedbackv5Model::getContributionsData( $pager, $offset, $limit, $descending, $userIds );

		return true;
	}

	/**
	 * Add an AFT entry to article's protection levels
	 *
	 * Basically, all this code will do the same as adding a value to $wgRestrictionTypes
	 * However, that would use the same permission types as the other entries, whereas the
	 * AFT permission levels should be different.
	 *
	 * Parts of code are heavily "inspired" by ProtectionForm.
	 *
	 * @param Page $article
	 * @param $output
	 * @return bool
	 */
	public static function onProtectionForm( Page $article, &$output ) {
		global $wgLang, $wgUser, $wgArticleFeedbackv5Namespaces;

		// only on pages in namespaces where it is enabled
		if ( !$article->getTitle()->inNamespaces( $wgArticleFeedbackv5Namespaces ) ) {
			return true;
		}

		$permErrors = $article->getTitle()->getUserPermissionsErrors( 'protect', $wgUser );
		if ( wfReadOnly() ) {
			$permErrors[] = array( 'readonlytext', wfReadOnlyReason() );
		}
		$disabled = $permErrors != array();
		$disabledAttrib = $disabled ? array( 'disabled' => 'disabled' ) : array();

		$articleId = $article->getId();

		// on a per-page basis, AFT can only be restricted from these levels
		$levels = array(
			'aft-reader' => 'articlefeedbackv5-protection-permission-reader',
			'aft-member' => 'articlefeedbackv5-protection-permission-member',
			'aft-editor' => 'articlefeedbackv5-protection-permission-editor',
			'aft-administrator' => 'articlefeedbackv5-protection-permission-administrator'
		);

		// build permissions dropdown
		$existingPermissions = ArticleFeedbackv5Permissions::getRestriction( $articleId )->pr_level;
		$id = 'articlefeedbackv5-protection-level';
		$attribs = array(
			'id' => $id,
			'name' => $id,
			'size' => count( $levels )
		) + $disabledAttrib;
		$permissionsDropdown = Xml::openElement( 'select', $attribs );
		foreach( $levels as $key => $label ) {
			// possible labels: articlefeedbackv5-protection-permission-(all|reader|editor)
			$permissionsDropdown .= Xml::option( wfMessage( $label )->escaped(), $key, $key == $existingPermissions );
		}
		$permissionsDropdown .= Xml::closeElement( 'select' );

		$scExpiryOptions = wfMessage( 'protect-expiry-options' )->inContentLanguage()->text();
		$showProtectOptions = ( $scExpiryOptions !== '-' );

		list(
			$mExistingExpiry,
			$mExpiry,
			$mExpirySelection
		) = ArticleFeedbackv5Permissions::getExpiry( $articleId );

		if( $showProtectOptions ) {
			$expiryFormOptions = '';

			// add option to re-use existing expiry
			if ( $mExistingExpiry && $mExistingExpiry != 'infinity' ) {
				$timestamp = $wgLang->timeanddate( $mExistingExpiry, true );
				$d = $wgLang->date( $mExistingExpiry, true );
				$t = $wgLang->time( $mExistingExpiry, true );
				$expiryFormOptions .=
					Xml::option(
						wfMessage( 'protect-existing-expiry', $timestamp, $d, $t )->escaped(),
						'existing',
						$mExpirySelection == 'existing'
					);
			}

			// add regular expiry options
			$expiryFormOptions .= Xml::option( wfMessage( 'protect-othertime-op' )->escaped(), 'othertime' );
			foreach( explode( ',', $scExpiryOptions ) as $option ) {
				if ( strpos( $option, ':' ) === false ) {
					$show = $value = $option;
				} else {
					list( $show, $value ) = explode( ':', $option );
				}

				$expiryFormOptions .= Xml::option(
					htmlspecialchars( $show ),
					htmlspecialchars( $value ),
					$mExpirySelection == $value
				);
			}

			// build expiry dropdown
			$protectExpiry = Xml::tags( 'select',
				array(
					'id' => 'articlefeedbackv5-protection-expiration-selection',
					'name' => 'articlefeedbackv5-protection-expiration-selection',
					// when selecting anything other than "othertime", clear the input field for other time
					'onchange' => 'javascript:if ( $( this ).val() != "othertime" ) $( "#articlefeedbackv5-protection-expiration" ).val( "" );',
				),
				$expiryFormOptions );
			$mProtectExpiry = Xml::label( wfMessage( 'protectexpiry' )->escaped(), 'mwProtectExpirySelection-aft' );
		}

		// build custom expiry field
		$attribs = array(
			'id' => 'articlefeedbackv5-protection-expiration',
			// when entering an other time, make sure "othertime" is selected in the dropdown
			'onkeyup' => 'javascript:if ( $( this ).val() ) $( "#articlefeedbackv5-protection-expiration-selection" ).val( "othertime" );',
			'onchange' => 'javascript:if ( $( this ).val() ) $( "#articlefeedbackv5-protection-expiration-selection" ).val( "othertime" );'
		) + $disabledAttrib;

		$protectOther = Xml::input( 'articlefeedbackv5-protection-expiration', 50, $mExpiry, $attribs );
		$mProtectOther = Xml::label( wfMessage( 'protect-othertime' )->text(), "mwProtect-aft-expires" );

		// build output
		$output .= "
				<tr>
					<td>".
			Xml::openElement( 'fieldset' ) .
			Xml::element( 'legend', null, wfMessage( 'articlefeedbackv5-protection-level' )->text() ) .
			Xml::openElement( 'table', array( 'id' => 'mw-protect-table-aft' ) ) . "
								<tr>
									<td>$permissionsDropdown</td>
								</tr>
								<tr>
									<td>";

		if ( $showProtectOptions && !$disabled ) {
			$output .= "				<table>
											<tr>
												<td class='mw-label'>$mProtectExpiry</td>
												<td class='mw-input'>$protectExpiry</td>
											</tr>
										</table>";
		}

		$output .= "					<table>
											<tr>
												<td class='mw-label'>$mProtectOther</td>
												<td class='mw-input'>$protectOther</td>
											</tr>
										</table>
									</td>
								</tr>" .
			Xml::closeElement( 'table' ) .
			Xml::closeElement( 'fieldset' ) . "
					</td>
				</tr>";

		return true;
	}

	/**
	 * Write AFT's article's protection levels to DB
	 *
	 * Parts of code are heavily "inspired" by ProtectionForm.
	 *
	 * @param Page $article
	 * @param string $errorMsg
	 * @return bool
	 */
	public static function onProtectionSave( Page $article, &$errorMsg ) {
		global $wgRequest, $wgArticleFeedbackv5Namespaces;

		// only on pages in namespaces where it is enabled
		if ( !$article->getTitle()->inNamespaces( $wgArticleFeedbackv5Namespaces ) ) {
			return true;
		}

		$requestPermission = $wgRequest->getVal( 'articlefeedbackv5-protection-level' );
		$requestExpiry = $wgRequest->getText( 'articlefeedbackv5-protection-expiration' );
		$requestExpirySelection = $wgRequest->getVal( 'articlefeedbackv5-protection-expiration-selection' );

		// fetch permissions set to edit page ans make sure that AFT permissions are no tighter than these
		$editPermission = $article->getTitle()->getRestrictions( 'edit' );
		if ( !$editPermission ) {
			$editPermission[] = '*';
		}
		$availablePermissions = User::getGroupPermissions( $editPermission );
		if ( !in_array( $requestPermission, $availablePermissions ) ) {
			$errorMsg .= wfMessage( 'articlefeedbackv5-protection-level-error' )->escaped();
			return false;
		}

		if ( $requestExpirySelection == 'existing' ) {
			$expirationTime = ArticleFeedbackv5Permissions::getRestriction( $article->getId() )->pr_expiry;
		} else {
			if ( $requestExpirySelection == 'othertime' ) {
				$value = $requestExpiry;
			} else {
				$value = $requestExpirySelection;
			}

			if ( $value == 'infinite' || $value == 'indefinite' || $value == 'infinity' ) {
				$expirationTime = wfGetDB( DB_SLAVE )->getInfinity();
			} else {
				$unix = strtotime( $value );

				if ( !$unix || $unix === -1 ) {
					$errorMsg .= wfMessage( 'protect_expiry_invalid' )->escaped();
					return false;
				} else {
					// @todo FIXME: Non-qualified absolute times are not in users specified timezone
					// and there isn't notice about it in the ui
					$expirationTime = wfTimestamp( TS_MW, $unix );
				}
			}
		}

		$success = ArticleFeedbackv5Permissions::setRestriction(
			$article->getId(),
			$requestPermission,
			$expirationTime
		);

		return $success;
	}

	/**
	 * Post-login update new user's last feedback with his new id
	 *
	 * @param User $currentUser
	 * @param string $injected_html
	 * @return bool
	 */
	public static function userLoginComplete( $currentUser, $injected_html ) {
		global $wgRequest;

		$id = 0;

		// feedback id is c-parameter in the referrer, extract it
		$referrer = ( $wgRequest->getVal( 'referrer' ) ) ? $wgRequest->getVal( 'referrer' ) : $wgRequest->getHeader( 'referer' );
		$url = parse_url( $referrer );
		$values = array();
		if ( isset( $url['query'] ) ) {
			parse_str( $url['query'], $values );
		}
		if ( isset( $values['c'] ) ) {
			$id = $values['c'];

		// if c-parameter is no longer in url (e.g. account creation didn't work at first attempts), try cookie data
		} else {
			$cookie = json_decode( $wgRequest->getCookie( ArticleFeedbackv5Utils::getCookieName( 'feedback-ids' ) ), true );
			if ( is_array( $cookie ) ) {
				$id = array_shift( $cookie );
			}
		}

		// the page that feedback was added to is the one we'll be returned to
		$title = Title::newFromDBkey( $wgRequest->getVal( 'returnto' ) );
		if ( $title !== null && $id ) {
			$pageId = $title->getArticleID();

			/*
			 * If we find this feedback and it is not yet "claimed" (and the feedback was
			 * not submitted by a registered user), "claim" it to the current user.
			 * Make sure the current request's IP actually still matches the one saved for
			 * the original submission.
			 */
			$feedback = ArticleFeedbackv5Model::get( $id, $pageId );
			if (
				$feedback &&
				!$feedback->aft_user &&
				$feedback->aft_user_text == IP::sanitizeIP( $wgRequest->getIP() ) &&
				!$feedback->aft_claimed_user
			 ) {
				$feedback->aft_claimed_user = $currentUser->getId();
				$feedback->update();
			}
		}

		return true;
	}
}
