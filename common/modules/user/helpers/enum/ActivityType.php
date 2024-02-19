<?php
namespace common\modules\user\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class ActivityType
 * @package common\modules\user\heplers\enum
 */
class ActivityType extends BaseEnum
{
	const SIGNUP        = 0;
    const CONTENT       = 1;
    const COMMENT       = 3;
    const REVIEW        = 4;
    const LIKES         = 5;
    const LIKED         = 6;
    const SUBSCRIBES    = 7;
    const SUBSCRIBED    = 8;
    const ACHIEVEMENT   = 9;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'user-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::SIGNUP 		=> 'activity_type_signup',
        self::CONTENT       => 'activity_type_content',
        self::COMMENT       => 'activity_type_comment',
        self::REVIEW        => 'activity_type_review',
        self::LIKES         => 'activity_type_likes',
        self::LIKED         => 'activity_type_liked',
        self::SUBSCRIBES    => 'activity_type_subscribes',
        self::SUBSCRIBED    => 'activity_type_subscribed',
        self::ACHIEVEMENT   => 'activity_type_achievement',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}