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
 * ArticleFeedbackv5_Fetch_PageIdTestCase
 *
 * Testing ArticleFeedbackv5Fetch
 */
class ArticleFeedbackv5_Fetch_PageIdTestCase extends ArticleFeedbackv5TestCase
{

	/**
	 * testSetPageIdWithAnInvalidPageIdAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithAnInvalidPageIdAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = 'This page id is invalid.';
		$this->assertFalse( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithANullPageIdAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithANullPageIdAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = null;
		$this->assertFalse( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithABooleanFalsePageIdAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithABooleanFalsePageIdAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = false;
		$this->assertFalse( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithABooleanTruePageIdAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithABooleanTruePageIdAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getFeedbackId() );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = true;
		$this->assertFalse( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithAnIntegerValueOf1PageIdAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithAnIntegerValueOfOnePageIdAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = 1;
		$this->assertTrue( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertSame( $value, $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithAnIntegerValueOf1PageIdAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithAnStringValueOfOnePageIdAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = '1';
		$this->assertTrue( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertSame( (integer) $value, $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithAnIntegerValueOf1PageIdAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithAnIntegerValueOfZeroPageIdAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = 0;
		$this->assertTrue( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertSame( $value, $articleFeedbackv5Fetch->getPageId() );
	}

	/**
	 * testSetPageIdWithAnIntegerValueOf1PageIdAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getPageId
	 * @covers ArticleFeedbackv5Fetch::setPageId
	 */
	public function testSetPageIdWithAnStringValueOfZeroPageIdAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertNull( $articleFeedbackv5Fetch->getPageId() );

		$value = '0';
		$this->assertTrue( $articleFeedbackv5Fetch->setPageId( $value ) );
		$this->assertSame( (integer) $value, $articleFeedbackv5Fetch->getPageId() );
	}
}

