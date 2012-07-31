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
 * ArticleFeedbackv5_AliasTestCase
 *
 * Testing ArticleFeedbackv5Flagging
 */
class ArticleFeedbackv5_Flagging_ConstructorTestCase extends ArticleFeedbackv5TestCase
{

	/**
	 * testConstructor
	 *
	 * @covers ArticleFeedbackv5Flagging::__construct
	 * @covers ArticleFeedbackv5Flagging::getUserId
	 */
	public function testConstructor() {
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.flagging.php'; 
		$this->assertFileExists( $file );

		include $file;
		
		$user = 0;

		$pageId = null;

		$feedbackId = null;

		$articleFeedbackv5Flagging = new ArticleFeedbackv5Flagging( $user, $pageId, $feedbackId );

		$this->assertInstanceOf( 'ArticleFeedbackv5Flagging', $articleFeedbackv5Flagging );

		$this->assertSame( $user, $articleFeedbackv5Flagging->getUserId() );
	}
}

