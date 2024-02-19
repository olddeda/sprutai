<?php
namespace common\modules\payment\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class State
 * @package common\modules\payment\helpers\enum
 */
class State extends BaseEnum
{
	const CREATED			    = 0;    // Создано платежное поручение, но еще не отправлено в платежную систему
	const WAIT_VERIFICATION		= 1;    // Операция ожидает запроса верификации от платежной системы
	const WAIT_RESULT		    = 2;    // Операция ожидает получения окончательного результата от платежной системы
	const COMPLETE              = 3;    // Результат операции известен, получен о ПС или выставлен оператором вручную
	const COMPLETE_VERIFY       = 4;    // Результат операции известен, отправлен ответ к ПС, ждем редиректа от ПС. Необходимо для некоторых ПС.
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'payment-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::CREATED 		        => 'created',
		self::WAIT_VERIFICATION   	=> 'wait_verification',
		self::WAIT_RESULT	        => 'wait_result',
		self::COMPLETE              => 'complete',
		self::COMPLETE_VERIFY       => 'complete_verify',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}