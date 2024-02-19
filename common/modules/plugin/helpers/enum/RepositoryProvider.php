<?php
namespace common\modules\plugin\helpers\enum;

use common\modules\base\helpers\enum\BaseEnum;

class RepositoryProvider extends BaseEnum
{
	const GITHUB		= 0;
	const BITBUCKET		= 1;
	const MANUAL		= 2;
	const URL			= 3;
	
	/**
	 * @var string message category
	 */
	public static $messageCategory = 'plugin-enum';

	/**
	 * @var array list of properties
	 */
	public static $list = [
		self::GITHUB 	=> 'repository_provider_github',
		//self::BITBUCKET	=> 'repository_provider_bitbucket',
		self::MANUAL 	=> 'repository_provider_manual',
		self::URL		=> 'repository_provider_url',
		
	];
	
	/**
	 * @var array list of exclude
	 */
	public static $exclude = [];
}