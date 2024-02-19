<?php
namespace common\modules\user\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class Subscribe extends BaseEnum
{
	const NONE			= 0;
	const ALL			= 1;
	const EMAIL			= 2;
	const TELEGRAM		= 3;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'user-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::NONE 		=> 'subscribe_none',
		self::ALL 		=> 'subscribe_all',
		self::EMAIL		=> 'subscribe_email',
		self::TELEGRAM	=> 'subscribe_telegram',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [
	];
}