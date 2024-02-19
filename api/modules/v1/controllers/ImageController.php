<?php
namespace api\modules\v1\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use api\modules\v1\components\Controller;

/**
 * Class ImageController
 * @package api\modules\v1\controllers
 */
class ImageController extends Controller
{

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['fetch'],
            ],
            'access' => [
                'except' => ['fetch'],
            ],
        ]);
    }


	public function actionFetch() {
		return [
		    "success" => "success",
            "body" => Yii::$app->request->get("url"),
        ];
	}
}
