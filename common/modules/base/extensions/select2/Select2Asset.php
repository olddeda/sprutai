<?php
namespace common\modules\base\extensions\select2;

use common\modules\base\components\Debug;
use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
	/**
	 * @var string
	 */
    public $sourcePath = '@bower/select2/dist';
	
	/**
	 * @var array
	 */
    public $js = [
        'js/select2.full.min.js',
    ];
	
	/**
	 * @var array
	 */
    public $css = [
        'css/select2.min.css',
    ];
	
	/**
	 * @var array
	 */
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
    public function init() {
		parent::init();
	}
	
	/**
     * @inheritdoc
     */
    public function registerAssetFiles($view) {
        $language = \Yii::$app->language;
        if (strpos($language, '-') !== 0)
        	$language = current(explode('-', $language));

        if (is_file(\Yii::getAlias("{$this->sourcePath}/js/i18n/{$language}.js"))) {
            $this->js[] = "js/i18n/{$language}.js";
        }
        parent::registerAssetFiles($view);
    }
}
