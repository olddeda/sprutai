<?php
namespace common\modules\seo\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class SeoHeadWidget
 * @package common\modules\seo\widgets
 */

class SeoHeadWidget extends Widget
{
	/** @var string */
	public $title;
	
	/** @var string */
	public $keywords;
	
	/** @var string */
	public $description;
	
	/** @var string */
	public $h1;
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		$view = $this->getView();
		if ($view->seo) {
			$seo = $view->seo;
			
			if (!empty($seo->title))
				$this->title = $seo->title;
			
			if (!empty($seo->keywords))
				$this->keywords = $seo->keywords;
			
			if (!empty($seo->description))
				$this->description = $seo->description;
		}
		
		if (!$view->title && isset($view->params['breadcrumbs']) && count($view->params['breadcrumbs'])) {
			$tmp = [];
			foreach ($view->params['breadcrumbs'] as $b) {
				if (is_array($b) && isset($b['label']))
					$tmp[] = $b['label'];
				if (is_string($b))
					$tmp[] = $b;
			}
			if (count($tmp))
				$view->title = implode(' - ', $tmp);
		}
		
		if ($view->keywords)
			$this->keywords = $this->keywords;
		if ($view->description)
			$this->description = $view->description;
		
		$t = $this->title ?: $view->title;
		$page = Yii::$app->request->get('page', 0);
		if ($page)
			$t .= Yii::t('seo', 'title_page', $page);
		
		$this->h1 = $view->title;
		if ($page)
			$this->h1 .= Yii::t('seo', 'title_page', $page);
		$view->h1 = $this->h1;
		
		echo Html::tag('title', Html::encode($t));
		
		if ($this->keywords) {
			$view->registerMetaTag([
				'name' => 'keywords',
				'content' => $this->keywords,
			]);
		}
		
		if ($this->description) {
			$view->registerMetaTag([
				'name' => 'description',
				'content' => $this->description,
			]);
		}
	}
}