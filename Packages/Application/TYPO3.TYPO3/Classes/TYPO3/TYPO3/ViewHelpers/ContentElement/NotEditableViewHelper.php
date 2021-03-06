<?php
namespace TYPO3\TYPO3\ViewHelpers\ContentElement;

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
 * Renders a wrapper around the inner contents of the tag to enable frontend editing.
 * The wrapper contains the property name which should be made editable, and is either a "span" or a "div" tag (depending on the context)
 *
 * @Flow\Scope("prototype")
 */
class NotEditableViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper {

	/**
	 * @var \TYPO3\Flow\Security\Authorization\AccessDecisionManagerInterface
	 */
	protected $accessDecisionManager;

	/**
	 * Injects the access decision manager
	 *
	 * @param \TYPO3\Flow\Security\Authorization\AccessDecisionManagerInterface $accessDecisionManager The access decision manager
	 * @return void
	 */
	public function injectAccessDecisionManager(\TYPO3\Flow\Security\Authorization\AccessDecisionManagerInterface $accessDecisionManager) {
		$this->accessDecisionManager = $accessDecisionManager;
	}

	/**
	 * @return string
	 */
	public function render() {
		if ($this->hasAccessToResource('TYPO3_TYPO3_Backend_BackendController')) {
			$this->tag->setContent($this->renderChildren());
			$this->tag->addAttribute('class', 'typo3-typo3-notEditable');
			return $this->tag->render();
		} else {
			return $this->renderChildren();
		}
	}

	/**
	 * Check if we currently have access to the given resource
	 *
	 * @param string $resource The resource to check
	 * @return boolean TRUE if we currently have access to the given resource
	 */
	protected function hasAccessToResource($resource) {
		try {
			$this->accessDecisionManager->decideOnResource($resource);
		} catch (\TYPO3\Flow\Security\Exception\AccessDeniedException $e) {
			return FALSE;
		}

		return TRUE;
	}
}
?>