<?php
namespace TYPO3\Media\ViewHelpers\Uri;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Media".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Renders the src path of a thumbnail image of a given TYPO3.Media Image instance
 *
 * = Examples =
 *
 * <code title="Rendering an image path as-is">
 * {m:uri.image(image: imageObject)}
 * </code>
 * <output>
 * (depending on the image)
 * _Resources/Persistent/b29[...]95d.jpeg
 * </output>
 *
 *
 * <code title="Rendering an image path with scaling at a given width only">
 * {m:uri.image(image: imageObject, maximumWidth: 80)}
 * </code>
 * <output>
 * (depending on the image; has scaled keeping the aspect ratio)
 * _Resources/Persistent/b29[...]95d.jpeg
 * </output>
 *
 * @see \TYPO3\Media\ViewHelpers\ImageViewHelper
 */
class ImageViewHelper extends \TYPO3\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
	 * @Flow\Inject
	 */
	protected $resourcePublisher;

	/**
	 * Renders the path to a thumbnail image, created from a given image.
	 *
	 * @param \TYPO3\Media\Domain\Model\Image $image If specified, this resource object is used instead of the path and package information
	 * @param integer $maximumWidth Desired maximum height of the image
	 * @param integer $maximumHeight Desired maximum width of the image
	 * @param boolean $allowCropping Whether the image should be cropped if the given sizes would hurt the aspect ratio
	 * @param boolean $allowUpScaling Whether the resulting image size might exceed the size of the original image
	 *
	 * @return string the relative image path, to be used as src attribute for <img /> tags
	 */
	public function render(\TYPO3\Media\Domain\Model\Image $image = NULL, $maximumWidth = NULL, $maximumHeight = NULL, $allowCropping = FALSE, $allowUpScaling = FALSE) {
		$ratioMode = ($allowCropping ? \TYPO3\Media\Domain\Model\ImageInterface::RATIOMODE_OUTBOUND : \TYPO3\Media\Domain\Model\ImageInterface::RATIOMODE_INSET);
		if ($allowUpScaling !== TRUE && $maximumWidth > $image->getWidth()) {
			$maximumWidth = $image->getWidth();
		}
		if ($allowUpScaling !== TRUE && $maximumHeight > $image->getHeight()) {
			$maximumHeight = $image->getHeight();
		}
		$thumbnailImageVariant = $image->getThumbnail($maximumWidth, $maximumHeight, $ratioMode);
		return $this->resourcePublisher->getPersistentResourceWebUri($thumbnailImageVariant->getResource());
	}

}
?>