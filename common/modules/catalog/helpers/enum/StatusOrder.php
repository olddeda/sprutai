<?php
namespace common\modules\catalog\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class StatusOrder extends BaseEnum
{
	const WAIT              = 0;
	const COMPLETE 		    = 1;
	const PENDING           = 2;
    const DELETED           = 3;
	const SENT              = 4;
	const CANCELED          = 5;
	const PAID              = 6;
	const PAID_WAIT         = 7;
    const ADDRESS           = 8;
	const PREORDER          = 9;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'catalog-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::WAIT => 'status_order_wait',
		self::COMPLETE => 'status_order_complete',
        self::PENDING => 'status_order_pending',
        self::DELETED => 'status_order_deleted',
        self::CANCELED => 'status_order_canceled',
        self::SENT => 'status_order_sent',
        self::PAID => 'status_order_paid',
        self::PAID_WAIT => 'status_order_paid_wait',
        self::ADDRESS => 'status_order_address',
		self::PREORDER => 'status_order_preorder',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}