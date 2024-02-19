<?php
namespace client\controllers;

use common\modules\base\extensions\sitemap\Sitemap;
use common\modules\base\extensions\sitemap\SitemapController as BaseController;

use common\modules\content\models\Article;
use common\modules\content\models\News;
use common\modules\content\models\Portfolio;

class SitemapController extends BaseController {
	
	/**
	 * @var int Cache duration, set null to disabled
	 */
	protected $cacheDuration = 3600;
	
	/**
	 * @var string Cache filename
	 */
	protected $cacheFilename = 'sitemap.xml';
	
	/**
	 * @return array
	 */
	public function models() {
		return [
			[
				'class' => Article::class,
				'change' => Sitemap::HOURLY,
				'priority' => 1.0
			],
			[
				'class' => News::class,
				'change' => Sitemap::DAILY,
				'priority' => 0.8
			],
			[
				'class' => Portfolio::class,
				'change' => Sitemap::DAILY,
				'priority' => 0.8
			],
		];
	}
	
	/**
	 * @return array
	 */
	public function urls() {
		return [];
	}
}