<?php
namespace common\modules\dashboard\controllers;

use common\modules\base\helpers\enum\Status;
use common\modules\dashboard\models\Dashboard;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

/**
 * DefaultController implements the CRUD actions for Dashboard model.
 */
class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'save' => ['post'],
				],
			],
		]);
	}
	
	/**
	 * Show dashboard
	 * @return mixed
	 */
	public function actionIndex() {
		
		// Render view
		return $this->render('index', []);
	}
	
	/**
	 * Save dashboard
	 */
	public function actionSave() {
		$data = Yii::$app->request->post('data', []);
		if (is_array($data)) {
			foreach ($data as $item) {
				$model = Dashboard::find()->andWhere([
					'name' => $item['name'],
					'user_id' => Yii::$app->user->id,
				])->one();
				if (is_null($model)) {
					$model = new Dashboard();
					$model->user_id = Yii::$app->user->id;
					$model->name = $item['name'];
					$model->status = Status::ENABLED;
				}
				$model->width = $item['width'];
				$model->height = $item['height'];
				$model->x = $item['x'];
				$model->y = $item['y'];
				$model->save();
			}
		}
	}
}