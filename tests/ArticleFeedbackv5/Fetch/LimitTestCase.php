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
 * ArticleFeedbackv5_Fetch_LimitTestCase
 *
 * Testing ArticleFeedbackv5Fetch
 */
class ArticleFeedbackv5_Fetch_LimitTestCase extends ArticleFeedbackv5TestCase
{

	/**
	 * @see ArticleFeedbackv5Fetch::$limit
	 *
	 * @var integer $defaultLimitValue The default limit value
	 */
	private $defaultLimitValue = 25;

	/**
	 * testSetLimitWithAnInvalidLimitAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithAnInvalidLimitAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = 'This limit is invalid.';
		$this->assertFalse( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithANullLimitAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithANullLimitAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = null;
		$this->assertFalse( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithABooleanFalseLimitAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithABooleanFalseLimitAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = false;
		$this->assertFalse( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithABooleanTrueLimitAndReturnFalse
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithABooleanTrueLimitAndReturnFalse() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = true;
		$this->assertFalse( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithAnIntegerValueOf1LimitAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithAnIntegerValueOfOneLimitAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = 1;
		$this->assertTrue( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( $value, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithAnIntegerValueOf1LimitAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithAnStringValueOfOneLimitAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = '1';
		$this->assertTrue( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( (integer) $value, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithAnIntegerValueOf1LimitAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithAnIntegerValueOfZeroLimitAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = 0;
		$this->assertTrue( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( $value, $articleFeedbackv5Fetch->getLimit() );
	}

	/**
	 * testSetLimitWithAnIntegerValueOf1LimitAndReturnTrue
	 *
	 * @covers ArticleFeedbackv5Fetch::__construct
	 * @covers ArticleFeedbackv5Fetch::getLimit
	 * @covers ArticleFeedbackv5Fetch::setLimit
	 */
	public function testSetLimitWithAnStringValueOfZeroLimitAndReturnTrue() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.fetch.php'; 
		$this->assertFileExists( $file );

		include_once $file;
		
		$articleFeedbackv5Fetch = new ArticleFeedbackv5Fetch();

		$this->assertInstanceOf( 'ArticleFeedbackv5Fetch', $articleFeedbackv5Fetch );

		$this->assertSame( $this->defaultLimitValue, $articleFeedbackv5Fetch->getLimit() );

		$value = '0';
		$this->assertTrue( $articleFeedbackv5Fetch->setLimit( $value ) );
		$this->assertSame( (integer) $value, $articleFeedbackv5Fetch->getLimit() );
	}
}

