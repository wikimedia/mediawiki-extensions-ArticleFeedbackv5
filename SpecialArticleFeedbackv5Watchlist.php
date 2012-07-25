<?php
/**
 * SpecialArticleFeedbackv5Watchlist class
 *
 * @package    ArticleFeedback
 * @subpackage Special
 * @author     Matthias Mullie <mmullie@wikimedia.org>
 * @version    $Id$
 */

/**
 * This is the Special page the shows the feedback dashboard for pages on one's watchlist
 *
 * @package    ArticleFeedback
 * @subpackage Special
 */
class SpecialArticleFeedbackv5Watchlist extends SpecialArticleFeedbackv5 {

	/**
	 * The user ID we're operating on (null for no watchlist)
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * Constructor
	 */
	public function __construct(
		$name = 'ArticleFeedbackv5Watchlist', $restriction = '', $listed = true,
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
		$user = $this->getUser();
		$out = $this->getOutput();

		if ( $user->getId() ) {
			$this->userId = $user->getId();
		} else {
			$out->redirect(SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getFullUrl());
		}

		parent::execute( $param );

		$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-watchlist-pagetitle' )->escaped() );
	}

	/**
	 * Fetch the requested data
	 *
	 * @return	ArticleFeedbackv5Fetch	The fetch-object
	 */
	protected function fetchData() {
		$fetch = new ArticleFeedbackv5Fetch();
		$fetch->setFilter( $this->startingFilter );
		$fetch->setUserId( $this->userId );
		$fetch->setSort( $this->startingSort );
		$fetch->setSortOrder( $this->startingSortDirection );
		$fetch->setLimit( $this->startingLimit );

		return $fetch;
	}

	/**
	 * Don't display totals for watchlist feedback
	 */
	protected function outputSummary() {
		$user = $this->getUser();
		$out = $this->getOutput();

		// Showing {count} posts
		$out->addHTML(
			Html::openElement(
				'div',
				array( 'id' => 'articleFeedbackv5-showing-count-wrap' )
			)
				. $this->msg( 'articlefeedbackv5-special-watchlist-showing',
					$user->getUserPage()->getFullText(),
					$user->getName()
				)
				. Html::openElement(
						'span',
						array( 'id' => 'articlefeedbackv5-special-central-watchlist-link' )
					)
					. $this->msg( 'articlefeedbackv5-special-watchlist-central-link',
						SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getFullText()
					)->parse()
				. Html::closeElement( 'span' )
			. Html::closeElement( 'div' )
		);
	}

	/**
	 * Outputs the page controls
	 *
	 * Showing: [filters...]  Sort by: Relevance | Helpful | Rating | Date
	 */
	protected function outputFilters() {
		$out = $this->getOutput();

		$filterLabels = array();
		foreach ( $this->topFilters as $filter ) {
			if ( $this->startingFilter == $filter ) {
				$class = 'articleFeedbackv5-filter-link filter-active';
			} else {
				$class = 'articleFeedbackv5-filter-link';
			}
			$msg_key = str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);

			$filterLabels[] = Html::openElement( 'a',
				array(
					'href'  => '#',
					'id'    => 'articleFeedbackv5-special-filter-' . $filter,
					'class' => $class
				)
			)
				// {msg:articlefeedbackv5-special-filter-{$filter}-watchlist}
				// Messages are:
				//  * articlefeedbackv5-special-filter-relevant-watchlist
				//  * articlefeedbackv5-special-filter-featured-watchlist
				//  * articlefeedbackv5-special-filter-helpful-watchlist
				//  * articlefeedbackv5-special-filter-comment-watchlist
				//  * articlefeedbackv5-special-filter-visible-watchlist
				. $this->msg( "articlefeedbackv5-special-filter-$msg_key-watchlist" )->escaped()

				. Html::closeElement( 'a' );
		}

		$filterSelectHtml = '';
		// No dropdown for readers
		if ( $this->showFeatured ) {
			$opts = array();
			$foundNonDefaults = false;
			foreach ( $this->filters as $filter ) {
				$msg_key = str_replace(array('all-', 'visible-', 'notdeleted-'), '', $filter);

				$key   = $this->msg( "articlefeedbackv5-special-filter-$msg_key-watchlist" )->escaped();
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
	 * Don't display totals for watchlist feedback
	 *
	 * @return array the counts, as filter => count
	 */
	protected function getFilterCounts() {
		return array();
	}
}
