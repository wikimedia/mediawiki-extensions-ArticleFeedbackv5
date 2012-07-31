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
 * @author		Jeremy Postlethwaite <jpostlethwaite@wikimedia.org>
 */

/**
 * AllTestsAbstract
 */
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/UnitTest/AllTestsAbstract.php';

/**
 * ArticleFeedbackv5 Test configuration
 */
$testConfiguration = dirname( __FILE__ ) . '/TestConfiguration.defaults.php';

if ( is_file( $testConfiguration ) ) {
	require_once $testConfiguration;
}

/**
 * AllTests
 */
class AllTests extends AllTestsAbstract
{

	/**
	 * Run test suites
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite()
	{
		parent::setUp();

		$suite = new PHPUnit_Framework_TestSuite( 'AllTests Suite' );

		/**
		 * ArticleFeedbackv5_AllTests
		 */
		require_once dirname( __FILE__ ) . '/ArticleFeedbackv5/AllTests.php';

		// This is the class name of a test suite
		$suite->addTestSuite( 'ArticleFeedbackv5_AllTests' );

		return $suite;
	}
}

