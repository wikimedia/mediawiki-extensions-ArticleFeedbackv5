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
	protected $pageId = 0;

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
	 * @var int
	 */
	protected $startingOffset = 0;

	/**
	 * The starting sort direction
	 *
	 * @var string
	 */
	protected $startingSortDirection;

	/**
	 * The filters available to users without special privileges
	 *
	 * @var bool
	 */
	protected $defaultFilters = array(
		'visible-relevant',
		'visible-featured',
		'visible-helpful',
		'visible-comment',
		'visible'
	);

	/**
	 * The filters available outside of the select
	 *
	 * @var bool
	 */
	protected $topFilters = array(
		'visible-relevant',
		'visible-comment',
	);

	/**
	 * Constructor
	 */
	public function __construct(
		$name = 'ArticleFeedbackv5', $restriction = '', $listed = true,
		$function = false, $file = 'default', $includable = false
	) {
		wfProfileIn( __METHOD__ );

		parent::__construct( $name, $restriction, $listed, $function, $file, $includable );

		$this->filters = array();
		foreach ( ArticleFeedbackv5Model::$lists as $filter => $data ) {
			if ( $this->isAllowed( $data['permissions'] ) ) {
				$this->filters[] = $filter;
			}
		}

		$this->sorts = array( 'relevance-asc', 'relevance-desc', 'age-desc', 'age-asc' );
		if ( $this->isAllowed( 'aft-editor' ) ) {
			array_push( $this->sorts, 'helpful-desc', 'helpful-asc' );
		}

		// these are messages that require some parsing that the current JS mw.msg does not yet support
		$flyovers = array(
			'hide', 'show', 'requestoversight', 'unrequestoversight',
			'oversight', 'unoversight', 'declineoversight', 'feature',
			'unfeature', 'resolve', 'unresolve'
		);
		foreach ( $flyovers as $flyover ) {
			$message = wfMessage( "articlefeedbackv5-noteflyover-$flyover-description" )->parse();
			$vars["mw.msg.articlefeedbackv5-noteflyover-$flyover-description"] = $message;
		}
		$this->getOutput()->addJsConfigVars( $vars );

		wfProfileOut( __METHOD__ );
	}

	/**
	 * Executes the special page
	 *
	 * @param $param string the parameter passed in the url
	 */
	public function execute( $param ) {
		$user = $this->getUser();
		$request = $this->getRequest();

		$out = $this->getOutput();
		$out->addModuleStyles( 'ext.articleFeedbackv5.dashboard' );
		$out->addModuleStyles( 'jquery.articleFeedbackv5.special' );

		// set robot policy
		$out->setIndexPolicy('noindex');

		if ( !$param ) {
			// No Page ID: do central log
		} else {
			// Permalink
			if ( preg_match('/^(.+)\/(\d+)$/', $param, $m ) ) {
				$param = $m[1];
				$this->feedbackId = $m[2];
			}
			// Get page
			$title = Title::newFromText( $param );
			if ( !$title->exists() ) {
				$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
				return;
			}
			$this->pageId = $title->getArticleID();
			$this->title = $title;
		}

		// select filter, sort, and sort direction
		$this->setFilterSortDirection(
			$request->getText( 'filter' ),
			$request->getText( 'sort' )
		);

/*
 * This is outdated stuff
		$fetch->setFilter( $this->startingFilter );
		$fetch->setFeedbackId( $this->feedbackId );
		$fetch->setPageId( $this->pageId );
		$fetch->setSort( $this->startingSort );
		$fetch->setSortOrder( $this->startingSortDirection );
		$fetch->setLimit( $this->startingLimit );
		$fetched = $fetch->run();
*/

		// permalink page
		if ( $this->feedbackId ) {
			$record = ArticleFeedbackv5Model::get( $this->feedbackId, $this->pageId );
			$records = array( $record->id => $record );

		// list page
		} else {
			$records = ArticleFeedbackv5Model::getList(
				$this->startingFilter,
				$this->pageId,
				$this->startingOffset,
				$this->startingSortDirection
			);
		}

		// build renderer
		$permalink = (bool) $this->feedbackId;
		$central = (bool) $this->pageId;
		$renderer = new ArticleFeedbackv5Render( $user, $permalink, $central );

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

		$filterCount = ArticleFeedbackv5Model::getCount( $this->startingFilter, $this->pageId );
		$totalCount = ArticleFeedbackv5Model::getCount( 'all', null ); // @todo: this filter does not yet exist

		// JS variables
		$out->addJsConfigVars( 'afPageId', $this->pageId );
		$out->addJsConfigVars( 'afReferral', $request->getText( 'ref', 'url' ) );
		$out->addJsConfigVars( 'afStartingFilter', $this->startingFilter );
		$out->addJsConfigVars( 'afStartingFeedbackId', $this->startingFilter == 'id' ? $this->feedbackId : null );
		$out->addJsConfigVars( 'afStartingSort', $this->startingSort );
		$out->addJsConfigVars( 'afStartingSortDirection', $this->startingSortDirection );
		$out->addJsConfigVars( 'afCount', $totalCount );
		$out->addJsConfigVars( 'afOffset', $this->startingOffset + ArticleFeedbackv5Model::LIST_LIMIT );
		$out->addJsConfigVars( 'afShowMore', $filterCount > $this->startingOffset + ArticleFeedbackv5Model::LIST_LIMIT );
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
			Html::rawElement(
				'p',
				array( 'id' => 'articlefeedbackv5-header-message' ),
				$this->msg( 'articlefeedbackv5-header-message' )->rawParams(
					Html::rawElement(
						'a',
						array( 'href' => $this->getHelpLink().'#Feedback_page' ),
						$this->msg( 'articlefeedbackv5-header-message-link-text' )->escaped() . ' &raquo;'
					)
				)->text()
			) .
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
		$watchlistLink = '';
		if ( !$this->pageId && $user->getId() ) {

			// @todo: this code will need to change, don't yet have a clue how though ;)
			$fetch = new ArticleFeedbackv5Fetch();
			$fetch->setUserId( $user->getId() );
			$fetch->setLimit( 1 );
			$fetched = $fetch->run();

			if ( count( $fetched->records ) > 0 ) {
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
		$count =
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-showing-count-wrap' ),
				$this->msg(
					$this->pageId ? 'articlefeedbackv5-special-showing' : 'articlefeedbackv5-special-central-showing',
					Html::element(
						'span',
						array( 'id' => 'articleFeedbackv5-feedback-count-total' ),
						'0' // this figure will be filled out through JS
					)
				)->text() .
					$watchlistLink
			);

		// % found
		$percent = '';
		if ( $this->pageId ) {
			$ratings = $this->fetchOverallRating( $this->pageId );
			$found = isset( $ratings['found'] ) ? $ratings['found'] : null;
			if ( $found ) {
				$class = $found >= 50 ? 'positive' : 'negative';

				$percent =
					Html::rawElement(
						'div',
						array( 'id' => 'articleFeedbackv5-percent-found-wrap' ),
						$this->msg( 'articlefeedbackv5-percent-found' )->rawParams(
							Html::rawElement(
								'span',
								array( 'class' => "stat-marker $class" ),
								$this->msg( 'percent', $found )->escaped()
							)
						)->escaped()
					);
			}
		}

		return $count . $percent;
	}

	/**
	 * Get link to help-page, based on user's permission level
	 *
	 * @return string
	 */
	protected function getHelpLink() {
		$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl')->text();
		if( $this->isAllowed( 'aft-oversighter' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-oversighters' )->text();
		} elseif( $this->isAllowed( 'aft-monitor' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-monitors' )->text();
		} elseif( $this->isAllowed( 'aft-editor' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-editors' )->text();
		}

		return $helpLink;
	}

	/**
	 * @param $renderer ArticleFeedbackv5Render the renderer
	 * @param $record array the fetched records
	 * @return string
	 */
	protected function buildContent( $renderer, $records ) {
		if ( $this->feedbackId ) {
			$record = array_pop( $records );
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
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $this->title->getPrefixedText() ),
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
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $this->title->getPrefixedText() ),
					'&lsaquo; ' . $this->msg( 'articlefeedbackv5-special-goback' )->escaped()
				)
			);
	}

	/**
	 * Outputs a listing
	 *
	 * @param $renderer ArticleFeedbackv5Render the renderer
	 * @param $records array the fetched records &etc.
	 * @return string
	 */
	protected function buildListing( $renderer, $records ) {
		// build rows output
		$rows = '';
		foreach ( (array) $records as $record ) {
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
		$filterLabels = array();
		foreach ( $this->topFilters as $filter ) {
			if ( $this->startingFilter == $filter ) {
				$class = 'articleFeedbackv5-filter-link filter-active';
			} else {
				$class = 'articleFeedbackv5-filter-link';
			}
			$count = ArticleFeedbackv5Model::getCount( $filter, $this->pageId );
			$msg_key = str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);

			$filterLabels[] = Html::openElement( 'a',
					array(
						'href'  => '#',
						'id'    => 'articleFeedbackv5-special-filter-' . $filter,
						'class' => $class
					)
				)
				// {msg:articlefeedbackv5-special-filter-{$filter}}
				// Messages are:
				//  * articlefeedbackv5-special-filter-relevant
				//  * articlefeedbackv5-special-filter-featured
				//  * articlefeedbackv5-special-filter-helpful
				//  * articlefeedbackv5-special-filter-comment
				//  * articlefeedbackv5-special-filter-visible
				. $this->msg( "articlefeedbackv5-special-filter-$msg_key", $count )->escaped()

			. Html::closeElement( 'a' );
		}

		$filterSelectHtml = '';
		// No dropdown for readers
		if ( $this->isAllowed( 'aft-editor' ) ) {
			$opts = array();
			$foundNonDefaults = false;
			foreach ( $this->filters as $filter ) {
				$count = ArticleFeedbackv5Model::getCount( $filter, $this->pageId );
				$msg_key = str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);

				$key   = $this->msg( 'articlefeedbackv5-special-filter-' . $msg_key, $count )->escaped();
				if ( in_array( $filter, $this->topFilters ) ) {
					continue;
				}
				if ( !$foundNonDefaults && !in_array( $filter, $this->defaultFilters ) ) {
					// Add a divider between the defaults and the rest (use X,
					// so that it can be distinguished from "More filters")
					$opts[ '---------' ] = 'X';
					$foundNonDefaults = true;
				}
				$opts[ (string) $key ] = $filter;
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
				Html::rawElement(
					'span',
					array( 'class' => 'articleFeedbackv5-filter-label' ),
					$this->msg( 'articlefeedbackv5-special-filter-label-before' )->escaped()
				) .
				implode( ' ', $filterLabels ) .
				Html::rawElement(
					'div',
					array( 'id' => 'articleFeedbackv5-select-wrapper' ),
					$filterSelectHtml
				) .
				$this->msg( 'articlefeedbackv5-special-filter-label-after' )->escaped()
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
		$opts = array();
		foreach ( $this->sorts as $i => $sort ) {
			// Messages are:
			//  * articlefeedbackv5-special-sort-relevance-desc
			//  * articlefeedbackv5-special-sort-relevance-asc
			//  * articlefeedbackv5-special-sort-age-desc
			//  * articlefeedbackv5-special-sort-age-asc
			if ( $i % 2 == 0 && $i > 0 ) {
				// Add dividers between each pair (append trailing spaces so
				// that they all get added)
				$opts[ '---------' . str_repeat( ' ', $i ) ] = '';
			}
			$key = $this->msg( 'articlefeedbackv5-special-sort-' . $sort )->escaped();
			$opts[ (string) $key ] = $sort;
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
					$this->msg( 'articlefeedbackv5-special-sort-label-before' )->escaped()
				) .
				Html::rawElement(
					'div',
					array( 'id' => 'articleFeedbackv5-sort-wrapper' ),
					$sortSelect->getHTML()
				) .
				$this->msg( 'articlefeedbackv5-special-sort-label-after' )->escaped()
			);
	}

	/**
	 * Takes an associative array of label to value and converts the message
	 * names into localized strings
	 *
	 * @param  $options array the options, indexed by label
	 * @return array    the options, indexed by localized and escaped text
	 */
	private function selectMsg( array $options ) {
		$newOpts = array();
		foreach ( $options as $label => $value ) {
			$newOpts[$this->msg( $label )->escaped()] = $value;
		}
		return $newOpts;
	}

	/**
	 * Grabs the overall rating for a page
	 *
	 * @param  $pageId int the page id
	 * @return array   the overall rating, as array (found => %, rating => avg)
	 */
	private function fetchOverallRating( $pageId ) {
		$rv = array();
		$dbr = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			array(
				'aft_article_feedback_ratings_rollup',
				'aft_article_field'
			),
			array(
				'arr_total / arr_count AS rating',
				'afi_name'
			),
			array(
				'arr_page_id' => $pageId,
				'arr_field_id = afi_id',
				'afi_name' => array( 'found', 'rating' )
			)
		);

		foreach ( $rows as $row ) {
			if ( $row->afi_name == 'found' ) {
				$rv['found'] = ( int ) ( 100 * $row->rating );
			} elseif ( $row->afi_name == 'rating' ) {
				$rv['rating'] = ( int ) $row->rating;
			}
		}

		return $rv;
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

		// Was a filter requested in the url?
		if ( $filter ) {
			if ( in_array( $filter, $this->filters ) ) {
				// pass through;
			} elseif ( in_array( 'all-' . $filter, $this->filters ) ) {
				$filter = 'all-' . $filter;
			} elseif ( in_array( 'notdeleted-' . $filter, $this->filters ) ) {
				$filter = 'notdeleted-' . $filter;
			} elseif ( in_array( 'visible-' . $filter, $this->filters ) ) {
				$filter = 'visible-' . $filter;
			} else {
				$filter = false;
			}
		}

		// Was a filter requested via cookie?
		if ( !$filter && $this->feedbackId === null ) {
			$request = $this->getRequest();
			global $wgArticleFeedbackv5Tracking;
			$version = isset($wgArticleFeedbackv5Tracking['version']) ? $wgArticleFeedbackv5Tracking['version'] : 0;
			$cookie = json_decode( $request->getCookie( 'last-filter', 'ext.articleFeedbackv5@' . $version . '-' ) );
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
		if ( !$filter ) {
			if ( $this->feedbackId ) {
				$filter = 'id';
			} elseif ( $this->isAllowed( 'aft-oversighter' ) ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['deleted'];
			} elseif ( $this->isAllowed( 'aft-monitor' ) ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['hidden'];
			} elseif ( $this->isAllowed( 'aft-editor' ) ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['featured'];
			} else {
				$filter = $wgArticleFeedbackv5DefaultFilters['all'];
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
				$dir  = $cookie_dir;
			}
		}

		// Decide on our default sort info
		if ( !$sort ) {
			$key = $this->shortFilter( $filter );
			list( $sort, $dir ) = $wgArticleFeedbackv5DefaultSorts[$key];
		}

		$this->startingFilter = $filter;
		$this->startingSort = $sort;
		$this->startingSortDirection = $dir;
	}

	/**
	 * Returns the starting filter with permissions info stripped out
	 *
	 * @param  $filter string the long filter name
	 * @return string  the short filter name
	 */
	protected function shortFilter( $filter ) {
		return str_replace( array( 'all-', 'visible-', 'notdeleted-' ), '', $filter );
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
