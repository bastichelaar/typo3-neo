<?php
namespace TYPO3\TYPO3\ViewHelpers;

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
 * ViewHelper which wraps all content elements, and adds an additional div wrapper
 * if we are in backend mode.
 */
class ContentElementViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3\Service\ContentElementWrappingService
	 */
	protected $contentElementWrappingService;

	/**
	 * Include all JavaScript files matching the include regular expression
	 * and not matching the exclude regular expression.
	 *
	 * @param \TYPO3\TYPO3CR\Domain\Model\NodeInterface $node
	 * @param boolean $page
	 * @return string
	 */
	public function render(\TYPO3\TYPO3CR\Domain\Model\NodeInterface $node, $page = FALSE) {
		return $this->contentElementWrappingService->wrapContentObject($node, $this->templateVariableContainer->get('fluidTemplateTsObject')->getPath(), $this->renderChildren(), $page);
	}
}
?>