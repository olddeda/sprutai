<?php

namespace common\modules\base\helpers\enum;

/**
 * Class ModuleType
 * @package common\modules\base\helpers\enum
 */
class ModuleType extends BaseEnum
{
	const NONE				= 0;
	const COMPANY			= 1;
	const USER				= 2;
	const MEDIA				= 3;
	const BLOG				= 4;
	const COMMENT			= 5;
	const LOOKUP			= 7;
	const MENU				= 8;
	const MENU_ITEM			= 9;
	const STORE				= 10;
	const STORE_ITEM		= 13;
	const EXPERTS			= 16;
	const TAG				= 17;
	const TAG_FILTER		= 22;
	const ITEM				= 30;
	
	const CONTENT			= 40;
	const CONTENT_PAGE		= 41;
	const CONTENT_NEWS		= 42;
	const CONTENT_ARTICLE	= 43;
	const CONTENT_PROJECT	= 44;
	const CONTENT_BLOG		= 45;
	const CONTENT_PLUGIN	= 46;
	const CONTENT_QUESTION	= 47;
	
	const EVENT				= 50;
	
	const PLUGIN_VERSION	= 60;
	
	const COMPANY_DISCOUNT	= 70;

	const CATALOG                   = 80;
	const CATALOG_ITEM              = 81;
    const CATALOG_ITEM_ORDER        = 82;
    const CATALOG_FIELD_GROUP       = 83;
    const CATALOG_FIELD_GROUP_TAG   = 84;
    const CATALOG_FIELD             = 85;
    const CATALOG_ITEM_FIELD        = 86;
    const CATALOG_ITEM_CORRECT      = 89;

	const VOTE              = 90;

	const ACHIEVEMENT       = 100;
    const ACHIEVEMENT_USER  = 101;
	
	const BANNER			= 150;
	
	const CONTEST			= 160;
	
	const PASTE				= 170;
    
    const MAILING			= 180;
    const MAILING_USER	    = 181;
    
    const SHORTENER         = 190;
    const SHORTENER_HIT     = 191;
	
	const QA_QUESTION		= 200;
	const QA_ANSWER			= 201;
	
	const TELEGRAM_USER		= 701;
	const TELEGRAM_CHAT		= 702;
	
	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::NONE				=> 'none',
		self::USER				=> 'user',
		self::MEDIA				=> 'media',
		self::BLOG 				=> 'blog',
		self::COMMENT 			=> 'comment',
		self::TAG				=> 'tag',
		self::LOOKUP			=> 'lookup',
		self::MENU				=> 'menu',
		self::MENU_ITEM			=> 'menu_item',
		self::STORE				=> 'store',
		self::STORE_ITEM		=> 'store_item',
		self::ITEM				=> 'item',
		self::EXPERTS			=> 'experts',
		
		self::CONTENT			=> 'content',
		self::CONTENT_PAGE 		=> 'content_page',
		self::CONTENT_NEWS		=> 'content_news',
		self::CONTENT_ARTICLE	=> 'content_article',
		self::CONTENT_PROJECT	=> 'content_project',
		self::CONTENT_PLUGIN	=> 'content_plugin',
		
		self::EVENT				=> 'event',
		
		self::PLUGIN_VERSION	=> 'plugin_version',
		
		self::COMPANY_DISCOUNT	=> 'company_discount',

        self::CATALOG           => 'catalog',
        self::CATALOG_ITEM      => 'catalog_item',
		
		self::BANNER			=> 'banner',
		
		self::CONTEST			=> 'contest',
		
		self::PASTE				=> 'paste',
        
        self::MAILING           => 'mailing',
        
        self::SHORTENER         => 'shortener',
        self::SHORTENER_HIT     => 'shortener_hit',
		
		self::QA_QUESTION		=> 'qa_question',
		self::QA_ANSWER			=> 'qa_answer',
		
		self::TELEGRAM_USER		=> 'telegram_user',
	];

	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
	
}