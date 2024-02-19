<?php
namespace common\modules\payment\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Result
 * @package common\modules\payment\helpers\enum
 */
class Result extends BaseEnum
{
	const SUCCEED       = 0;    // Платежная система сообщила об успешно проведенном платеже
	const FAILED        = 1;    // Платежная система сообщила об ошибке в процессе платежа
	const CANCELED      = 2;    // Платеж отменен плательщиком через платежную систему
	const REJECTED      = 3;    // Платежное поручение, поданное платежной системой в нашу на проверку, не подтверждено
	const ERROR         = 4;    // Зарегистрирована ошибка в обработке сообщения от ПС
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'payment-enum';
	
	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::SUCCEED       => 'result_succeed',
		self::FAILED        => 'result_failed',
		self::CANCELED      => 'result_canceled',
		self::REJECTED      => 'result_rejected',
		self::ERROR         => 'result_error',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}