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
require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/ArticleFeedbackv5/AllTests.php';

/**
 * ArticleFeedbackv5_AllTests
 */
class ArticleFeedbackv5_Flagging_AllTests extends AllTests
{

	/**
	 * Run test suites
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite( 'ArticleFeedbackv5_Flagging_AllTests ');

		/**
		 * Constructor
		 */
		require_once 'ConstructorTestCase.php';

		$suite->addTestSuite( 'ArticleFeedbackv5_Flagging_ConstructorTestCase' );

		return $suite;
	}
}

