<?php
namespace common\modules\base\components\bugsnag;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\AssetBundle;

/**
 * Class BugsnagAsset
 * @package common\modules\base\components\bugsnag
 */
class BugsnagAsset extends AssetBundle
{
    /**
     * @var integer Bugsnag javascript version
     */
    public $version = 5;

    /**
     * @type boolean Use the Cloudfront CDN (which may have CORS issues @see https://github.com/bugsnag/bugsnag-js/issues/155)
     */
    public $useCdn = true;

    /**
     * Initiates Bugsnag javascript registration
     */
    public function init() {
        if (!Yii::$app->has('bugsnag')) {
            throw new InvalidConfigException('BugsnagAsset requires Bugsnag component to be enabled');
        }
	
		if (!Yii::$app->bugsnag->apiKey) {
			throw new InvalidConfigException('BugsnagAsset requires Bugsnag api key');
		}

        $this->registerJavascript();

        parent::init();
    }
    
    /**
     * Registers Bugsnag JavaScript to page
     */
    private function registerJavascript() {
        $filePath = '//d2wy8f7a9ursnm.cloudfront.net/v'.$this->version.'/bugsnag.min.js';
        if (!$this->useCdn) {
            $this->sourcePath = '@bower/bugsnag/src';
			$filePath = 'bugsnag.js';

			if (!file_exists(Yii::getAlias($this->sourcePath . '/' . $filePath))) {
				throw new InvalidConfigException('Cannot find Bugsnag.js source code.  Is bower-asset/bugsnag installed?');
			}
        }

        $this->js[] = [
            $filePath, 
            'data-apikey' => Yii::$app->bugsnag->apiKey,
            'data-releasestage' => Yii::$app->bugsnag->stage,
            'data-appversion' => Yii::$app->version,
            'position' => \yii\web\View::POS_HEAD,
        ];

        // Include this wrapper since bugsnag.js might be blocked by adblockers.  We don't want to completely die if so.
        $js = 'var Bugsnag = Bugsnag || {};';

        if (!Yii::$app->user->isGuest) {
        	$user = Yii::$app->user->identity;
            $userId = Json::htmlEncode($user->id);
            $userName = Json::htmlEncode($user->getFio(false));
            $userEmail = Json::htmlEncode($user->email);
            $js .= "Bugsnag.user = { id: $userId, name: $userName, email: $userEmail };";
        }

        Yii::$app->view->registerJs($js, \yii\web\View::POS_BEGIN);
    }
}
