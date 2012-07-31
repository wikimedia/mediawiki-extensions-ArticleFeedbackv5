<?php
/**
 * Wikimedia Foundation
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author Jeremy Postlethwaite <jpostlethwaite@wikimedia.org>
 */

/**
 * @see ArticleFeedbackv5TestCase
 */
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/ArticleFeedbackv5TestCase.php';

/**
 * ArticleFeedbackv5_Fetch_ConstructorTestCase
 *
 * Testing ArticleFeedbackv5Fetch
 */
class ArticleFeedbackv5_Fetch_ConstructorTestCase extends ArticleFeedbackv5TestCase
{

	/**
	 * testConstructorWithNoParameters
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getFeedbackId
	 * @covers ArticleFeedbackv5Fetch::getFilter
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::getSort
	 * @covers ArticleFeedbackv5Fetch::getSortOrder
	 */
	public function testConstructorWithNoParameters() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getFeedbackId() );

		$this->assertSame( 'visible', $articleFeedbackv5Fetch->getFilter() );

		$this->assertSame( 25, $articleFeedbackv5Fetch->getLimit() );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$this->assertSame( 'age', $articleFeedbackv5Fetch->getSort() );

		$this->assertSame( 'desc', $articleFeedbackv5Fetch->getSortOrder() );
	}

	/**
	 * testConstructorWithNoParameters
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getFeedbackId
	 * @covers ArticleFeedbackv5Fetch::getFilter
	 * @covers ArticleFeedbackv5Fetch::setFilter
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 * @covers ArticleFeedbackv5Fetch::getSort
	 * @covers ArticleFeedbackv5Fetch::getSortOrder
	 */
	public function testConstructorWithAllParameters() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$filter = 'id';
		$filterValue = 1;
		$pageId = 3;
		global $wgArticleFeedbackv5InitialFeedbackPostCountToDisplay;

		$limit = 10;
		$wgArticleFeedbackv5InitialFeedbackPostCountToDisplay = $limit;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch( $filter, $filterValue, $pageId );

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $filterValue, $articleFeedbackv5Fetch->getFeedbackId() );

		$this->assertSame( $filter, $articleFeedbackv5Fetch->getFilter() );

		$this->assertSame( $limit, $articleFeedbackv5Fetch->getLimit() );

		$this->assertSame( $pageId, $articleFeedbackv5Fetch->getPageId() );

		$this->assertSame( 'age', $articleFeedbackv5Fetch->getSort() );

		$this->assertSame( 'desc', $articleFeedbackv5Fetch->getSortOrder() );
	}
}

