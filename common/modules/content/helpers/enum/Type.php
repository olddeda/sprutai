<?php
namespace common\modules\content\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

/**
 * Class Type
 * @package common\modules\content\helpers\enum
 */
class Type extends BaseEnum
{
	const NONE			= 0;
	const PAGE			= 1;
	const NEWS			= 2;
	const ARTICLE		= 3;
	const PROJECT		= 4;
	const BLOG			= 5;
	const PLUGIN		= 6;
	const INSTRUCTION	= 7;
	const QUESTION		= 8;
	const PORTFOLIO		= 9;
	const EVENT			= 10;
	const SHORTCUT		= 11;
	const VIDEO         = 12;
	const QA            = 13;

	const CATALOG_ITEM_SPRUTHUB = 21;

	const VERSION		= 100;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'content-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::NONE 			=> 'type_none',
		self::PAGE 			=> 'type_page',
		self::NEWS 			=> 'type_news',
		self::ARTICLE 		=> 'type_article',
		self::PROJECT 		=> 'type_project',
		self::BLOG 			=> 'type_blog',
		self::PLUGIN 		=> 'type_plugin',
		self::INSTRUCTION 	=> 'type_instruction',
		self::QUESTION		=> 'type_question',
		self::PORTFOLIO		=> 'type_portfolio',
		self::EVENT			=> 'type_event',
		self::SHORTCUT		=> 'type_shortcut',
        self::VIDEO		    => 'type_video',
		self::VERSION		=> 'type_version',
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}