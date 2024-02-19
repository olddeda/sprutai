<?php

namespace common\modules\media\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Type extends BaseEnum
{
	const IMAGE 			= 0;
	const VIDEO 			= 2;
	const FILE 				= 3;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'media-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::IMAGE => 'image',
		self::VIDEO => 'video',
		self::FILE => 'file',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}