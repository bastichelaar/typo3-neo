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

use \TYPO3\Flow\Error\Error;
use \TYPO3\TYPO3\Domain\Service\ContentContext;
use \TYPO3\TYPO3CR\Domain\Model\NodeInterface;

use TYPO3\Flow\Annotations as Flow;

/**
 * An Object Converter for Nodes which can be used for routing (but also for other
 * purposes) as a plugin for the Property Mapper.
 *
 * @Flow\Scope("singleton")
 */
class NodeObjectConverter extends \TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter {

	/**
	 * @var boolean
	 */
	const REMOVED_CONTENT_SHOWN = 1;

	/**
	 * @var array
	 */
	protected $sourceTypes = array('string', 'array');

	/**
	 * @var string
	 */
	protected $targetType = 'TYPO3\TYPO3CR\Domain\Model\NodeInterface';

	/**
	 * @var integer
	 */
	protected $priority = 1;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3\Domain\Repository\SiteRepository
	 */
	protected $siteRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Security\Context
	 */
	protected $securityContext;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Property\PropertyMapper
	 */
	protected $propertyMapper;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeRepository
	 */
	protected $nodeRepository;

	/**
	 * Converts the specified node path into a Node.
	 *
	 * The node path must be an absolute context node path and can be specified as a string or as an array item with the
	 * key "__contextNodePath". The latter case is for updating existing nodes.
	 *
	 * This conversion method does not support / allow creation of new nodes because new nodes should be created through
	 * the createNode() method of an existing reference node.
	 *
	 * Also note that the context's "current node" is not affected by this object converter, you will need to set it to
	 * whatever node your "current" node is, if any.
	 *
	 * All elements in the source array which start with two underscores (like __contextNodePath) are specially treated
	 * by this converter.
	 *
	 * All elements in the source array which start with a *single underscore (like _hidden) are *directly* set on the Node
	 * object.
	 *
	 * All other elements, not being prefixed with underscore, are properties of the node.
	 *
	 * @param string|array $source Either a string or array containing the absolute context node path which identifies the node. For example "/sites/mysitecom/homepage/about@user-admin"
	 * @param string $targetType not used
	 * @param array $subProperties not used
	 * @param \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration not used
	 * @return mixed An object or \TYPO3\Flow\Error\Error if the input format is not supported or could not be converted for other reasons
	 * @throws \Exception
	 */
	public function convertFrom($source, $targetType, array $subProperties = array(), \TYPO3\Flow\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
		if (is_string($source)) {
			$source = array('__contextNodePath' => $source);
		}

		if (!is_array($source) || !isset($source['__contextNodePath'])) {
			return new Error('Could not convert ' . gettype($source) . ' to Node object, a valid absolute context node path as a string or array is expected.', 1302879936);
		}

		preg_match(NodeInterface::MATCH_PATTERN_CONTEXTPATH, $source['__contextNodePath'], $matches);
		if (!isset($matches['NodePath'])) {
			return new Error('Could not convert array to Node object because the node path was invalid.', 1285162903);
		}
		$nodePath = $matches['NodePath'];

		if ($this->nodeRepository->getContext() === NULL) {
			$workspaceName = (isset($matches['WorkspaceName']) ? $matches['WorkspaceName'] : 'live');
			$contentContext = new ContentContext($workspaceName);
			$this->nodeRepository->setContext($contentContext);
		} else {
			$contentContext = $this->nodeRepository->getContext();
			$workspaceName = $contentContext->getWorkspace()->getName();
		}
		if ($workspaceName !== 'live') {
			$contentContext->setInvisibleContentShown(TRUE);
			if ($configuration->getConfigurationValue('TYPO3\TYPO3\Routing\NodeObjectConverter', self::REMOVED_CONTENT_SHOWN) === TRUE) {
				$contentContext->setRemovedContentShown(TRUE);
			}
		}

		$workspace = $contentContext->getWorkspace(FALSE);
		if (!$workspace) {
			return new Error(sprintf('Could not convert %s to Node object because the workspace "%s" as specified in the context node path does not exist.', $source['__contextNodePath'], $workspaceName), 1285162905);
		}

		$currentAccessModeFromContext = $contentContext->isInaccessibleContentShown();
		$contentContext->setInaccessibleContentShown(TRUE);
		$node = $contentContext->getNode($nodePath);
		$contentContext->setInaccessibleContentShown($currentAccessModeFromContext);

		if (!$node) {
			return new Error(sprintf('Could not convert array to Node object because the node "%s" does not exist.', $nodePath), 1285162908);
		}

		$contentTypeProperties = $node->getContentType()->getProperties();

		foreach ($source as $nodePropertyKey => $nodePropertyValue) {
			if (substr($nodePropertyKey, 0, 2) === '__') {
				continue;
			}
			if ($nodePropertyKey[0] === '_') {
				$propertyName = substr($nodePropertyKey, 1);

					// TODO: Hack: we need to create DateTime objects for some properties of Node
				if (($propertyName === 'hiddenBeforeDateTime' || $propertyName === 'hiddenAfterDateTime') && is_string($nodePropertyValue)) {
					if ($nodePropertyValue !== '') {
						$nodePropertyValue = \DateTime::createFromFormat('!Y-m-d', $nodePropertyValue);
					} else {
						$nodePropertyValue = NULL;
					}
				}
				\TYPO3\Flow\Reflection\ObjectAccess::setProperty($node, $propertyName, $nodePropertyValue);
			} else {
				if (!isset($contentTypeProperties[$nodePropertyKey])) {
					throw new \Exception('TODO: content type XY does not have a property YY according to the schema');
				}
				if (isset($contentTypeProperties[$nodePropertyKey]['type'])) {
					$targetType = $contentTypeProperties[$nodePropertyKey]['type'];
					if ($this->objectManager->isRegistered($targetType)) {
						$nodePropertyValue = $this->propertyMapper->convert(json_decode($nodePropertyValue, TRUE), $targetType);
					}
				}

				$node->setProperty($nodePropertyKey, $nodePropertyValue);
			}
		}
		return $node;
	}
}
?>