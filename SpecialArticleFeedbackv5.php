<?php
/**
 * SpecialArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Special
 * @author     Greg Chiasson <gchiasson@omniti.com>
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

/**
 * This is the Special page the shows the feedback dashboard
 *
 * @package    ArticleFeedback
 * @subpackage Special
 */
class SpecialArticleFeedbackv5 extends SpecialPage {

	/**
	 * The filters available
	 *
	 * Will be create on construction based on user permissions
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * The sorts available
	 *
	 * @var array
	 */
	protected $sorts;

	/**
	 * The page ID we're operating on (null for central log)
	 *
	 * @var int
	 */
	protected $pageId = null;

	/**
	 * The title for the page we're operating on (null for central log)
	 *
	 * @var Title
	 */
	protected $title;

	/**
	 * The feedback ID we're operating on (if permalink)
	 *
	 * @var int
	 */
	protected $feedbackId;

	/**
	 * The starting filter
	 *
	 * @var string
	 */
	protected $startingFilter;

	/**
	 * The starting sort
	 *
	 * @var string
	 */
	protected $startingSort;

	/**
	 * The starting offset
	 *
	 * @var string
	 */
	protected $startingOffset = '';

	/**
	 * The starting sort direction
	 *
	 * @var string
	 */
	protected $startingSortDirection;

	/**
	 * Constructor
	 */
	public function __construct(
		$name = 'ArticleFeedbackv5', $restriction = '', $listed = true,
		$function = false, $file = 'default', $includable = false
	) {
		parent::__construct( $name, $restriction, $listed, $function, $file, $includable );
	}

	/**
	 * Executes the special page
	 *
	 * @param $param string the parameter passed in the url
	 */
	public function execute( $param ) {
		$request = $this->getRequest();

		$out = $this->getOutput();
		$out->addModuleStyles( 'ext.articleFeedbackv5.dashboard' );
		$out->addModuleStyles( 'jquery.articleFeedbackv5.special' );

		// set robot policy
		$out->setIndexPolicy( 'noindex' );

		// these are messages that require some parsing that the current JS mw.msg does not yet support
		// articlefeedbackv5-noteflyover-helpful-description, articlefeedbackv5-noteflyover-undo-helpful-description,
		// articlefeedbackv5-noteflyover-unhelpful-description, articlefeedbackv5-noteflyover-undo-unhelpful-description,
		// articlefeedbackv5-noteflyover-flag-description, articlefeedbackv5-noteflyover-unflag-description,
		// articlefeedbackv5-noteflyover-autoflag-description, articlefeedbackv5-noteflyover-clear-flags-description,
		// articlefeedbackv5-noteflyover-feature-description, articlefeedbackv5-noteflyover-unfeature-description,
		// articlefeedbackv5-noteflyover-resolve-description, articlefeedbackv5-noteflyover-unresolve-description,
		// articlefeedbackv5-noteflyover-noaction-description, articlefeedbackv5-noteflyover-unnoaction-description,
		// articlefeedbackv5-noteflyover-inappropriate-description, articlefeedbackv5-noteflyover-uninappropriate-description,
		// articlefeedbackv5-noteflyover-archive-description, articlefeedbackv5-noteflyover-unarchive-description,
		// articlefeedbackv5-noteflyover-hide-description, articlefeedbackv5-noteflyover-unhide-description,
		// articlefeedbackv5-noteflyover-autohide-description, articlefeedbackv5-noteflyover-request-description,
		// articlefeedbackv5-noteflyover-unrequest-description, articlefeedbackv5-noteflyover-decline-description,
		// articlefeedbackv5-noteflyover-oversight-description, articlefeedbackv5-noteflyover-unoversight-description
		$vars = array();
		foreach ( ArticleFeedbackv5Activity::$actions as $action => $options ) {
			$message = wfMessage( "articlefeedbackv5-noteflyover-$action-description" )->parse();
			$vars["mw.msg.articlefeedbackv5-noteflyover-$action-description"] = $message;
		}
		$out->addJsConfigVars( $vars );
		$out->setArticleRelated( false );

		// build list of available filters & sorts
		$this->filters = array();
		foreach ( ArticleFeedbackv5Model::$lists as $filter => $data ) {
			if ( $this->isAllowed( $data['permissions'] ) ) {
				$this->filters[] = $filter;
			}
		}
		$this->sorts = array( 'relevance-DESC', 'relevance-ASC', 'age-DESC', 'age-ASC', 'helpful-DESC', 'helpful-ASC' );

		// don't display archived list unless specifically "enabled" (if cronjob
		// is not running, it would simply not work)
		global $wgArticleFeedbackv5AutoArchiveEnabled;
		if ( !$wgArticleFeedbackv5AutoArchiveEnabled ) {
			$this->filters = array_diff( $this->filters, array( 'archived' ) );
		}

		if ( $param ) {
			/*
			 * Check if title exists. We don't do the regex to separate the
			 * permalink part just yet because a slash in the param could
			 * also just mean we're looking at feedback from Some/Subpage.
			 */
			$title = Title::newFromText( $param );

			// if title does not exist, we may be looking at a permalink page
			if ( !$title || !$title->exists() ) {
				if ( preg_match( '/^(.+)\/(\w+)$/', $param, $match ) ) {
					$title = Title::newFromText( $match[1] );
					$this->feedbackId = $match[2];
				}

				if ( !$title || !$title->exists() ) {
					$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
					return;
				}
			}

			$this->pageId = $title->getArticleID();
			$this->title = $title;
		}

		// select filter, sort, and sort direction
		$this->setFilterSortDirection(
			$request->getText( 'filter' ),
			$request->getText( 'sort' )
		);

		$records = $this->fetchData();

		// build renderer
		$permalink = (bool) $this->feedbackId;
		$central = !(bool) $this->pageId;
		$renderer = new ArticleFeedbackv5Render( $permalink, $central );

		// build title
		if ( $permalink ) {
			$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-permalink-pagetitle', $this->title )->escaped() );
		} elseif ( $this->pageId ) {
			$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-pagetitle', $this->title )->escaped() );
		} else {
			$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-central-pagetitle' )->escaped() );
		}

		// output content
		$out->addHTML(
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-special-wrap' ),
				$this->buildHeaderLinks() . $this->buildContent( $renderer, $records )
			)
		);

		$filterCount = ArticleFeedbackv5Model::getCount( 'featured', $this->pageId );
		$totalCount = ArticleFeedbackv5Model::getCount( '*', $this->pageId );

		// JS variables
		$out->addJsConfigVars( 'afPageId', $this->pageId );
		$out->addJsConfigVars( 'afReferral', $request->getText( 'ref', 'url' ) );
		$out->addJsConfigVars( 'afStartingFilter', $this->startingFilter );
		$out->addJsConfigVars( 'afStartingFeedbackId', $permalink ? $this->feedbackId : null );
		$out->addJsConfigVars( 'afStartingSort', $this->startingSort );
		$out->addJsConfigVars( 'afStartingSortDirection', $this->startingSortDirection );
		$out->addJsConfigVars( 'afCount', $totalCount );
		$out->addJsConfigVars( 'afFilterCount', $filterCount );
		$out->addJsConfigVars( 'afOffset', $records ? $records->nextOffset() : 0 );
		$out->addJsConfigVars( 'afShowMore', $records ? $records->hasMore() : false );
	}

	/**
	 * @return DataModelList
	 */
	protected function fetchData() {
		// permalink page
		if ( $this->feedbackId ) {
			$record = ArticleFeedbackv5Model::get( $this->feedbackId, $this->pageId );
			if ( $record ) {
				return new DataModelList(
					array( array( 'id' => $record->aft_id, 'shard' => $record->aft_page ) ),
					'ArticleFeedbackv5Model'
				);
			}

		// list page
		} else {
			/*
			 * Hack: if a filter is requested but there is no feedback,
			 * and there _is_ feedback in the "unreviewed" filter, display that
			 * one instead.
			 */
			if (
				ArticleFeedbackv5Model::getCount( $this->startingFilter, $this->pageId ) == 0 &&
				ArticleFeedbackv5Model::getCount( 'unreviewed', $this->pageId ) > 0
			) {
				$this->startingFilter = 'unreviewed';
				$this->startingSort = 'relevance';
				$this->startingSortDirection = 'DESC';
			}

			return ArticleFeedbackv5Model::getList(
				$this->startingFilter,
				$this->pageId,
				$this->startingOffset,
				$this->startingSort,
				$this->startingSortDirection
			);
		}

		return false;
	}

	/**
	 * Outputs the header links in the top right corner
	 *
	 * View Article | Discussion | Help
	 * @return string
	 */
	protected function buildHeaderLinks() {
		// build link to page & talk page
		$pageLinks = '';
		if ( $this->pageId ) {
			$pageLinks =
				Linker::link(
					$this->title,
					$this->msg( 'articlefeedbackv5-go-to-article' )->escaped()
				) .
					' | ' .
					Linker::link(
						$this->title->getTalkPage(),
						$this->msg( 'articlefeedbackv5-discussion-page' )->escaped()
					) .
					' | ';
		}

		// build header for list-views
		$listHeader = '';
		if ( !$this->feedbackId ) {
			$listHeader = $this->buildListHeader();
		}

		return
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-wrap' ) ) .
				Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-links' ) ) .
					$pageLinks .
					Html::element(
						'a',
						array( 'href' => $this->getHelpLink().'#Feedback_page' ),
						$this->msg( 'articlefeedbackv5-whats-this' )->escaped()
					) .
				Html::closeElement( 'div' ) .
				$listHeader .
			Html::closeElement( 'div' );
	}

	/**
	 * Outputs additional info in header for list-views
	 *
	 * @return string
	 */
	protected function buildListHeader() {
		return
			$this->buildSummary() .
			Html::element( 'div', array( 'class' => 'float-clear' ) );
	}

	/**
	 * Display the feedback page's summary information in header
	 *
	 * @return string
	 */
	protected function buildSummary() {
		$user = $this->getUser();

		// if we have a logged in user and are currently browsing the central feedback page,
		// check if there is feedback on his/her watchlisted pages
		global $wgArticleFeedbackv5Watchlist;
		$watchlistLink = '';
		if ( $wgArticleFeedbackv5Watchlist && !$this->pageId && $user->getId() ) {
			$records = ArticleFeedbackv5Model::getWatchlistList(
				'unreviewed',
				$user
			);

			if ( count( $records ) > 0 ) {
				$watchlistLink =
					Html::rawElement(
						'span',
						array( 'id' => 'articlefeedbackv5-special-central-watchlist-link' ),
						$this->msg(
							'articlefeedbackv5-special-central-watchlist-link',
							SpecialPage::getTitleFor( 'ArticleFeedbackv5Watchlist' )->getFullText()
						)->parse()
					);
			}
		}

		// Showing {count} posts
		$totalCount = ArticleFeedbackv5Model::getCount( '*', $this->pageId );
		$totalComment = ArticleFeedbackv5Model::getCount( 'has_comment', $this->pageId );
		$totalNoComment = $totalCount - $totalComment;
		$count =
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-showing-count-wrap' ),
				$this->msg( 'articlefeedbackv5-special-count-total',
					Html::element(
						'span',
						array(
							'title' => $this->msg( 'articlefeedbackv5-special-count-total-title', $totalComment, $totalNoComment )->text()
						),
						$totalCount
					),
					$totalCount
				)->text()
			);

		// % found
		$percent = '';
		if ( $this->pageId ) {
			// calculate percentage found
			$totalInappropriate = ArticleFeedbackv5Model::getCount( 'inappropriate', $this->pageId );
			$totalHidden = ArticleFeedbackv5Model::getCount( 'hidden', $this->pageId );
			$totalOversighted = ArticleFeedbackv5Model::getCount( 'oversighted', $this->pageId );
			$total = ArticleFeedbackv5Model::getCount( '*', $this->pageId ) - $totalInappropriate - $totalHidden - $totalOversighted;
			$found = ArticleFeedbackv5Model::getCountFound( $this->pageId ) / ( $total ?: 1 ) * 100;

			if ( $found ) {
				if ( $found > 50 ) {
					$class = 'positive';
				} elseif ( $found < 50 ) {
					$class = 'negative';
				} else {
					$class = 'neutral';
				}
				$span = Html::rawElement(
					'span',
					array(
						'class' => "stat-marker $class",
						'title' => $this->msg( 'articlefeedbackv5-found-percent-title', $total )->text()
					),
					$this->msg( 'percent', round( $found ) )->escaped()
				);

				$percent =
					Html::rawElement(
						'div',
						array( 'id' => 'articleFeedbackv5-percent-found-wrap' ),
						$this->msg( 'articlefeedbackv5-found-percent' )
							->rawParams( $span )
							->numParams( round( $found ) )
							->escaped()
					);
			}
		}

		return $count . $watchlistLink . $percent;
	}

	/**
	 * Get link to help-page, based on user's permission level
	 *
	 * @return string
	 */
	protected function getHelpLink() {
		$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl' )->text();
		if ( $this->isAllowed( 'aft-oversighter' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-oversighters' )->text();
		} elseif ( $this->isAllowed( 'aft-monitor' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-monitors' )->text();
		} elseif ( $this->isAllowed( 'aft-editor' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-editors' )->text();
		}

		return $helpLink;
	}

	/**
	 * @param ArticleFeedbackv5Render $renderer the renderer
	 * @param DataModelList $record the fetched records
	 * @return string
	 */
	protected function buildContent( $renderer, $records ) {
		if ( !$records ) {
			return $this->msg( 'articlefeedbackv5-invalid-feedback' )->escaped();
		}

		if ( $this->feedbackId ) {
			$record = $records->fetchObject();
			return $this->buildPermalink( $renderer, $record );
		} else {
			return $this->buildListing( $renderer, $records );
		}
	}

	/**
	 * Outputs a permalink
	 *
	 * @param $renderer ArticleFeedbackv5Render the renderer
	 * @param $record ArticleFeedbackv5Model the fetched record
	 * @return string
	 */
	protected function buildPermalink( $renderer, $record ) {
		return
			Html::rawElement(
				'div',
				array( 'class' => 'articleFeedbackv5-feedback-permalink-goback' ),
				Linker::link(
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $this->title->getPrefixedDBkey() ),
					'&lsaquo; ' . $this->msg( 'articlefeedbackv5-special-goback' )->escaped()
				)
			) .
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-show-feedback' ),
				$renderer->run( $record )
			) .
			Html::rawElement(
				'div',
				array( 'class' => 'articleFeedbackv5-feedback-permalink-goback' ),
				Linker::link(
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $this->title->getPrefixedDBkey() ),
					'&lsaquo; ' . $this->msg( 'articlefeedbackv5-special-goback' )->escaped()
				)
			);
	}

	/**
	 * Outputs a listing
	 *
	 * @param ArticleFeedbackv5Render $renderer the renderer
	 * @param DataModelList $records the fetched records &etc.
	 * @return string
	 */
	protected function buildListing( $renderer, $records ) {
		// build rows output
		$rows = '';
		foreach ( $records as $record ) {
			$rows .= $renderer->run( $record );
		}

		// link back to the central page (only for editors)
		$centralPageLink = '';
		if ( $this->pageId && $this->isAllowed( 'aft-editor' ) ) {
			$centralPageLink =
				Html::rawElement(
					'div',
					array( 'class' => 'articleFeedbackv5-feedback-central-goback' ),
					Linker::link(
						SpecialPage::getTitleFor( 'ArticleFeedbackv5' ),
						$this->msg( 'articlefeedbackv5-special-central-goback' )->escaped()
					)
				);
		}

		return
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-sort-filter-controls' ),
				$this->buildFilters() .
				$this->buildSort()
			) .
			Html::rawElement(
				'div',
				array(
					'id'    => 'articleFeedbackv5-show-feedback',
					'class' => $this->pageId ? '' : 'articleFeedbackv5-central-feedback-log'
				),
				$rows
			) .
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-footer' ),
				Html::element(
					'a',
					array(
						'href' => '#more-feedback',
						'id'   => 'articleFeedbackv5-show-more'
					),
					$this->msg( 'articlefeedbackv5-special-more' )->text()
				) .
				Html::element(
					'a',
					array(
						'href' => '#refresh-feedback',
						'id'   => 'articleFeedbackv5-refresh-list'
					),
					$this->msg( 'articlefeedbackv5-special-refresh' )->text()
				) .
				Html::element( 'div', array( 'class' => 'clear' ) )
			) .
			$centralPageLink;
	}

	/**
	 * Outputs the page filter controls
	 *
	 * Showing: [filters...]
	 * @return string
	 */
	protected function buildFilters() {
		// filter to be displayed as link
		$filterLabels = array();
		foreach ( array( 'featured', 'unreviewed' ) as $filter ) {
			$count = ArticleFeedbackv5Model::getCount( $filter, $this->pageId );

			// Give grep a chance to find the usages:
			// articlefeedbackv5-special-filter-featured, articlefeedbackv5-special-filter-unreviewed
			$filterLabels[$filter] =
				Html::rawElement(
					'a',
					array(
						'href' => '#',
						'id' => "articleFeedbackv5-special-filter-$filter",
						'class' => 'articleFeedbackv5-filter-link' . ( $this->startingFilter == $filter ? ' filter-active' : '' )
					),
					$this->msg( "articlefeedbackv5-special-filter-$filter", $count )->escaped()
				);
		}

		// filters to be displayed in dropdown (only for editors)
		$filterSelectHtml = '';
		if ( $this->isAllowed( 'aft-editor' ) ) {
			$opts = array();

			// Give grep a chance to find the usages:
			// articlefeedbackv5-special-filter-featured, rticlefeedbackv5-special-filter-unreviewed,
			// articlefeedbackv5-special-filter-helpful, articlefeedbackv5-special-filter-unhelpful,
			// articlefeedbackv5-special-filter-flagged, articlefeedbackv5-special-filter-useful,
			// articlefeedbackv5-special-filter-resolved, articlefeedbackv5-special-filter-noaction,
			// articlefeedbackv5-special-filter-inappropriate, articlefeedbackv5-special-filter-archived,
			// articlefeedbackv5-special-filter-allcomment, articlefeedbackv5-special-filter-hidden,
			// articlefeedbackv5-special-filter-requested, articlefeedbackv5-special-filter-declined,
			// articlefeedbackv5-special-filter-oversighted,
			// articlefeedbackv5-special-filter-all
			foreach ( $this->filters as $filter ) {
				if ( in_array( $filter, array_keys( $filterLabels ) ) ) {
					continue;
				}

				$count = ArticleFeedbackv5Model::getCount( $filter, $this->pageId );

				$key = $this->msg( "articlefeedbackv5-special-filter-$filter", $count )->escaped();
				$opts[(string) $key] = $filter;
			}

			if ( count( $opts ) > 0 ) {
				// Put the "more filters" option at the beginning of the opts array
				$opts = array( $this->msg( 'articlefeedbackv5-special-filter-select-more' )->text() => '' ) + $opts;

				$filterSelect = new XmlSelect( false, 'articleFeedbackv5-filter-select' );
				$filterSelect->setDefault( $this->startingFilter );
				$filterSelect->addOptions( $opts );
				$filterSelectHtml = $filterSelect->getHTML();
			}
		}

		return
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-filter' ),
				implode( ' ', $filterLabels ) .
				Html::rawElement(
					'div',
					array( 'id' => 'articleFeedbackv5-select-wrapper' ),
					$filterSelectHtml
				)
			);
	}

	/**
	 * Outputs the page sort controls
	 *
	 * Showing: Sort by: Relevance | Helpful | Rating | Date
	 * @return string
	 */
	protected function buildSort() {
		// Sorting
		// Give grep a chance to find the usages:
		// articlefeedbackv5-special-sort-relevance-desc, articlefeedbackv5-special-sort-relevance-asc,
		// articlefeedbackv5-special-sort-helpful-desc, articlefeedbackv5-special-sort-helpful-asc,
		// articlefeedbackv5-special-sort-age-desc, articlefeedbackv5-special-sort-age-asc,
		// articlefeedbackv5-special-sort-label
		$opts = array();
		foreach ( $this->sorts as $i => $sort ) {
			if ( $i % 2 == 0 && $i > 0 ) {
				// Add dividers between each pair (append trailing spaces so
				// that they all get added)
				$opts[ '---------' . str_repeat( ' ', $i ) ] = '';
			}
			$key = $this->msg( strtolower( "articlefeedbackv5-special-sort-$sort" ) )->escaped();
			$opts[(string) $key] = $sort;
		}
		$sortSelect = new XmlSelect( false, 'articleFeedbackv5-sort-select' );
		$sortSelect->setDefault( $this->startingSort . '-' . $this->startingSortDirection );
		$sortSelect->addOptions( $opts );

		return
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-sort' ),
				Html::rawElement(
					'span',
					array( 'class' => 'articleFeedbackv5-sort-label' ),
					$this->msg( 'articlefeedbackv5-special-sort-label' )->escaped()
				) .
				Html::rawElement(
					'div',
					array( 'id' => 'articleFeedbackv5-sort-wrapper' ),
					$sortSelect->getHTML()
				)
			);
	}

	/**
	 * Sets the filter, sort, and sort direction based on what was passed in
	 *
	 * @param $filter string the requested filter
	 * @param $sort   string the requested sort
	 */
	protected function setFilterSortDirection( $filter, $sort ) {
		global $wgArticleFeedbackv5DefaultFilters,
			$wgArticleFeedbackv5DefaultSorts;

		// Was a filter requested via (hidden) user preference?
		if ( !$filter || !in_array( $filter, $this->filters ) ) {
			$filter = $this->getUser()->getOption( 'aftv5-last-filter' );
		}

		// Was a filter requested via cookie?
		if ( !$filter || !in_array( $filter, $this->filters ) ) {
			$request = $this->getRequest();
			$cookie = json_decode( $request->getCookie( ArticleFeedbackv5Utils::getCookieName( 'last-filter' ) ) );
			if ( $cookie !== null && is_object( $cookie )
				&& isset( $cookie->listControls ) && is_object( $cookie->listControls ) ) {
				$cookie_filter = $cookie->listControls->filter;
				$cookie_sort   = $cookie->listControls->sort;
				$cookie_dir    = $cookie->listControls->sortDirection;
			}
			if ( isset( $cookie_filter ) && in_array( $cookie_filter, $this->filters ) ) {
				$filter = $cookie_filter;
			}
		}

		// Find the default filter
		if ( !$filter || !in_array( $filter, $this->filters ) ) {
			if ( $this->isAllowed( 'aft-oversighter' ) ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['aft-oversighter'];
			} elseif ( $this->isAllowed( 'aft-monitor' ) ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['aft-monitor'];
			} elseif ( $this->isAllowed( 'aft-editor' ) ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['aft-editor'];
			} else {
				$filter = $wgArticleFeedbackv5DefaultFilters['aft-reader'];
			}
		}

		// Was a sort requested?
		if ( $sort ) {
			if ( in_array( $sort, $this->sorts ) ) {
				list( $sort, $dir ) = explode( '-', $sort );
			} else {
				$sort = false;
			}
		}

		// Was a sort included in the cookie?
		if ( isset( $cookie_filter ) && $cookie_filter == $filter
			&& isset( $cookie_sort ) && isset( $cookie_dir ) ) {
			if ( in_array( $cookie_sort . '-' . $cookie_dir, $this->sorts ) ) {
				$sort = $cookie_sort;
				$dir = strtoupper( $cookie_dir );
			}
		}

		// Decide on our default sort info
		if ( !$sort ) {
			list( $sort, $dir ) = $wgArticleFeedbackv5DefaultSorts[$filter];
		}

		$this->startingFilter = $filter;
		$this->startingSort = $sort;
		$this->startingSortDirection = $dir;
	}

	/**
	 * Returns whether an action is allowed
	 *
	 * @param  $action string the name of the action
	 * @return bool whether it's allowed
	 */
	public function isAllowed( $permission ) {
		$user = $this->getUser();
		return $user->isAllowed( $permission ) && !$user->isBlocked();
	}
}
