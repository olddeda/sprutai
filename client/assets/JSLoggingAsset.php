<?php

namespace client\assets;

use yii\helpers\Url;
use yii\web\View;

use common\modules\base\components\Debug;

use bedezign\yii2\audit\web\JSLoggingAsset as BaseJSLoggingAsset;
use bedezign\yii2\audit\Audit;

class JSLoggingAsset extends BaseJSLoggingAsset
{
	/**
	 * @param \yii\web\AssetManager $assetManager
	 */
	public function publish($assetManager)  {
		$module = Audit::getInstance();
		
		$url = Url::to(["/{$module->id}/js-log"]);
		$script = "window.auditUrl = '$url';";
		if ($module->entry) {
			$id = $module->getEntry()->id;
			$script .= "window.auditEntry = $id;";
		}
		\Yii::$app->view->registerJs($script, View::POS_HEAD);
		parent::publish($assetManager);
	}
}