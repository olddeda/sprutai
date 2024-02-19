<?php
namespace common\modules\catalog\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class CorrectAction extends BaseEnum
{
	const UPDATE             = 0;
	const ADD   		     = 1;
	const REMOVE             = 2;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'catalog-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::UPDATE => 'correct_action_update',
		self::ADD => 'correct_action_add',
        self::REMOVE => 'correct_action_remove',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}