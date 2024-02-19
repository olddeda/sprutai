<?php
namespace common\modules\maintenance\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use common\modules\base\components\Controller;

/**
 * Class MaintenanceController
 * @package common\maintenance\controllers
 */
class MaintenanceController extends Controller
{
    /**
     * Initialize controller.
     */
    public function init() {
        $this->layout = Yii::$app->maintenance->layoutPath;
        parent::init();
    }
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index'],
						'roles' => ['?', '@'],
					],
				],
			],
    	]);
	}

    /**
     * Index action.
     * @return bool|string
     */
    public function actionIndex() {
        $app = Yii::$app;

        if ($app->getRequest()->getIsAjax()) {
            return false;
        }

        return $this->render($app->maintenance->viewPath, [
            'title' => $app->maintenance->title,
            'message' => $app->maintenance->message
        ]);
    }
} 