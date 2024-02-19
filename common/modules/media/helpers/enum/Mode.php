<?php

namespace common\modules\media\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Mode extends BaseEnum
{
	const RESIZE		= 0;
	const CROP_TOP		= 1;
	const CROP_CENTER	= 2;
	const RESIZE_WIDTH  = 3;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'media-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::RESIZE 		=> 'resize',
		self::CROP_TOP 		=> 'crop_top',
		self::CROP_CENTER	=> 'crop_center',
		self::RESIZE_WIDTH  => 'resize_width',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}