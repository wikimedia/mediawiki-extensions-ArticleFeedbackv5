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
require_once dirname( dirname( __FILE__ ) ) . '/ArticleFeedbackv5TestCase.php';

/**
 * ArticleFeedbackv5_I18nTestCase
 *
 * Testing $messages
 */
class ArticleFeedbackv5_I18nTestCase extends ArticleFeedbackv5TestCase
{

	/**
	 * testMessagesIsDefined
	 */
	public function testMessagesIsDefined() {

		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.i18n.php';
		$this->assertFileExists( $file );

		include $file;

		$this->assertArrayHasKey( 'en', $messages );
	}

	/**
	 * testMessagesHasAllEnglishDocumentation
	 */
	public function testMessagesHasAllEnglishDocumentation() {

		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.i18n.php';
		$this->assertFileExists( $file );

		include $file;

		foreach ( array_keys( $messages['en'] ) as $key ) {
			$this->assertArrayHasKey( $key, $messages['qqq'] );
		}
	}

	/**
	 * testMessagesDocumentationHasAllEnglish
	 */
	public function testMessagesDocumentationHasAllEnglish() {

		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.i18n.php';
		$this->assertFileExists( $file );

		include $file;

		foreach ( array_keys( $messages['qqq'] ) as $key ) {
			$this->assertArrayHasKey( $key, $messages['en'] );
		}
	}
}
