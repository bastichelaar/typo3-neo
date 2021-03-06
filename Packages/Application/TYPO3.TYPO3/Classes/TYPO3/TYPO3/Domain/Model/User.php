<?php
namespace TYPO3\TYPO3\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.TYPO3".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use \TYPO3\Party\Domain\Model\Person;

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * Domain Model of a User
 *
 * @Flow\Entity
 * @Flow\Scope("prototype")
 */
class User extends Person  {

	/**
	 * Preferences of this user
	 *
	 * @var \TYPO3\TYPO3\Domain\Model\UserPreferences
	 * @ORM\OneToOne
	 */
	protected $preferences;

	/**
	 * Constructs this User object
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->preferences = new UserPreferences();
	}

	/**
	 * @return \TYPO3\TYPO3\Domain\Model\UserPreferences
	 */
	public function getPreferences() {
		return $this->preferences;
	}

}
?>