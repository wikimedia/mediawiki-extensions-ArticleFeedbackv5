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
class SpecialArticleFeedbackv5 extends UnlistedSpecialPage {

	/**
	 * The filters available
	 *
	 * Will be create on construction based on user permissions
	 *
	 * @var array
	 */
	private $filters;

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
	 * Constructor
	 */
	public function __construct() {
		global $wgUser;
		parent::__construct( 'ArticleFeedbackv5' );

		$this->showHidden = $wgUser->isAllowed( 'aftv5-see-hidden-feedback' );
		$this->showDeleted = $wgUser->isAllowed( 'aftv5-see-deleted-feedback' );
		$this->showFeatured = $wgUser->isAllowed( 'aftv5-feature-feedback' );
		$this->filters = $this->defaultFilters;
		$this->sorts = ArticleFeedbackv5Fetch::$knownSorts;

		if ( $this->showDeleted ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive',  'visible-unfeatured', 'visible-resolved', 'visible-unresolved',
				'all-hidden', 'all-unhidden',
				'all-requested', 'all-unrequested', 'all-declined',
				'all-oversighted', 'all-unoversighted', 'all'
			);
		} elseif ( $this->showHidden ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-unfeatured', 'visible-resolved', 'visible-unresolved',
				'notdeleted-hidden', 'notdeleted-unhidden',
				'notdeleted-requested', 'notdeleted-unrequested', 'notdeleted-declined','notdeleted'
			);
		} elseif ( $this->showFeatured ) {
			array_push( $this->filters,
				'visible-unhelpful', 'visible-abusive', 'visible-unfeatured', 'visible-resolved', 'visible-unresolved'
			);
		}
	}

	/**
	 * Executes the special page
	 *
	 * @param $param string the parameter passed in the url
	 */
	public function execute( $param ) {
		global $wgArticleFeedbackv5DashboardCategory, $wgArticleFeedbackv5DefaultSorts, $wgArticleFeedbackv5DefaultFilters, $wgUser;
		$out = $this->getOutput();

		// set robot policy
		$out->setIndexPolicy('noindex');

		if ( !$param ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}

		// If this is a permalink, grab the page title
		if ( preg_match('/^(.+)\/(\d+)$/', $param, $m ) ) {
			$param = $m[1];
			$this->feedbackId = $m[2];
		}

		$title = Title::newFromText( $param );

		// Page does not exist.
		if ( !$title->exists() ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}

		$pageId = $title->getArticleID();
		$dbr    = wfGetDB( DB_SLAVE );
		$t      = $dbr->select(
			'categorylinks',
			'cl_from',
			array(
				'cl_from' => $pageId,
				'cl_to'   => $wgArticleFeedbackv5DashboardCategory
			),
			__METHOD__,
			array( 'LIMIT' => 1 )
		);

		// Page exists, but feedback is disabled.
		if ( $dbr->numRows( $t ) == 0 ) {
			$out->addWikiMsg( 'articlefeedbackv5-page-disabled' );
			return;
		}

		// Success!
		$ratings = $this->fetchOverallRating( $pageId );
		$found   = isset( $ratings['found'] ) ? $ratings['found'] : null;
		$rating  = isset( $ratings['rating'] ) ? $ratings['rating'] : null;

		$out->setPagetitle(
			$this->msg( 'articlefeedbackv5-special-pagetitle', $title )->escaped()
		);

		if ( !$pageId ) {
			$out->addWikiMsg( 'articlefeedbackv5-invalid-page-id' );
			return;
		}


		$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl')->text();
		if( $wgUser->isAllowed( 'aftv5-delete-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-oversighters' )->text();
		} elseif( $wgUser->isAllowed( 'aftv5-hide-feedback' ) ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-monitors' )->text();
		} elseif( !$wgUser->isAnon() ) {
			$helpLink = $this->msg( 'articlefeedbackv5-help-tooltip-linkurl-editors' )->text();
		}

		$out->addHTML(
			// <div id="articleFeedbackv5-header-wrap">
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-wrap' ) )
				// <div id="articleFeedbackv5-header-links">
				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-header-links' ) )
					// <a href="{article link}">
					//   {msg:articlefeedbackv5-go-to-article}
					// </a>
					. Linker::link(
						$title,
						$this->msg( 'articlefeedbackv5-go-to-article' )->escaped()
					)
					. ' | ' .
					// <a href="{talk page link}">
					//   {msg:articlefeedbackv5-discussion-page}
					// </a>
					Linker::link(
						$title->getTalkPage(),
						$this->msg( 'articlefeedbackv5-discussion-page' )->escaped()
					)
					. ' | ' .
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
				// <div id="articleFeedbackv5-showing-count-wrap">
				. Html::openElement(
					'div',
					array( 'id' => 'articleFeedbackv5-showing-count-wrap' )
				)
					// {msg:articlefeedbackv5-special-showing} with
					// <span id="articleFeedbackv5-feedback-count-total">{count}</span>
					. $this->msg(
						'articlefeedbackv5-special-showing',
						Html::element( 'span', array( 'id' => 'articleFeedbackv5-feedback-count-total' ), '0' )
					)
				// </div>
				. Html::closeElement( 'div' )
		);

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

		// BETA notice
		$out->addHTML(
			// <span class="articlefeedbackv5-beta-notice">
			//   {msg:articlefeedbackv5-beta-notice}
			// </span>
		    Html::element( 'span', array(
			    'class' => 'articlefeedbackv5-beta-notice'
		    ), $this->msg( 'articlefeedbackv5-beta-notice' )->text() )
			// <div class="float-clear"></div>
			. Html::element( 'div', array( 'class' => 'float-clear' ) )
		);

		/*
		// Temporary "This is a prototype" disclaimer text - removed per Fabrice 4/25
		$out->addHTML(
			// <div class="articlefeedbackv5-special-disclaimer">
			//   {msg:articlefeedbackv5-special-disclaimer}
			// </div>
			Html::element( 'div', array(
				'class' => 'articlefeedbackv5-special-disclaimer'
			), $this->msg( 'articlefeedbackv5-special-disclaimer' )->text() )
		);*/

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
			// <div class="float-clear"></div>
			. Html::element( 'div', array( 'class' => 'float-clear' ) )
			// </div>
			. Html::closeElement( 'div' )
		);

		// Sorting
		// decide on our default sort info
		if ( $this->showDeleted ) {
			list( $default, $dir ) = $wgArticleFeedbackv5DefaultSorts['deleted'];
		} elseif ( $this->showHidden ) {
			list( $default, $dir ) = $wgArticleFeedbackv5DefaultSorts['hidden'];
		} elseif ( $this->showFeatured ) {
			list( $default, $dir ) = $wgArticleFeedbackv5DefaultSorts['featured'];
		} else {
			list( $default, $dir ) = $wgArticleFeedbackv5DefaultSorts['all'];
		}
		$this->startingSort = $default;
		$this->startingSortDirection = $dir;

		$sortLabels = array();
		foreach ( $this->sorts as $sort ) {
			if ( $default == $sort ) {
				$sort_class = 'articleFeedbackv5-sort-link sort-active';
				$arrow_class = 'articleFeedbackv5-sort-arrow sort-' . $dir;
			} else {
				$sort_class = 'articleFeedbackv5-sort-link';
				$arrow_class = 'articleFeedbackv5-sort-arrow';
			}

			// <a href="#" id="articleFeedbackv5-special-sort-{$sort}"
			//   class="articleFeedbackv5-sort-link">
			$sortLabels[] = Html::openElement( 'a',
					array(
						'href'  => '#',
						'id'    => 'articleFeedbackv5-special-sort-' . $sort,
						'class' => $sort_class
					)
				)
				// {msg:articlefeedbackv5-special-sort-{$sort}}
				// Messages are:
				//  * articlefeedbackv5-special-sort-relevance
				//  * articlefeedbackv5-special-sort-helpful
				//  * articlefeedbackv5-special-sort-rating
				//  * articlefeedbackv5-special-sort-age
				. $this->msg( 'articlefeedbackv5-special-sort-' . $sort )->escaped()
				// <span id="articleFeedbackv5-sort-arrow-{$sort}"
				//   class="articleFeedbackv5-sort-arrow">
				// </span>
				. Html::element( 'span',
					array(
					'id'    => 'articleFeedbackv5-sort-arrow-' . $sort,
					'class' => $arrow_class
					)
				)
			// </a>
			. Html::closeElement( 'a' );
		}

		// Filtering
		$opts   = array();
		$counts = $this->getFilterCounts( $pageId );

		// decide on our default filter key name
		if ( $this->feedbackId ) {
			$default = 'id';
		} elseif ( $this->showDeleted ) {
			$default = $wgArticleFeedbackv5DefaultFilters['deleted'];
		} elseif ( $this->showHidden ) {
			$default = $wgArticleFeedbackv5DefaultFilters['hidden'];
		} elseif ( $this->showFeatured ) {
			$default = $wgArticleFeedbackv5DefaultFilters['featured'];
		} else {
			$default = $wgArticleFeedbackv5DefaultFilters['all'];
		}
		if ( !isset( $counts[$default] ) || $counts[$default] == 0 ) {
			if ( $default == 'visible-relevant' ) {
				$default = 'visible-comment';
			}
		}
		$this->startingFilter = $default;

		foreach ( $this->filters as $filter ) {
			$count = array_key_exists( $filter, $counts ) ? $counts[$filter] : 0;
			$msg_key = str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);
			$key   = $this->msg( 'articlefeedbackv5-special-filter-' . $msg_key, $count )->escaped();
			if( in_array( $filter, $this->defaultFilters ) ) {
				$opts[ (string) $key ] = $filter;
			} else {
				$opts[ '---------' ][ (string) $key ] = $filter;
			}
		}

		// <select id="articleFeedbackv5-filter-select">
		//   <option value="{each filter name}">{each filter message}</option>
		// </select>
		$filterSelect = new XmlSelect( false, 'articleFeedbackv5-filter-select' );
		$filterSelect->addOptions( $opts );
		$filterSelect->setDefault( $default );

		$out->addHTML(
			// <div id="articleFeedbackv5-sort-filter-controls">
			Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort-filter-controls' ) )
				// <div id="articleFeedbackv5-filter">
				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-filter' ) )
					// <span class="articleFeedbackv5-filter-label">
					. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-filter-label' ) )
						// {msg:articlefeedbackv5-special-filter-label-before}
						. $this->msg( 'articlefeedbackv5-special-filter-label-before' )->escaped()
					// </span>
					. Html::closeElement( 'span' )
					// {filter select}
					. $filterSelect->getHTML()
					// {msg:articlefeedbackv5-special-filter-label-after'}
					. $this->msg( 'articlefeedbackv5-special-filter-label-after' )->escaped()
				// </div>
				. Html::closeElement( 'div' )
				// <div id="articleFeedbackv5-sort">
				. Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-sort' ) )
					// <span class="articleFeedbackv5-sort-label">
					. Html::openElement( 'span', array( 'class' => 'articleFeedbackv5-sort-label' ) )
						// {msg:articlefeedbackv5-special-sort-label-before}
						. $this->msg( 'articlefeedbackv5-special-sort-label-before' )->escaped()
					// </span>
					. Html::closeElement( 'span' )
					// {pipe-separated sort labels}
					. implode( $this->msg( 'pipe-separator' )->escaped(), $sortLabels )
					// {msg:articlefeedbackv5-special-sort-label-after}
					. $this->msg( 'articlefeedbackv5-special-sort-label-after' )->escaped()
				// </div>
				. Html::closeElement( 'div' )
		);

		// Tools label
		if ( $wgUser->isAllowed( 'aftv5-delete-feedback' ) || $wgUser->isAllowed( 'aftv5-hide-feedback' )
		   || $wgUser->isAllowed( 'aftv5-feature-feedback' )) {
		    $out->addHTML(
				// <div id="articleFeedbackv5-tools-label">
				Html::openElement( 'div', array( 'id' => 'articleFeedbackv5-tools-label' ) )
					// {msg:articlefeedbackv5-form-tools-label}
					. $this->msg( 'articlefeedbackv5-form-tools-label' )->escaped()
				// </div>
				. Html::closeElement( 'div' )
		    );
		}
		// </div>
		$out->addHTML( Html::closeElement( 'div' ) );

		$out->addHTML(
			// <div id="articleFeedbackv5-show-feedback"></div>
			Html::element( 'div', array( 'id' => 'articleFeedbackv5-show-feedback' ) )
			// <a href="#" id="articleFeedbackv5-show-more">
			//   {msg:articlefeedbackv5-special-more}
			// </a>
			. Html::element(
				'a',
				array(
					'href' => '#',
					'id'   => 'articleFeedbackv5-show-more'
				),
				$this->msg( 'articlefeedbackv5-special-more' )->text()
			)
		);

		$out->addJsConfigVars( 'afPageId', $pageId );
		// Only show the abuse counts to editors (ie, anyone allowed to
		// hide content).
		if ( $wgUser->isAllowed( 'aftv5-see-hidden-feedback' ) ) {
			$out->addJsConfigVars( 'afCanEdit', 1 );
		}
		$out->addJsConfigVars( 'afStartingFilter', $this->startingFilter );
		$out->addJsConfigVars( 'afStartingFilterValue', $this->startingFilter == 'id' ? $this->feedbackId : null );
		$out->addJsConfigVars( 'afStartingSort', $this->startingSort );
		$out->addJsConfigVars( 'afStartingSortDirection', $this->startingSortDirection );
		$out->addModules( 'ext.articleFeedbackv5.dashboard' );
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

	private function getFilterCounts( $pageId ) {
		$rv   = array();
		$dbr  = wfGetDB( DB_SLAVE );
		$rows = $dbr->select(
			'aft_article_filter_count',
			array(
				'afc_filter_name',
				'afc_filter_count'
			),
			array(
				'afc_page_id' => $pageId
			),
			array(),
			__METHOD__
		);

		foreach ( $rows as $row ) {
			$rv[ $row->afc_filter_name ] = $row->afc_filter_count;
		}

		return $rv;
	}
}

