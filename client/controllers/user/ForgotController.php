<?php
namespace client\controllers\user;

use Yii;

use common\modules\user\controllers\ForgotController as Controller;

/**
 * Controller that manages user authentication process.
 *
 * @property \common\modules\user\Module $module
 */
class ForgotController extends Controller
{
	public $layoutContent = 'content';
	
	public $rememberUrl = false;
	
	public function init() {
		if (!Yii::$app->user->isGuest)
			Yii::$app->user->logout(true);
		parent::init();
	}
}
