<?php
namespace api\modules\v1\controllers;

use common\modules\base\components\Debug;
use Yii;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;

//use api\modules\v1\components\Controller;
use common\modules\base\components\Controller;

/**
 * Class DefaultController
 * @package api\modules\v1\controllers
 */
class DefaultController extends Controller
{
	public function behaviors() {
		return [];
	}

	public function actionIndex() {
		throw new NotFoundHttpException("Unsuported action request", 100);
	}
}
