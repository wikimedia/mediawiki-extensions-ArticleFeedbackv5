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
	 * The user we're operating on (null for no watchlist)
	 *
	 * @var User
	 */
	protected $user;

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

		if ( $user->isAnon() ) {
			$out->redirect(SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getFullUrl());
		} else {
			$this->user = $user;
		}

		parent::execute( $param );

		$out->setPagetitle( $this->msg( 'articlefeedbackv5-special-watchlist-pagetitle' )->escaped() );
	}

	/**
	 * @return array
	 */
	protected function fetchData() {
		return ArticleFeedbackv5Model::getWatchlistList(
			$this->startingFilter,
			$this->user,
			$this->startingOffset,
			$this->startingSort,
			$this->startingSortDirection
		);
	}

	/**
	 * Display the feedback page's summary information in header
	 *
	 * @return string
	 */
	protected function buildSummary() {
		$user = $this->getUser();

		// Showing {count} posts
		return
			Html::rawElement(
				'div',
				array( 'id' => 'articleFeedbackv5-special-watchlist-showing-wrap' ),
				$this->msg( 'articlefeedbackv5-special-watchlist-showing',
					$user->getUserPage()->getFullText(),
					$user->getName()
				) .
					Html::rawElement(
						'span',
						array( 'id' => 'articlefeedbackv5-special-central-watchlist-link' ),
						$this->msg( 'articlefeedbackv5-special-watchlist-central-link',
							SpecialPage::getTitleFor( 'ArticleFeedbackv5' )->getFullText()
						)->parse()
					)
			);
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
		foreach ( array( 'visible-relevant', 'visible' ) as $filter ) {
			$msg_key = str_replace( array( 'all-', 'visible-', 'notdeleted-' ), '', $filter );

			$filterLabels[$filter] =
				Html::rawElement(
					'a',
					array(
						'href' => '#',
						'id' => "articleFeedbackv5-special-filter-$filter",
						'class' => 'articleFeedbackv5-filter-link' . ( $this->startingFilter == $filter ? ' filter-active' : '' )
					),
					$this->msg( "articlefeedbackv5-special-filter-$msg_key-watchlist" )->escaped()
				);
		}

		// filters to be displayed in dropdown (only for editors)
		$filterSelectHtml = '';
		if ( $this->isAllowed( 'aft-editor' ) ) {
			$opts = array();

			foreach ( $this->filters as $filter ) {
				if ( in_array( $filter, array_keys( $filterLabels ) ) ) {
					continue;
				}

				$msg_key = str_replace( array( 'all-', 'visible-', 'notdeleted-' ), '', $filter );

				$key = $this->msg( "articlefeedbackv5-special-filter-$msg_key-watchlist" )->escaped();
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
}
