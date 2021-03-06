<?php
namespace TYPO3\TypoScript\TypoScriptObjects;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TypoScript".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Matcher object for use inside a "Case" statement
 */
class MatcherImplementation extends AbstractTypoScriptObject {

	/**
	 * @var boolean
	 */
	protected $condition;

	/**
	 * The type to render if $condition is TRUE
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * @param boolean $condition
	 */
	public function setCondition($condition) {
		$this->condition = $condition;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * If $condition matches, render $type and return it. Else, return MATCH_NORESULT.
	 *
	 * @return mixed
	 */
	public function evaluate() {
		if ($this->tsValue('condition')) {
			$type = $this->tsValue('type');
			$renderedElement = $this->tsRuntime->render(
				sprintf('%s/element<%s>', $this->path, $type)
			);
			return $this->tsRuntime->evaluateProcessor('element', $this, $renderedElement);
		} else {
			return CaseImplementation::MATCH_NORESULT;
		}
	}
}
?>