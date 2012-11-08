<?php
namespace TYPO3\Form\Exception;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Form".            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This exception is thrown if the form definition would get an inconsistent state, like:
 *
 * - adding a page to two different forms
 *
 *
 * @api
 */
class FormDefinitionConsistencyException extends \TYPO3\Form\Exception {
}
?>