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
require_once dirname( dirname( __FILE__ ) ) . '/ArticleFeedbackv5/AllTests.php';

/**
 * ArticleFeedbackv5_AllTests
 */
class ArticleFeedbackv5_AllTests extends AllTests
{

	/**
	 * Run test suites
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite( 'ArticleFeedbackv5_AllTests ');


		/**
		 * AliasTestCase
		 */
		require_once 'AliasTestCase.php';

		$suite->addTestSuite( 'ArticleFeedbackv5_AliasTestCase' );

		/**
		 * I18nTestCase
		 */
		require_once 'I18nTestCase.php';

		$suite->addTestSuite( 'ArticleFeedbackv5_I18nTestCase' );

		/**
		 * SetupTestCase
		 */
		require_once 'SetupTestCase.php';

		$suite->addTestSuite( 'ArticleFeedbackv5_SetupTestCase' );

		/**
		 * ArticleFeedbackv5_Flagging_AllTests
		 */
		require_once dirname( __FILE__ ) . '/Flagging/AllTests.php';

		// This is the class name of a test suite
		$suite->addTestSuite( 'ArticleFeedbackv5_Flagging_AllTests' );

		/**
		 * ArticleFeedbackv5_Fetch_AllTests
		 */
		require_once dirname( __FILE__ ) . '/Fetch/AllTests.php';

		// This is the class name of a test suite
		$suite->addTestSuite( 'ArticleFeedbackv5_Fetch_AllTests' );
		
		if ( AllTests::getSeleniumIsRunning() ) {

			/**
			 * VerifySeleniumTestCase
			 */
			require_once 'VerifySeleniumTestCase.php';

			$suite->addTestSuite( 'ArticleFeedbackv5_VerifySeleniumTestCase' );
		}

		return $suite;
	}
}

