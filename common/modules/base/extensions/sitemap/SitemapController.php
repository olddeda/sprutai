<?php
namespace common\modules\base\extensions\sitemap;

use yii\helpers\Url;

/**
 * Class SitemapController
 * @package common\modules\base\extensions\sitemap
 */
class SitemapController extends \yii\web\Controller
{
    /**
     * @var int Cache duration, set null to disabled
     */
    protected $cacheDuration = 43200; // default 12 hour

    /**
     * @var string Cache filename
     */
    protected $cacheFilename = 'sitemap.xml';
	
	/**
	 * @return array
	 */
    public function models() {
        return [];
    }
	
	/**
	 * @return array
	 */
    public function urls() {
        return [];
    }
	
	/**
	 * @return bool|string
	 */
    public function actionIndex() {
        $cachePath = \Yii::$app->runtimePath.DIRECTORY_SEPARATOR.$this->cacheFilename;

        if (empty($this->cacheDuration) || !is_file($cachePath) || filemtime($cachePath) < time() - $this->cacheDuration) {
            $sitemap = new Sitemap();

            if (count($this->urls())) {
				foreach ($this->urls() as $item) {
					$sitemap->addUrl(
						isset($item['url']) ? Url::toRoute($item['url'], true) : Url::toRoute($item, true),
						isset($item['change']) ? $item['change'] : Sitemap::DAILY,
						isset($item['priority']) ? $item['priority'] : 0.8,
						isset($item['lastmod']) ? $item['lastmod'] : 0
					);
				}
			}

			if (count($this->models())) {
				foreach ($this->models() as $model) {
					$obj = new $model['class'];
					if ($obj instanceof SitemapInterface) {
						$sitemap->addModels(
							$obj::sitemap()->all(),
							isset($model['change']) ? $model['change'] : Sitemap::DAILY,
							isset($model['priority']) ? $model['priority'] : 0.8
						);
					}
				}
			}

            $xml = $sitemap->render();
            file_put_contents($cachePath, $xml);
        }
        else {
            $xml = file_get_contents($cachePath);
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \Yii::$app->getResponse()->getHeaders()->set('Content-Type', 'text/xml; charset=utf-8');
        return $xml;
    }
}
