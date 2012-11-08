<?php
namespace TYPO3\TYPO3\Tests\Functional\TypoScript;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3CR".               *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * Functional test case which tests the rendering
 *
 * @group large
 */
class RenderingTest extends \TYPO3\Flow\Tests\FunctionalTestCase {

	/**
	 * @var boolean
	 */
	static protected $testablePersistenceEnabled = TRUE;

	/**
	 * @var boolean
	 */
	protected $testableHttpEnabled = TRUE;

	/**
	 * @var boolean
	 */
	protected $testableSecurityEnabled = TRUE;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\NodeInterface
	 */
	protected $node;

	public function setUp() {
		parent::setUp();
		$nodeRepository = $this->objectManager->get('TYPO3\TYPO3CR\Domain\Repository\NodeRepository');
		\TYPO3\Flow\Reflection\ObjectAccess::setProperty($nodeRepository, 'context', new \TYPO3\TYPO3\Domain\Service\ContentContext('live'), TRUE);
		$siteImportService = $this->objectManager->get('TYPO3\TYPO3\Domain\Service\SiteImportService');
		$siteImportService->importSitesFromFile(__DIR__ . '/Fixtures/NodeStructure.xml');
		$this->persistenceManager->persistAll();

		$propertyMapper = $this->objectManager->get('TYPO3\Flow\Property\PropertyMapper');
		$this->node = $propertyMapper->convert('/sites/example/home', 'TYPO3\TYPO3CR\Domain\Model\Node');
		$this->assertFalse($propertyMapper->getMessages()->hasErrors());
	}

	/**
	 * @test
	 */
	public function basicRenderingWorks() {
		$output = $this->simulateRendering();

		$this->assertTeaserConformsToBasicRendering($output);
		$this->assertMainContentConformsToBasicRendering($output);
		$this->assertSidebarConformsToBasicRendering($output);
	}

	/**
	 * @test
	 */
	public function debugModeSettingWorks() {
		$output = $this->simulateRendering(NULL, TRUE);
		$this->assertContains('<!-- Beginning to render TS path', $output);

		$output = $this->simulateRendering();
		$this->assertNotContains('<!-- Beginning to render TS path', $output);
	}

	/**
	 * @test
	 */
	public function overriddenValueInPrototype() {
		$output = $this->simulateRendering('Test_OverriddenValueInPrototype.ts2');

		$this->assertTeaserConformsToBasicRendering($output);
		$this->assertMainContentConformsToBasicRendering($output);

		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-headline > div', 'Static Headline', TRUE, $output);
		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-text > div', 'Below, you\'ll see the most recent activity', TRUE, $output);
		$this->assertSelectEquals('.sidebar', '[COMMIT WIDGET]', TRUE, $output);
	}

	/**
	 * @test
	 */
	public function additionalProcessorInPrototype() {
		$output = $this->simulateRendering('Test_AdditionalProcessorInPrototype.ts2');

		$this->assertTeaserConformsToBasicRendering($output);
		$this->assertMainContentConformsToBasicRendering($output);

		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-headline > div > .processor-wrap', 'BEFOREStatic HeadlineAFTER', TRUE, $output);
	}

	/**
	 * @test
	 */
	public function additionalProcessorInPrototype2() {
		$output = $this->simulateRendering('Test_AdditionalProcessorInPrototype2.ts2');

		$this->assertSelectEquals('.teaser > .typo3-phoenix-contenttypes-headline > div > header > h1', 'Welcome to this example', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left > .typo3-phoenix-contenttypes-headline > div > header > h1', 'Documentation', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .center > .typo3-phoenix-contenttypes-headline > div > header > h1', 'Development Process', TRUE, $output);

		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-headline > div > header > .processor-wrap', 'BEFOREStatic HeadlineAFTER', TRUE, $output);
	}

	/**
	 * @test
	 */
	public function replaceElementRenderingCompletelyInSidebar() {
		$output = $this->simulateRendering('Test_ReplaceElementRenderingCompletelyInSidebar.ts2');
		$this->assertTeaserConformsToBasicRendering($output);
		$this->assertMainContentConformsToBasicRendering($output);

			// header is now wrapped in h3
		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-headline > header > h3', 'Last Commits', TRUE, $output);
		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-text > div', 'Below, you\'ll see the most recent activity', TRUE, $output);
	}

	/**
	 * @test
	 */
	public function replaceElementRenderingCompletelyBasedOnAdvancedCondition() {
		$output = $this->simulateRendering('Test_ReplaceElementRenderingCompletelyBasedOnAdvancedCondition.ts2');
		$this->assertTeaserConformsToBasicRendering($output);
		$this->assertSidebarConformsToBasicRendering($output);

		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left > .typo3-phoenix-contenttypes-headline > div > header', 'DOCS: Documentation', TRUE, $output);
	}

	/**
	 * @test
	 */
	public function overriddenValueInNestedPrototype() {
		$output = $this->simulateRendering('Test_OverriddenValueInNestedPrototype.ts2');
		$this->assertTeaserConformsToBasicRendering($output);

		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left > .typo3-phoenix-contenttypes-headline > div > header', 'Static Headline', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .center > .typo3-phoenix-contenttypes-headline > div > header', 'Static Headline', TRUE, $output);

		$this->assertSidebarConformsToBasicRendering($output);
	}

	/**
	 * @test
	 */
	public function overriddenValueInNestedPrototype2() {
		$output = $this->simulateRendering('Test_OverriddenValueInNestedPrototype2.ts2');
		$this->assertTeaserConformsToBasicRendering($output);

		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left > .typo3-phoenix-contenttypes-headline > div > header', 'Static Headline', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .center > .typo3-phoenix-contenttypes-headline > div > h1', 'Development Process', TRUE, $output);

		$this->assertSidebarConformsToBasicRendering($output);
	}

	/**
	 * Helper function for setting assertions
	 * @param string $output
	 */
	protected function assertTeaserConformsToBasicRendering($output) {
		$this->assertContains('TYPO3 Phoenix is based on Flow, a powerful PHP application framework licensed under the GNU/LGPL.', $output);
		$this->assertSelectEquals('h1', 'Home', TRUE, $output);

		$this->assertSelectEquals('.teaser > .typo3-phoenix-contenttypes-headline > div > h1', 'Welcome to this example', TRUE, $output);
		$this->assertSelectEquals('.teaser > .typo3-phoenix-contenttypes-text > div', 'This is our exemplary rendering test.', TRUE, $output);
	}

	/**
	 * Helper function for setting assertions
	 * @param string $output
	 */
	protected function assertMainContentConformsToBasicRendering($output) {
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-headline > div > h1', 'Do you love Flow?', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-text > div', 'If you do, make sure to post your opinion about it on Twitter!', TRUE, $output);

		$this->assertSelectEquals('.main', '[TWITTER WIDGET]', TRUE, $output);

		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left > .typo3-phoenix-contenttypes-headline > div > h1', 'Documentation', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left > .typo3-phoenix-contenttypes-text > div', 'We\'re still improving our docs, but check them out nevertheless!', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .left', '[SLIDESHARE]', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .center > .typo3-phoenix-contenttypes-headline > div > h1', 'Development Process', TRUE, $output);
		$this->assertSelectEquals('.main > .typo3-phoenix-contenttypes-threecolumn > .center > .typo3-phoenix-contenttypes-text > div', 'We\'re spending lots of thought into our infrastructure, you can profit from that, too!', TRUE, $output);
	}

	/**
	 * Helper function for setting assertions
	 * @param string $output
	 */
	protected function assertSidebarConformsToBasicRendering($output) {
		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-headline > div > h1', 'Last Commits', TRUE, $output);
		$this->assertSelectEquals('.sidebar > .typo3-phoenix-contenttypes-text > div', 'Below, you\'ll see the most recent activity', TRUE, $output);
		$this->assertSelectEquals('.sidebar', '[COMMIT WIDGET]', TRUE, $output);
	}

	/**
	 * Helper function for setting assertions
	 * @static
	 * @param array $selector
	 * @param string $content
	 * @param integer $count
	 * @param mixed $actual
	 * @param string $message
	 * @param boolean $isHtml
	 */
	public static function assertSelectEquals($selector, $content, $count, $actual, $message = '', $isHtml = TRUE) {
		if ($message === '') {
			$message = $selector . ' did not match.';
		}
		parent::assertSelectEquals($selector, $content, $count, $actual, $message, $isHtml);
	}

	/**
	 * Simulate rendering
	 * @param string $additionalTypoScriptFile
	 * @param boolean $debugMode
	 * @return string
	 */
	protected function simulateRendering($additionalTypoScriptFile = NULL, $debugMode = FALSE) {
		$typoScriptRuntime = $this->parseTypoScript($additionalTypoScriptFile);
		if ($debugMode) {
			$typoScriptRuntime->injectSettings(array('debugMode' => TRUE, 'catchRuntimeExceptions' => FALSE));
		}
		$typoScriptRuntime->pushContextArray(array('node' => $this->node));
		$output = $typoScriptRuntime->render('page1');
		$typoScriptRuntime->popContext();

		return $output;
	}

	/**
	 * Parse TypoScript
	 * @param string $additionalTypoScriptFile
	 * @return \TYPO3\TypoScript\Core\Runtime
	 */
	protected function parseTypoScript($additionalTypoScriptFile = NULL) {
		$typoScript = file_get_contents(__DIR__ . '/Fixtures/PredefinedTypoScript.ts2');
		$typoScript .= chr(10) . chr(10) . file_get_contents(__DIR__ . '/Fixtures/BaseTypoScript.ts2');

		$fixtureDirectory = \TYPO3\Flow\Utility\Files::concatenatePaths(array(__DIR__, 'Fixtures'));

		if ($additionalTypoScriptFile !== NULL) {
			$typoScript .= chr(10) . chr(10) . file_get_contents(\TYPO3\Flow\Utility\Files::concatenatePaths(array($fixtureDirectory, $additionalTypoScriptFile)));
		}
		$typoScript = str_replace('FIXTURE_DIRECTORY', $fixtureDirectory, $typoScript);

		$parser = new \TYPO3\TypoScript\Core\Parser();
		$typoScriptConfiguration = $parser->parse($typoScript);

		$httpRequest = \TYPO3\Flow\Http\Request::create(new \TYPO3\Flow\Http\Uri('http://foo.bar/bazfoo'));
		$request = $httpRequest->createActionRequest();
		$response = new \TYPO3\Flow\Http\Response();

		$controllerContext = new \TYPO3\Flow\Mvc\Controller\ControllerContext(
			$request,
			$response,
			$this->getMock('TYPO3\Flow\Mvc\Controller\Arguments', array(), array(), '', FALSE),
			$this->getMock('TYPO3\Flow\Mvc\Routing\UriBuilder'),
			$this->getMock('TYPO3\Flow\Mvc\FlashMessageContainer')
		);
		return new \TYPO3\TypoScript\Core\Runtime($typoScriptConfiguration, $controllerContext);
	}
}
?>