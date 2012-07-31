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
 * ArticleFeedbackv5_SetupTestCase
 *
 * Test the setup file: ArticleFeedbackv5.php
 */
class ArticleFeedbackv5_SetupTestCase extends ArticleFeedbackv5TestCase
{

	/**
	 * testExtensionCredits
	 */
	public function testExtensionCredits() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;
		
		$this->assertArrayHasKey( 'other', $wgExtensionCredits );
	}

	/**
	 * testExtensionCreditsHasPath
	 */
	public function testExtensionCreditsHasPath() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;

		$other = current( $wgExtensionCredits['other'] );

		$this->assertArrayHasKey( 'path', $other );
	}

	/**
	 * testExtensionCreditsHasName
	 */
	public function testExtensionCreditsHasName() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;

		$other = current( $wgExtensionCredits['other'] );

		$this->assertArrayHasKey( 'name', $other );

		$this->assertSame( 'Article Feedback', $other['name'] );
	}

	/**
	 * testExtensionCreditsHasVersion
	 */
	public function testExtensionCreditsHasVersion() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;

		$other = current( $wgExtensionCredits['other'] );

		$this->assertArrayHasKey( 'version', $other );

		$this->assertSame( 'unknown', $other['version'] );
	}

	/**
	 * testExtensionCreditsHasUrl
	 */
	public function testExtensionCreditsHasUrl() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;

		$other = current( $wgExtensionCredits['other'] );

		$this->assertArrayHasKey( 'url', $other );

		$url = '//www.mediawiki.org/wiki/Extension:ArticleFeedbackv5';
		$this->assertSame( $url, $other['url'] );
	}

	/**
	 * testExtensionCreditsHasAuthor
	 */
	public function testExtensionCreditsHasAuthor() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;

		$other = current( $wgExtensionCredits['other'] );

		$this->assertArrayHasKey( 'author', $other );
	}

	/**
	 * testExtensionCreditsHasDescriptionmsg
	 */
	public function testExtensionCreditsHasDescriptionmsg() {

		global $wgContentNamespaces;
		
		$file = TESTS_EXTENSION_ROOT . '/ArticleFeedbackv5/ArticleFeedbackv5.php'; 
		$this->assertFileExists( $file );

		include $file;

		$other = current( $wgExtensionCredits['other'] );

		$this->assertArrayHasKey( 'descriptionmsg', $other );

		$this->assertSame( 'articlefeedbackv5-desc', $other['descriptionmsg'] );
	}
}
