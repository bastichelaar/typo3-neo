<?php
namespace TYPO3\Aloha\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Aloha".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 *
 * @api
 * @Flow\Scope("prototype")
 */
class BaseUriViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
	 */
	protected $resourcePublisher;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject the Flow resource publisher.
	 *
	 * @param \TYPO3\Flow\Resource\Publishing\ResourcePublisher $resourcePublisher
	 * @return void
	 */
	public function injectResourcePublisher(\TYPO3\Flow\Resource\Publishing\ResourcePublisher $resourcePublisher) {
		$this->resourcePublisher = $resourcePublisher;
	}

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	public function render() {
		if ($this->settings['alohaDevelopmentMode'] === TRUE) {
			// TODO: throw exception if Aloha-dev folder is not available
			return $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'Packages/TYPO3.Aloha/Aloha-dev';
		} else {
			return $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'Packages/TYPO3.Aloha/Aloha';
		}
	}
}

?>