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
	 * Whether to show featured feedback
	 *
	 * @var bool
	 */
	protected $showFeatured;

	/**
	 * Whether to show hidden feedback
	 *
	 * @var bool
	 */
	protected $showHidden;

	/**
	 * Whether to show deleted feedback
	 *
	 * @var bool
	 */
	protected $showDeleted;

	/**
	 * The page ID we're operating on (null for central log)
	 *
	 * @var int
	 */
	protected $pageId;

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
	 * The starting sort direction
	 *
	 * @var string
	 */
	protected $startingSortDirection;

	/**
	 * The starting limit
	 *
	 * @var int
	 */
	protected $startingLimit;

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

		$user = $this->getUser();
		$this->showHidden = $user->isAllowed( 'aftv5-see-hidden-feedback' );
		$this->showDeleted = $user->isAllowed( 'aftv5-see-deleted-feedback' );
		$this->showFeatured = $user->isAllowed( 'aftv5-feature-feedback' );

		$this->filters = $this->defaultFilters;
		if ( $this->showDeleted ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-resolved',
				'all-hidden',
				'all-requested', 'all-declined',
				'notdeleted-requested', 'all-oversighted', 'all'
			);
		} elseif ( $this->showHidden ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-resolved',
				'notdeleted-hidden',
				'notdeleted-declined','notdeleted'
			);
		} elseif ( $this->showFeatured ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-resolved'
			);
		}

		$this->sorts = array( 'relevance-asc', 'relevance-desc', 'age-desc', 'age-asc' );
		if ( $this->showFeatured ) {
			array_push( $this->sorts, 'helpful-desc', 'helpful-asc' );
		}

		global $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		if ( $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay ) {
			$this->startingLimit = $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;
		}

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
			$this->title  = $title;
		}

		// Select filter, sort, and sort direction
		$this->setFilterSortDirection(
			$request->getText( 'filter' ),
			$request->getText( 'sort' )
		);

		// Fetch
		$fetch = $this->fetchData();
		$fetched = $fetch->run();

		// Build renderer
		$permalink = ( 'id' == $fetch->getFilter() );
		$central   = ( $this->pageId ? false : true );
		$renderer  = new ArticleFeedbackv5Render( $user, $permalink, $central );

		// Title
		if ( $permalink ) {
			$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-permalink-pagetitle', $this->title )->escaped() );
		} elseif ( $this->pageId ) {
			$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-pagetitle', $this->title )->escaped() );
		} else {
			$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-central-pagetitle' )->escaped() );
		}

		// Wrap the whole thing in a div
		$out->addHTML(
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-special-wrap' ) )
		);

		// Header links
		$this->outputHeaderLinks();

		if ( $permalink ) {
			$this->outputPermalink( $renderer, $fetched );
		} else {
			$this->outputListing( $renderer, $fetched );
		}

		// Close the wrapper
		$out->addHTML(
			Html::closeElement( 'div' )
		);

		// JS variables
		$out->addJsConfigVars( 'afPageId', $this->pageId );
		$out->addJsConfigVars( 'afReferral', $request->getText( 'ref', 'url' ) );
		$out->addJsConfigVars( 'afStartingFilter', $this->startingFilter );
		$out->addJsConfigVars( 'afStartingFilterValue', $this->startingFilter == 'id' ? $this->feedbackId : null );
		$out->addJsConfigVars( 'afStartingSort', $this->startingSort );
		$out->addJsConfigVars( 'afStartingSortDirection', $this->startingSortDirection );
		$out->addJsConfigVars( 'afStartingLimit', $this->startingLimit );
		$out->addJsConfigVars( 'afCount', $fetch->overallCount() );
		if ( isset( $fetched->continue ) ) {
			$out->addJsConfigVars( 'afContinue', $fetched->continue );
		}
		$out->addJsConfigVars( 'afShowMore', $fetched->showMore );

	}

	/**
	 * Fetch the requested data
	 *
	 * @return	ArticleFeedbackv5Fetch	The fetch-object
	 */
	protected function fetchData() {
		$fetch = new ArticleFeedbackv5Fetch();
		$fetch->setFilter( $this->startingFilter );
		$fetch->setFeedbackId( $this->feedbackId );
		$fetch->setPageId( $this->pageId );
		$fetch->setSort( $this->startingSort );
		$fetch->setSortOrder( $this->startingSortDirection );
		$fetch->setLimit( $this->startingLimit );

		return $fetch;
	}

	/**
	 * Outputs the header links in the top right corner
	 *
	 * View Article | Discussion | Help
	 */
	protected function outputHeaderLinks() {
		$user = $this->getUser();
		$out = $this->getOutput();

		$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl')->text();
		if( $user->isAllowed( 'aftv5-delete-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-oversighters' )->text();
		} elseif( $user->isAllowed( 'aftv5-hide-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-monitors' )->text();
		} elseif( $user->isAllowed( 'aftv5-feature-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-editors' )->text();
		}
		$helpLink .= '#Feedback_page';

		$out->addHTML(
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-wrap' ) )
				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-links' ) )
			);

		// Only add the links to the article and its talk page if there is one
		if ( $this->pageId ) {
			$out->addHTML(
					Linker::link(
						$this->title,
						$this->msg( 'articlefeedbackv5-go-to-article' )->escaped()
					)
					. ' | ' .
					Linker::link(
						$this->title->getTalkPage(),
						$this->msg( 'articlefeedbackv5-discussion-page' )->escaped()
					)
					. ' | '
				);
		}

		$out->addHTML(
				Html::element(
					'a',
					array( 'href' => $helpLink ),
					$this->msg( 'articlefeedbackv5-whats-this' )->escaped()
				)
			. Html::closeElement( 'div' )
		);
	}

	/**
	 * Outputs a permalink
	 *
	 * @param $renderer ArticleFeedbackv5Render the renderer
	 * @param $fetched  stdClass                the fetched records &etc.
	 */
	protected function outputPermalink( $renderer, $fetched ) {
		$out = $this->getOutput();

		// validate that data was found
		if ( count( $fetched->records ) != 1 ) {
			return;
		}

		$record = array_pop( $fetched->records );

		// Close the header
		$out->addHTML( Html::closeElement( 'div' ) );

		// Top linkback
		$out->addHTML(
			Html::rawElement( 'div', array(
					'class' => 'articleFeedbackv5-feedback-permalink-goback'
				), Linker::link(
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $this->title->getPrefixedText() ),
					'&lsaquo; ' . wfMessage( 'articlefeedbackv5-special-goback' )->escaped()
				)
			)
		);

		// Render
		$out->addHTML( Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-show-feedback' ) ) );
		$out->addHTML( $renderer->run( $record ) );
		$out->addHTML( Html::closeElement( 'div' ) );

		// Bottom linkback
		$out->addHTML(
			Html::rawElement( 'div', array(
					'class' => 'articleFeedbackv5-feedback-permalink-goback'
				), Linker::link(
					SpecialPage::getTitleFor( 'ArticleFeedbackv5', $this->title->getPrefixedText() ),
					'&lsaquo; ' . wfMessage( 'articlefeedbackv5-special-goback' )->escaped()
				)
			)
		);
	}

	/**
	 * Outputs a listing
	 *
	 * @param $renderer ArticleFeedbackv5Render the renderer
	 * @param $fetched  stdClass                the fetched records &etc.
	 */
	protected function outputListing( $renderer, $fetched ) {
		$user = $this->getUser();
		$out = $this->getOutput();

		// Notices
		$this->outputNotices();

		// Add controls block
		$out->addHTML( Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort-filter-controls' ) ) );
		$this->outputFilters();
		$this->outputSort();
		$out->addHTML( Html::closeElement( 'div' ) );

		// Open feedback output
		$class = '';
		if ( !$this->pageId ) {
			$class = 'articleFeedbackv5-central-feedback-log';
		}
		$out->addHTML(
			Html::openElement( 'div', array(
				'id'    => 'articleFeedbackv5-show-feedback',
				'class' => $class
			) )
		);

		// Rows
		foreach ( $fetched->records as $record ) {
			$out->addHTML( $renderer->run( $record ) );
		}

		// Close feedback output
		$out->addHTML(
			Html::closeElement( 'div' )

			. Html::openElement(
				'div',
				array(
					'id' => 'articleFeedbackv5-footer'
				) )

				. Html::element(
					'a',
					array(
						'href' => '#more-feedback',
						'id'   => 'articleFeedbackv5-show-more'
					),
					$this->msg( 'articlefeedbackv5-special-more' )->text()
				)

				. Html::element(
					'a',
					array(
						'href' => '#refresh-feedback',
						'id'   => 'articleFeedbackv5-refresh-list'
					),
					$this->msg( 'articlefeedbackv5-special-refresh' )->text()
				)

			. Html::element( 'div', array( 'class' => 'clear' ) )

			. Html::closeElement( 'div' )
		);

		// Link back to the central page - only for editors
		if ( $this->pageId && $user->isAllowed( 'aftv5-feature-feedback' ) ) {
			$out->addHTML(
				Html::rawElement( 'div', array(
						'class' => 'articleFeedbackv5-feedback-central-goback'
					), Linker::link(
						SpecialPage::getTitleFor( 'ArticleFeedbackv5' ),
							wfMessage( 'articlefeedbackv5-special-central-goback'
						)->escaped()
					)
				)
			);
		}
	}

	/**
	 * Outputs the notices above the controls
	 *
	 * {% found}     BETA      Add Feedback
	 */
	protected function outputNotices() {
		$user = $this->getUser();
		$out = $this->getOutput();

		$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl')->text();
		if( $user->isAllowed( 'aftv5-delete-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-oversighters' )->text();
		} elseif( $user->isAllowed( 'aftv5-hide-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-monitors' )->text();
		} elseif( $user->isAllowed( 'aftv5-feature-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-special-linkurl-editors' )->text();
		}
		$helpLink .= '#Feedback_page';

		$helpLink = Html::openElement(
			'a',
			array( 'href' => $helpLink ) )
				. $this->msg( 'articlefeedbackv5-header-message-link-text' )->escaped() . ' &raquo;'
			.Html::closeElement( 'a' );

		// Header message
		$out->addHTML(
			Html::openElement(
				'p',
				array( 'id' => 'articlefeedbackv5-header-message' )
			)
				. $this->msg( 'articlefeedbackv5-header-message' )->rawParams( $helpLink )->text()
			. Html::closeElement( 'p' )
		);

		$this->outputSummary();

		// Link to add feedback (view article)
		if ( $this->pageId ) {
			$out->addHtml(
				Html::element(
					'a',
					array(
						'href'  => '#',
						'id'    => 'articleFeedbackv5-special-add-feedback',
					),
					$this->msg( 'articlefeedbackv5-special-add-feedback' )->text()
				)
			);
		}

		// Close the section
		$out->addHtml(
				Html::element( 'div', array( 'class' => 'float-clear' ) )
			. Html::closeElement( 'div' )
		);
	}

	/**
	 * Display the feedback page's summary information in header
	 */
	protected function outputSummary() {
		$out = $this->getOutput();
		$user = $this->getUser();

		// if we have a logged in user and are currently browsing the central feedback page,
		// check if there is feedback on his/her watchlisted pages
		$watchlistLink = '';
		if ( !$this->pageId && $user->getId() ) {
			$fetch = new ArticleFeedbackv5Fetch();
			$fetch->setUserId( $user->getId() );
			$fetch->setLimit( 1 );
			$fetched = $fetch->run();

			if ( count( $fetched->records ) > 0 ) {
				$watchlistLink =
					Html::openElement(
						'span',
						array( 'id' => 'articlefeedbackv5-special-central-watchlist-link' )
					)
						. $this->msg( 'articlefeedbackv5-special-central-watchlist-link',
							SpecialPage::getTitleFor( 'ArticleFeedbackv5Watchlist' )->getFullText()
						)->parse()
					. Html::closeElement( 'span' );
			}
		}

		// Showing {count} posts
		$out->addHTML(
			Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-showing-count-wrap' )
			)
				. $this->msg(
					$this->pageId ? 'articlefeedbackv5-special-showing' : 'articlefeedbackv5-special-central-showing',
					Html::element( 'span', array( 'id' => 'articleFeedbackv5-feedback-count-total' ), '0' )
				)
				. $watchlistLink
			. Html::closeElement( 'div' )
		);

		// % found
		if ( $this->pageId ) {
			$ratings = $this->fetchOverallRating( $this->pageId );
			$found   = isset( $ratings['found'] ) ? $ratings['found'] : null;
			if ( $found ) {
				$class = $found > 50 ? 'positive' : 'negative';

				$span = Html::rawElement( 'span', array(
					'class' => "stat-marker $class"
				), wfMessage( 'percent', $found )->escaped() );
				$out->addHtml(
					Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-percent-found-wrap' ) )
						. $this->msg( 'articlefeedbackv5-percent-found' )->rawParams( $span )->escaped()
						. Html::closeElement( 'div' )
				);
			}
		}
	}

	/**
	 * Outputs the page filter controls
	 *
	 * Showing: [filters...]
	 */
	protected function outputFilters() {
		$out = $this->getOutput();

		// Filtering
		$counts = $this->getFilterCounts();

		$filterLabels = array();
		foreach ( $this->topFilters as $filter ) {
			if ( $this->startingFilter == $filter ) {
				$class = 'articleFeedbackv5-filter-link filter-active';
			} else {
				$class = 'articleFeedbackv5-filter-link';
			}
			$count = array_key_exists( $filter, $counts ) ? $counts[$filter] : 0;
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
		if ( $this->showFeatured ) {
			$opts = array();
			$foundNonDefaults = false;
			foreach ( $this->filters as $filter ) {
				$count = array_key_exists( $filter, $counts ) ? $counts[$filter] : 0;
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

		$out->addHTML(
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-filter' ) )
				. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-filter-label' ) )
					. $this->msg( 'articlefeedbackv5-special-filter-label-before' )->escaped()
				. Html::closeElement( 'span' )

				. implode( ' ', $filterLabels )

				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-select-wrapper' ) )
					. $filterSelectHtml
				. Html::closeElement( 'div' )

				. $this->msg( 'articlefeedbackv5-special-filter-label-after' )->escaped()
			. Html::closeElement( 'div' )
		);
	}

	/**
	 * Outputs the page sort controls
	 *
	 * Showing: Sort by: Relevance | Helpful | Rating | Date
	 */
	protected function outputSort() {
		$out = $this->getOutput();

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

		$out->addHTML(
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort' ) )
				. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-sort-label' ) )
					. $this->msg( 'articlefeedbackv5-special-sort-label-before' )->escaped()
				. Html::closeElement( 'span' )

				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort-wrapper' ) )
				. $sortSelect->getHTML()
				. Html::closeElement( 'div' )

				. $this->msg( 'articlefeedbackv5-special-sort-label-after' )->escaped()
			. Html::closeElement( 'div' )
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
	 * Gets the counts for the filter
	 *
	 * @return array the counts, as filter => count
	 */
	private function getFilterCounts() {
		if ( !isset( $this->filterCounts ) ) {
			$rv   = array();
			$dbr  = wfGetDB( DB_SLAVE );
			$rows = $dbr->select(
				'aft_article_filter_count',
				array(
					'afc_filter_name',
					'afc_filter_count'
				),
				array(
					'afc_page_id' => $this->pageId ? $this->pageId : 0
				),
				__METHOD__
			);
			foreach ( $rows as $row ) {
				$rv[ $row->afc_filter_name ] = $row->afc_filter_count;
			}
			$this->filterCounts = $rv;
		}
		return $this->filterCounts;
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
				&& $this->pageId == $cookie->page
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
			} elseif ( $this->showDeleted ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['deleted'];
			} elseif ( $this->showHidden ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['hidden'];
			} elseif ( $this->showFeatured ) {
				$filter = $wgArticleFeedbackv5DefaultFilters['featured'];
			} else {
				$filter = $wgArticleFeedbackv5DefaultFilters['all'];
			}
		}

		// Switch from relevant to all comments if the count is zero
		$counts = $this->getFilterCounts();
		if ( !isset( $counts[$filter] ) || $counts[$filter] == 0 ) {
			if ( $filter == 'visible-relevant' ) {
				$filter = 'visible-comment';
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
		return str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);
	}

}

