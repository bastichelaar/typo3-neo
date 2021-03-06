<?php
namespace TYPO3\Media\Tests\Unit\ViewHelpers\Uri;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Media".           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Testcase for the uri.image ViewHelper
 */
class ImageViewHelperTest extends \TYPO3\Flow\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\Media\ViewHelpers\Uri\ImageViewHelper
	 */
	protected $viewHelper;

	/**
	 * @var \TYPO3\Flow\Resource\Publishing\ResourcePublisher
	 */
	protected $mockResourcePublisher;

	/**
	 * @var \TYPO3\Media\Domain\Model\Image
	 */
	protected $mockImage;

	/**
	 * @var \TYPO3\Media\Domain\Model\ImageVariant
	 */
	protected $mockThumbnail;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->viewHelper = $this->getAccessibleMock('TYPO3\Media\ViewHelpers\Uri\ImageViewHelper', array('dummy'));
		$this->mockResourcePublisher = $this->getMock('TYPO3\Flow\Resource\Publishing\ResourcePublisher');
		$this->viewHelper->_set('resourcePublisher', $this->mockResourcePublisher);
		$this->mockImage = $this->getMockBuilder('TYPO3\Media\Domain\Model\Image')->disableOriginalConstructor()->getMock();
		$this->mockThumbnail = $this->getMockBuilder('TYPO3\Media\Domain\Model\ImageVariant')->disableOriginalConstructor()->getMock();
	}

	/**
	 * @test
	 */
	public function ratioModeDefaultsToInset() {
		$this->mockImage->expects($this->once())->method('getThumbnail')->with(NULL, NULL, \TYPO3\Media\Domain\Model\Image::RATIOMODE_INSET)->will($this->returnValue($this->mockThumbnail));
		$this->viewHelper->render($this->mockImage);
	}

	/**
	 * @test
	 */
	public function ratioModeIsOutboundIfAllowCroppingIsTrue() {
		$this->mockImage->expects($this->once())->method('getThumbnail')->with(NULL, NULL, \TYPO3\Media\Domain\Model\Image::RATIOMODE_OUTBOUND)->will($this->returnValue($this->mockThumbnail));
		$this->viewHelper->render($this->mockImage, NULL, NULL, TRUE);
	}

	/**
	 * @test
	 */
	public function thumbnailWidthDoesNotExceedImageWithByDefault() {
		$this->mockImage->expects($this->atLeastOnce())->method('getWidth')->will($this->returnValue(123));
		$this->mockImage->expects($this->once())->method('getThumbnail')->with(123, NULL, \TYPO3\Media\Domain\Model\Image::RATIOMODE_INSET)->will($this->returnValue($this->mockThumbnail));
		$this->viewHelper->render($this->mockImage, 456, NULL);
	}

	/**
	 * @test
	 */
	public function thumbnailHeightDoesNotExceedImageHeightByDefault() {
		$this->mockImage->expects($this->atLeastOnce())->method('getHeight')->will($this->returnValue(123));
		$this->mockImage->expects($this->once())->method('getThumbnail')->with(NULL, 123, \TYPO3\Media\Domain\Model\Image::RATIOMODE_INSET)->will($this->returnValue($this->mockThumbnail));
		$this->viewHelper->render($this->mockImage, NULL, 123);
	}

	/**
	 * @test
	 */
	public function thumbnailWidthMightExceedImageWithIfAllowUpScalingIsTrue() {
		$this->mockImage->expects($this->any())->method('getWidth')->will($this->returnValue(123));
		$this->mockImage->expects($this->once())->method('getThumbnail')->with(456, NULL, \TYPO3\Media\Domain\Model\Image::RATIOMODE_INSET)->will($this->returnValue($this->mockThumbnail));
		$this->viewHelper->render($this->mockImage, 456, NULL, FALSE, TRUE);
	}

	/**
	 * @test
	 */
	public function thumbnailHeightMightExceedImageHeightIfAllowUpScalingIsTrue() {
		$this->mockImage->expects($this->any())->method('getHeight')->will($this->returnValue(123));
		$this->mockImage->expects($this->once())->method('getThumbnail')->with(NULL, 456, \TYPO3\Media\Domain\Model\Image::RATIOMODE_INSET)->will($this->returnValue($this->mockThumbnail));
		$this->viewHelper->render($this->mockImage, NULL, 456, FALSE, TRUE);
	}
}
?>