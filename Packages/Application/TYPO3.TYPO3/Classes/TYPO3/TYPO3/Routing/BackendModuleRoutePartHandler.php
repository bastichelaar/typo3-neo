<?php
namespace TYPO3\TYPO3\Routing;

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
 * A route part handler for finding nodes specifically in the website's frontend.
 *
 * @Flow\Scope("singleton")
 */
class BackendModuleRoutePartHandler extends \TYPO3\Flow\Mvc\Routing\DynamicRoutePart {

	const MATCHRESULT_FOUND = TRUE;
	const MATCHRESULT_NOSUCHMODULE = -1;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Iterate through the segments of the current request path
	 * find the corresponding module configuration and set controller & action
	 * accordingly
	 *
	 * @param string $value
	 * @return boolean|integer
	 */
	protected function matchValue($value) {
		$segments = explode('/', $value);
		$rootModule = array_shift($segments);
		if (!isset($this->settings['modules'][$rootModule])) {
			return self::MATCHRESULT_NOSUCHMODULE;
		}
		$moduleConfiguration = $this->settings['modules'][$rootModule];
		$moduleController = isset($moduleConfiguration['controller']) ? $moduleConfiguration['controller'] : '\TYPO3\TYPO3\Controller\Module\StandardController';
		$modulePath = array($rootModule);
		$level = 1;
		if (is_array($segments)) {
			foreach ($segments as $segment) {
				if (isset($moduleConfiguration['submodules'][$segment])) {
					array_push($modulePath, $segment);
					$moduleConfiguration = $moduleConfiguration['submodules'][$segment];
					$moduleController = isset($moduleConfiguration['controller']) ? $moduleConfiguration['controller'] : '\TYPO3\TYPO3\Controller\Module\StandardController';
				} else {
					if ($level === count($segments)) {
						$moduleMethods = array_change_key_case(array_flip(get_class_methods($moduleController)), CASE_LOWER);
						if (array_key_exists($segment . 'action', $moduleMethods) === TRUE) {
							$moduleAction = $segment;
							break;
						}
					}
					return self::MATCHRESULT_NOSUCHMODULE;
				}
				$level++;
			}
		}

		$this->value = array(
			'module' => implode('/', $modulePath),
			'controller' => $moduleController,
			'action' => isset($moduleAction) ? $moduleAction : 'index'
		);

		return self::MATCHRESULT_FOUND;
	}

	/**
	 * @param string $requestPath
	 * @return string
	 */
	protected function findValueToMatch($requestPath) {
		return $requestPath;
	}

	/**
	 * Iterate through the configured modules, find the matching module and set
	 * the route path accordingly
	 *
	 * @param array $value (contains action, controller and package of the module controller)
	 * @return boolean
	 */
	protected function resolveValue($value) {
		if (is_array($value)) {
			$this->value = $value['module'];
		} else {
			$this->value = $value;
		}
		return TRUE;
	}

}
?>