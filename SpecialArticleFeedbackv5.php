<?php
/**
 * SpecialArticleFeedbackv5 class
 *
 * @package    ArticleFeedback
 * @subpackage Special
 * @author     Greg Chiasson <gchiasson@omniti.com>
 * @author     Elizabeth M Smith <elizabeth@omniti.com>
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
	private $filters;

	/**
	 * The sorts available
	 *
	 * @var array
	 */
	private $sorts;

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
	public function __construct() {
		wfProfileIn( __METHOD__ );

		global $wgUser;
		parent::__construct( 'ArticleFeedbackv5' );

		$this->showHidden = $wgUser->isAllowed( 'aftv5-see-hidden-feedback' );
		$this->showDeleted = $wgUser->isAllowed( 'aftv5-see-deleted-feedback' );
		$this->showFeatured = $wgUser->isAllowed( 'aftv5-feature-feedback' );

		$this->filters = $this->defaultFilters;
		if ( $this->showDeleted ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-resolved',
				'all-hidden',
				'all-requested', 'all-declined',
				'all-oversighted', 'all'
			);
		} elseif ( $this->showHidden ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-resolved',
				'notdeleted-hidden',
				'notdeleted-requested', 'notdeleted-declined','notdeleted'
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
		global $wgUser, $wgRequest;

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
			$wgRequest->getText( 'filter' ),
			$wgRequest->getText( 'sort' )
		);

		// Fetch
		$fetch = new ArticleFeedbackv5Fetch( $this->startingFilter,
			$this->feedbackId, $this->pageId );
		$fetch->setSort( $this->startingSort );
		$fetch->setSortOrder( $this->startingSortDirection );
		$fetch->setLimit( $this->startingLimit );
		$fetched = $fetch->run();

		// Build renderer
		$permalink = ( 'id' == $fetch->getFilter() );
		$central   = ( $this->pageId ? false : true );
		$renderer  = new ArticleFeedbackv5Render( $wgUser, $permalink, $central );

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
			// <div id="articleFeedbackv5-special-wrap">
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
			// </div>
			Html::closeElement( 'div' )
		);

		// JS variables
		$out->addJsConfigVars( 'afPageId', $this->pageId );
		$out->addJsConfigVars( 'afReferral', $wgRequest->getText( 'ref', 'url' ) );
		// Only show the abuse counts to editors (ie, anyone allowed to
		// hide content).
		if ( $wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) ) {
			$out->addJsConfigVars( 'afCanEdit', 1 );
		}
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
	 * Outputs the header links in the top right corner
	 *
	 * View Article | Discussion | Help
	 */
	public function outputHeaderLinks() {
		global $wgUser;
		$out = $this->getOutput();

		$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl')->text();
		if( $wgUser->isAllowed( 'aftv5-delete-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-oversighters' )->text();
		} elseif( $wgUser->isAllowed( 'aftv5-hide-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-monitors' )->text();
		} elseif( $wgUser->isAllowed( 'aftv5-feature-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-editors' )->text();
		}
		$helpLink .= '#Feedback_page';

		$out->addHTML(
			// <div id="articleFeedbackv5-header-wrap">
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-wrap' ) )
				// <div id="articleFeedbackv5-header-links">
				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-links' ) )
			);

		// Only add the links to the article and its talk page if there is one
		if ( $this->pageId ) {
			$out->addHTML(
					// <a href="{article link}">
					//   {msg:articlefeedbackv5-go-to-article}
					// </a>
					Linker::link(
						$this->title,
						$this->msg( 'articlefeedbackv5-go-to-article' )->escaped()
					)
					. ' | ' .
					// <a href="{talk page link}">
					//   {msg:articlefeedbackv5-discussion-page}
					// </a>
					Linker::link(
						$this->title->getTalkPage(),
						$this->msg( 'articlefeedbackv5-discussion-page' )->escaped()
					)
					. ' | '
				);
		}

		$out->addHTML(
				// <a href="{help link}">
				//   {msg:articlefeedbackv5-whats-this}
				// </a>
				Html::element(
					'a',
					array( 'href' => $helpLink ),
					$this->msg( 'articlefeedbackv5-whats-this' )->escaped()
				)
			// </div>
			. Html::closeElement( 'div' )
		);
	}

	/**
	 * Outputs a permalink
	 *
	 * @param $renderer ArticleFeedbackv5Render the renderer
	 * @param $fetched  stdClass                the fetched records &etc.
	 */
	public function outputPermalink( $renderer, $fetched ) {
		$out = $this->getOutput();
		$record = array_pop( $fetched->records );

		// Close the header
		$out->addHTML( Html::closeElement( 'div' ) );

		// Top linkback
		$out->addHTML(
			// <div class="articleFeedbackv5-feedback-permalink-goback">
			//   <a href="{page title}">&laquo; {msg:articlefeedbackv5-special-goback}</a>
			// </div>
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
			// <div class="articleFeedbackv5-feedback-permalink-goback">
			//   <a href="{page title}">&laquo; {msg:articlefeedbackv5-special-goback}</a>
			// </div>
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
	public function outputListing( $renderer, $fetched ) {
		global $wgUser;

		$out = $this->getOutput();

		// Notices
		$this->outputNotices();

		// Controls
		$this->outputControls();

		// Open feedback output
		$class = '';
		if ( !$this->pageId ) {
			$class = 'articleFeedbackv5-central-feedback-log';
		}
		$out->addHTML(
			// <div id="articleFeedbackv5-show-feedback"
			//   {class="articleFeedbackv5-central-feedback"?}>
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
			// </div>
			Html::closeElement( 'div' )
			// <a href="#" id="articleFeedbackv5-show-more">
			//   {msg:articlefeedbackv5-special-more}
			// </a>
			. Html::element(
				'a',
				array(
					'href' => '#more-feedback',
					'id'   => 'articleFeedbackv5-show-more'
				),
				$this->msg( 'articlefeedbackv5-special-more' )->text()
			)
		);

		// Link back to the central page - only for editors
		if ( $this->pageId && $wgUser->isAllowed( 'aftv5-feature-feedback' ) ) {
			$out->addHTML(
				// <div class="articleFeedbackv5-feedback-central-goback">
				//   <a href="{page title}">{msg:articlefeedbackv5-special-central-goback}</a>
				// </div>
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
	public function outputNotices() {
		global $wgUser;
		$out = $this->getOutput();

		$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl')->text();
		if( $wgUser->isAllowed( 'aftv5-delete-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-oversighters' )->text();
		} elseif( $wgUser->isAllowed( 'aftv5-hide-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-monitors' )->text();
		} elseif( $wgUser->isAllowed( 'aftv5-feature-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-editors' )->text();
		}
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

		// Showing {count} posts
		$out->addHTML(
			// <div id="articleFeedbackv5-showing-count-wrap">
			Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-showing-count-wrap' )
			)
				// {msg:articlefeedbackv5-special-showing} with
				// <span id="articleFeedbackv5-feedback-count-total">{count}</span>
				. $this->msg(
					$this->pageId ? 'articlefeedbackv5-special-showing' : 'articlefeedbackv5-special-central-showing',
					Html::element( 'span', array( 'id' => 'articleFeedbackv5-feedback-count-total' ), '0' )
				)
			// </div>
			. Html::closeElement( 'div' )
		);

		// % found
		if ( $this->pageId ) {
			$ratings = $this->fetchOverallRating( $this->pageId );
			$found   = isset( $ratings['found'] ) ? $ratings['found'] : null;
			if ( $found ) {
				$class = $found > 50 ? 'positive' : 'negative';
				// <span class="stat-marker {positive|negative}">{msg:percent}</span>
				$span = Html::rawElement( 'span', array(
					'class' => "stat-marker $class"
				), wfMsg( 'percent', $found ) );
				$out->addHtml(
					// <div id="articleFeedbackv5-percent-found-wrap">
					Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-percent-found-wrap' ) )
						// {msg:articlefeedbackv5-percent-found} with span above
						. $this->msg( 'articlefeedbackv5-percent-found' )->rawParams( $span )->escaped()
					// </div>
					. Html::closeElement( 'div' )
				);
			}
		}

		// Survey button
		global $wgArticleFeedbackv5SpecialPageSurveyUrl;
		if ( $wgArticleFeedbackv5SpecialPageSurveyUrl ) {
			$out->addHTML(
				// <a href="{survey-url}" target="_blank" class="articleFeedbackv5-survey-button">
				//   {msg:articlefeedbackv5-special-survey-button-text}
				// </span>
				Html::element( 'a', array(
					'href'   => $wgArticleFeedbackv5SpecialPageSurveyUrl,
					'target' => '_blank',
					'class'  => 'articleFeedbackv5-survey-button',
				), $this->msg( 'articlefeedbackv5-special-survey-button-text' )->text() )
			);
		}

		// Link to add feedback (view article)
		if ( $this->pageId ) {
			$out->addHtml(
				// <a href="#" id="articleFeedbackv5-special-add-feedback">
				//   {msg:articlefeedbackv5-special-add-feedback}
				// </a>
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
				// <div class="float-clear"></div>
				Html::element( 'div', array( 'class' => 'float-clear' ) )
			// </div>
			. Html::closeElement( 'div' )
		);
	}

	/**
	 * Outputs the page controls
	 *
	 * Showing: [filters...]  Sort by: Relevance | Helpful | Rating | Date
	 */
	public function outputControls() {
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
			// <a href="#" id="articleFeedbackv5-special-filter-{$filter}"
			//   class="articleFeedbackv5-filter-link">
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
			// </a>
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
				// <select id="articleFeedbackv5-filter-select">
				//   <option value="{each filter name}">{each filter message}</option>
				// </select>
				$filterSelect = new XmlSelect( false, 'articleFeedbackv5-filter-select' );
				$filterSelect->setDefault( $this->startingFilter );
				$filterSelect->addOptions( $opts );
				$filterSelectHtml = $filterSelect->getHTML();
			}
		}

		$filterBlock =
			// <div id="articleFeedbackv5-filter">
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-filter' ) )
				// <span class="articleFeedbackv5-filter-label">
				. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-filter-label' ) )
					// {msg:articlefeedbackv5-special-filter-label-before}
					. $this->msg( 'articlefeedbackv5-special-filter-label-before' )->escaped()
				// </span>
				. Html::closeElement( 'span' )
				// {filter labels}
				. implode( ' ', $filterLabels )
				// <div id="">
				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-select-wrapper' ) )
					// {filter select}
					. $filterSelectHtml
				// </div>
				. Html::closeElement( 'div' )
				// {msg:articlefeedbackv5-special-filter-label-after'}
				. $this->msg( 'articlefeedbackv5-special-filter-label-after' )->escaped()
			// </div>
			. Html::closeElement( 'div' );

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
		// <select id="articleFeedbackv5-sort-select">
		//   <option value="{each sort name}">{each sort message}</option>
		// </select>
		$sortSelect = new XmlSelect( false, 'articleFeedbackv5-sort-select' );
		$sortSelect->setDefault( $this->startingSort . '-' . $this->startingSortDirection );
		$sortSelect->addOptions( $opts );

		$sortBlock =
			// <div id="articleFeedbackv5-sort">
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort' ) )
				// <span class="articleFeedbackv5-sort-label">
				. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-sort-label' ) )
					// {msg:articlefeedbackv5-special-sort-label-before}
					. $this->msg( 'articlefeedbackv5-special-sort-label-before' )->escaped()
				// </span>
				. Html::closeElement( 'span' )
				// {sort select}
				. $sortSelect->getHTML()
				// {msg:articlefeedbackv5-special-sort-label-after}
				. $this->msg( 'articlefeedbackv5-special-sort-label-after' )->escaped()
			// </div>
			. Html::closeElement( 'div' );

		// Add controls block
		$out->addHTML(
			// <div id="articleFeedbackv5-sort-filter-controls">
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort-filter-controls' ) )
				// {filter label and select list}
				. $filterBlock
				// {sort label and select list}
				. $sortBlock
			// </div>
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
				array(),
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
	public function setFilterSortDirection( $filter, $sort ) {
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
			global $wgArticleFeedbackv5Tracking, $wgRequest;
			$version = isset($wgArticleFeedbackv5Tracking['version']) ? $wgArticleFeedbackv5Tracking['version'] : 0;
			$cookie = json_decode( $wgRequest->getCookie( 'last-filter', 'ext.articleFeedbackv5@' . $version . '-' ) );
			if ( $cookie !== null && is_object( $cookie )
				&& isset( $cookie->page ) && $this->pageId == $cookie->page
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
	public function shortFilter( $filter ) {
		return str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);
	}

}

