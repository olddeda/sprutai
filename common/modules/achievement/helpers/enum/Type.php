<?php
namespace common\modules\achievement\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Type
 * @package common\modules\achievement\heplers\enum
 */
class Type extends BaseEnum
{
	const OWNER         = 0;
    const REVIEW        = 1;
    const ARTICLE       = 2;
    const NEWS          = 3;
    const BLOG          = 4;
    const PLUGIN        = 5;
    const COMMENT       = 6;
    const LIKES         = 7;
    const LIKED         = 8;
    const SUBSCRIBED    = 9;

	/**
	 * @var string message category
	 */
	public static $messageCategory = 'achievement-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::OWNER 		=> 'type_owner',
        self::REVIEW 		=> 'type_review',
        self::ARTICLE 	    => 'type_article',
        self::NEWS          => 'type_news',
        self::BLOG          => 'type_blog',
        self::PLUGIN        => 'type_plugin',
        self::COMMENT       => 'type_comment',
        self::LIKES         => 'type_likes',
        self::LIKED         => 'type_liked',
        self::SUBSCRIBED    => 'type_subscribed',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}