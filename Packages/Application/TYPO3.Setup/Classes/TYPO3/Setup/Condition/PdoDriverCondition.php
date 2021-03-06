<?php
namespace TYPO3\Setup\Condition;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Setup".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Condition that checks if the PDO driver is loaded and if there are available drivers
 */
class PdoDriverCondition extends AbstractCondition {

	/**
	 * Returns TRUE if the condition is satisfied, otherwise FALSE
	 *
	 * @return boolean
	 */
	public function isMet() {
		if (defined('PDO::ATTR_DRIVER_NAME') === FALSE || \PDO::getAvailableDrivers() === array()) {
			return FALSE;
		}
		return TRUE;
	}

}
?>