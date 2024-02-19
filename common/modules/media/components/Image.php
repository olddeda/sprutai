<?php
namespace common\modules\media\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Palette\RGB;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use Imagine\Image\Metadata\ExifMetadataReader;

use common\modules\base\components\Debug;

class Image extends Component
{
	/**
	 * GD2 driver definition for Imagine implementation using the GD library.
	 */
	const DRIVER_GD2 = 'gd2';

	/**
	 * imagick driver definition.
	 */
	const DRIVER_IMAGICK = 'imagick';

	/**
	 * gmagick driver definition.
	 */
	const DRIVER_GMAGICK = 'gmagick';

	/**
	 * @var array|string the driver to use. This can be either a single driver name or an array of driver names.
	 * If the latter, the first available driver will be used.
	 */
	public static $driver = [self::DRIVER_GMAGICK, self::DRIVER_IMAGICK, self::DRIVER_GD2];

	/**
	 * @var ImagineInterface instance.
	 */
	private static $_imagine;

	/**
	 * @var
	 */
	private $_imgSrc;
	private $_imgDst;


	/**
	 * Returns the `Imagine` object that supports various image manipulations.
	 * @return ImagineInterface the `Imagine` object
	 */
	public static function getImagine() {
		if (self::$_imagine === null) {
			self::$_imagine = static::createImagine();
			self::$_imagine->setMetadataReader(new ExifMetadataReader());
		}
		return self::$_imagine;
	}

	/**
	 * @param ImagineInterface $imagine the `Imagine` object.
	 */
	public static function setImagine($imagine) {
		self::$_imagine = $imagine;
	}

	/**
	 * Creates an `Imagine` object based on the specified [[driver]].
	 * @return ImagineInterface the new `Imagine` object
	 * @throws InvalidConfigException if [[driver]] is unknown or the system doesn't support any [[driver]].
	 */
	protected static function createImagine() {
		foreach ((array) static::$driver as $driver) {
			switch ($driver) {
				case self::DRIVER_GMAGICK:
					if (class_exists('Gmagick', false)) {
						return new \Imagine\Gmagick\Imagine();
					}
					break;
				case self::DRIVER_IMAGICK:
					if (class_exists('Imagick', false)) {
						return new \Imagine\Imagick\Imagine();
					}
					break;
				case self::DRIVER_GD2:
					if (function_exists('gd_info')) {
						return new \Imagine\Gd\Imagine();
					}
					break;
				default:
					throw new InvalidConfigException("Unknown driver: $driver");
			}
		}
		throw new InvalidConfigException("Your system does not support any of these drivers: " . implode(',', (array) static::$driver));
	}

	/**
	 * Open image
	 * @param string $filename
	 */
	public function open($filename) {
		$this->_imgSrc = static::getImagine()->open(Yii::getAlias($filename));
		$this->_imgDst = $this->_imgSrc->copy();
	}

	/**
	 * Load image from contents
	 * @param string $data
	 */
	public function load($data) {
		$this->_imgSrc = static::getImagine()->load($data);
		$this->_imgDst = $this->_imgSrc->copy();
	}

	/**
	 * Get width
	 * @return mixed
	 */
	public function getWidth() {
		return $this->_imgSrc->getSize()->getWidth();
	}

	/**
	 * Get height
	 * @return mixed
	 */
	public function getHeight() {
		return $this->_imgSrc->getSize()->getHeight();
	}

	/**
	 * Show image binary
	 * @param string $format
	 */
	public function show($format = null) {
		if (!$format)
			$format = 'png';
		$this->_imgDst->show($format);
	}

	/**
	 * Show image binary
	 * @param string $format
	 */
	public function get($format = null) {
		if (!$format)
			$format = 'png';
		return $this->_imgDst->get($format);
	}

	/**
	 * Resize
	 * @param $width
	 * @param $height
	 */
	public function resize($width, $height, $proportional = true) {
		if ($proportional) {
			if ($width && $height) {
				if ($this->getWidth() > $this->getHeight())
					$height = round($width / $this->getWidth() * $this->getHeight());
				else
					$width = round($height / $this->getHeight() * $this->getWidth());
			}
			else {
				if ($width > $height) {
					if ($width > $this->getWidth())
						$width = $this->getWidth();
					$height = round($width / $this->getWidth() * $this->getHeight());
				}
				else
					$width = round($height / $this->getHeight() * $this->getWidth());
			}
		}

		$this->_imgDst = $this->_imgDst->resize(new Box($width, $height));
	}

	/**
	 * Crop and scale
	 * @param $parameters = array(
	 * 'width' => 300,			/// ширина получаемой картинки
	 * 'height' => 500,		/// высота получаемой картинки
	 * 'cropX' => 1000,		/// ручная установка отступа кропа по x
	 * 'cropY' => 700,		/// ручная установка отступа кропа по y
	 * 'cropWidth' => 400,	/// ручная установка ширины кропа
	 * 'cropHeight' => 600,	/// ручная установка высоты кропа
	 * 'cropRatio' => 0.7,	/// относительный отступ кропа внутрь
	 * 'centerX' => false,	/// горизонтально центрировать область кропа по исходной картинке
	 * 'centerY' => false,	/// вертикально центрировать область кропа по исходной картинке
	 * 'simple' => false,		/// простая обрезка и масштабирование
	 * 'aspect' => true,		/// сохранять пропорции
	 * 'magnify' => true,		/// разрешить увеличение при масштабировании
	 * );
	 */
	public function cropAndScale($parameters) {
		$width = $parameters['width'];
		$height = $parameters['height'];

		$cropX = isset($parameters['cropX']) ? $parameters['cropX'] : 0;
		$cropY = isset($parameters['cropY']) ? $parameters['cropY'] : 0;
		$cropWidth = isset($parameters['cropWidth']) ? $parameters['cropWidth'] : $width;
		$cropHeight = isset($parameters['cropHeight']) ? $parameters['cropHeight'] : $height;

		if (isset($parameters['simple']) && $parameters['simple']) {
			$k = (real)$width / (real)$height;
			$kk = (real)$this->getWidth() / (real)$this->getHeight();
			if ($k > $kk) {
				$cropWidth = $this->getWidth();
				$cropHeight = (int)((real)$this->getWidth() / (real)$k);
			}
			if ($k < $kk) {
				$cropWidth = (int)((real) $k * (real)$this->getHeight());
				$cropHeight = $this->getHeight();
			}
			if ($k == $kk) {
				$cropWidth = $this->getWidth();
				$cropHeight = $this->getHeight();
			}
		}

		if (isset($parameters['centerX']) && $parameters['centerX']) {
			$cropX = (int)((real)$this->getWidth() / 2.0 - (real)$cropWidth / 2.0);
		}
		if (isset($parameters['centerY']) && $parameters['centerY']) {
			$cropY = (int)((real)$this->getHeight() / 2.0 - (real)$cropHeight / 2.0);
		}

		if (isset($parameters['cropRatio'])) {
			$cropX += ((real)$cropWidth - (real)$parameters['cropRatio'] * (real)$cropWidth) / 2.0;
			$cropY += ((real)$cropHeight - (real)$parameters['cropRatio'] * (real)$cropHeight) / 2.0;
			$cropWidth = (real)$parameters['cropRatio'] * (real)$cropWidth;
			$cropHeight = (real)$parameters['cropRatio'] * (real)$cropHeight;
		}

		$cropX = $cropX > 0 ? $cropX : 0;
		$cropY = $cropY > 0 ? $cropY : 0;
		

		if (isset($parameters['aspect']) && $parameters['aspect']) {
			
			$this->_imgDst = $this->_imgDst->crop(new Point($cropX, $cropY), new Box($cropWidth, $cropHeight));

			// Zoom
			if (!isset($parameters['simple']) || !$parameters['simple']) {
				$k = (real)$cropWidth / (real)$cropHeight;
				if ($k > 1)
					$height = (int)((real) $width / (real)$k);
				if ($k < 1)
					$width = (int)((real) $k * (real)$height);
				if ($k == 1) {
					if ($width > $height)
						$width = $height;
					else
						$height = $width;
				}
			}
			
			$this->_imgDst = $this->_imgDst->resize(new Box($width, $height));
			//$this->_imgDst->show('png');
		}
		else {
			$this->_imgDst = $this->_imgSrc->crop(new Point($cropX, $cropY), new Box($cropWidth, $cropHeight));
		}
	}

	/**
	 * Crops an image.
	 *
	 * For example,
	 *
	 * ~~~
	 * $obj->crop('path\to\image.jpg', 200, 200, [5, 5]);
	 *
	 * $point = new \Imagine\Image\Point(5, 5);
	 * $obj->crop('path\to\image.jpg', 200, 200, $point);
	 * ~~~
	 *
	 * @param string $filename the image file path or path alias.
	 * @param integer $width the crop width
	 * @param integer $height the crop height
	 * @param array $start the starting point. This must be an array with two elements representing `x` and `y` coordinates.
	 * @return ImageInterface
	 * @throws InvalidParamException if the `$start` parameter is invalid
	 */
	public static function crop($filename, $width, $height, array $start = [0, 0]) {
		if (!isset($start[0], $start[1])) {
			throw new InvalidParamException('$start must be an array of two elements.');
		}

		return static::getImagine()->open(Yii::getAlias($filename))->copy()->crop(new Point($start[0], $start[1]), new Box($width, $height));
	}

	/**
	 * Creates a thumbnail image. The function differs from `\Imagine\Image\ImageInterface::thumbnail()` function that
	 * it keeps the aspect ratio of the image.
	 * @param string $filename the image file path or path alias.
	 * @param integer $width the width in pixels to create the thumbnail
	 * @param integer $height the height in pixels to create the thumbnail
	 * @param string $mode
	 * @return ImageInterface
	 */
	public static function thumbnail($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND) {
		$box = new Box($width, $height);
		$img = static::getImagine()->open(Yii::getAlias($filename));

		if (($img->getSize()->getWidth() <= $box->getWidth() && $img->getSize()->getHeight() <= $box->getHeight()) || (!$box->getWidth() && !$box->getHeight())) {
			return $img->copy();
		}

		$img = $img->thumbnail($box, $mode);

		// create empty image to preserve aspect ratio of thumbnail
		$thumb = static::getImagine()->create($box, new Color('FFF', 100));

		// calculate points
		$size = $img->getSize();

		$startX = 0;
		$startY = 0;
		if ($size->getWidth() < $width) {
			$startX = ceil($width - $size->getWidth()) / 2;
		}
		if ($size->getHeight() < $height) {
			$startY = ceil($height - $size->getHeight()) / 2;
		}

		$thumb->paste($img, new Point($startX, $startY));

		return $thumb;
	}

	/**
	 * Adds a watermark to an existing image.
	 * @param string $filename the image file path or path alias.
	 * @param string $watermarkFilename the file path or path alias of the watermark image.
	 * @param array $start the starting point. This must be an array with two elements representing `x` and `y` coordinates.
	 * @return ImageInterface
	 * @throws InvalidParamException if `$start` is invalid
	 */
	public static function watermark($filename, $watermarkFilename, array $start = [0, 0]) {
		if (!isset($start[0], $start[1])) {
			throw new InvalidParamException('$start must be an array of two elements.');
		}

		$img = static::getImagine()->open(Yii::getAlias($filename));
		$watermark = static::getImagine()->open(Yii::getAlias($watermarkFilename));
		$img->paste($watermark, new Point($start[0], $start[1]));

		return $img;
	}

	/**
	 * Draws a text string on an existing image.
	 * @param string $filename the image file path or path alias.
	 * @param string $text the text to write to the image
	 * @param string $fontFile the file path or path alias
	 * @param array $start the starting position of the text. This must be an array with two elements representing `x` and `y` coordinates.
	 * @param array $fontOptions the font options. The following options may be specified:
	 *
	 * - color: The font color. Defaults to "fff".
	 * - size: The font size. Defaults to 12.
	 * - angle: The angle to use to write the text. Defaults to 0.
	 *
	 * @return ImageInterface
	 * @throws InvalidParamException if `$fontOptions` is invalid
	 */
	public static function text($filename, $text, $fontFile, array $start = [0, 0], array $fontOptions = []) {
		if (!isset($start[0], $start[1])) {
			throw new InvalidParamException('$start must be an array of two elements.');
		}

		$fontSize = ArrayHelper::getValue($fontOptions, 'size', 12);
		$fontColor = ArrayHelper::getValue($fontOptions, 'color', 'fff');
		$fontAngle = ArrayHelper::getValue($fontOptions, 'angle', 0);

		$img = static::getImagine()->open(Yii::getAlias($filename));
		$font = static::getImagine()->font(Yii::getAlias($fontFile), $fontSize, new Color($fontColor));

		$img->draw()->text($text, $font, new Point($start[0], $start[1]), $fontAngle);

		return $img;
	}

	/**
	 * Adds a frame around of the image. Please note that the image size will increase by `$margin` x 2.
	 * @param string $filename the full path to the image file
	 * @param integer $margin the frame size to add around the image
	 * @param string $color the frame color
	 * @param integer $alpha the alpha value of the frame.
	 * @return ImageInterface
	 */
	public static function frame($filename, $margin = 20, $color = '666', $alpha = 100) {
		$img = static::getImagine()->open(Yii::getAlias($filename));

		$size = $img->getSize();

		$pasteTo = new Point($margin, $margin);
		$padColor = new Color($color, $alpha);

		$box = new Box($size->getWidth() + ceil($margin * 2), $size->getHeight() + ceil($margin * 2));

		$image = static::getImagine()->create($box, $padColor);

		$image->paste($img, $pasteTo);

		return $image;
	}
}