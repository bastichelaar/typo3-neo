<?php
namespace TYPO3\TYPO3\ViewHelpers\Backend;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.TYPO3".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * ViewHelper for the backend 'container'. Renders the required HTML to integrate
 * the Phoenix backend into a website.
 */
class JavascriptConfigurationViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\ContentTypeManager
	 */
	protected $contentTypeManager;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 *
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @return string
	 */
	public function render() {
		return (implode("\n", array(
			'window.T3Configuration = {};',
			'window.T3Configuration.Schema = ' . json_encode($this->contentTypeManager->getFullConfiguration()) . ';',
			'window.T3Configuration.UserInterface = ' . json_encode($this->settings['userInterface']) . ';',
			'window.T3Configuration.phoenixShouldCacheSchema = ' . json_encode($this->bootstrap->getContext()->isProduction()) . ';',
			'window.T3Configuration.enableAloha = ' . json_encode($this->settings['enableAloha']) . ';',
			'window.T3Configuration.contentTypeGroups = ' . json_encode($this->settings['contentTypeGroups']) . ';'
		)));
	}
}
?>