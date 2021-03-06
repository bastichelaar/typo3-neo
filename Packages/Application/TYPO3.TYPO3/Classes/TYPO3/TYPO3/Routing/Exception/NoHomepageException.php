<?php
namespace TYPO3\TYPO3\Routing\Exception;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.TYPO3".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * A "no homepage" exception
 */
class NoHomepageException extends \TYPO3\TYPO3\Routing\Exception {

	protected $statusCode = 500;

}

?>