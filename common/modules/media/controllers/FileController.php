<?php

namespace common\modules\media\controllers;

use common\modules\base\helpers\enum\Boolean;
use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use common\modules\base\components\Controller;

use common\modules\media\models\MediaFormat;
use common\modules\media\models\search\MediaFormatSearch;

/**
 * FileController implements the CRUD actions for File model.
 */
class FileController extends Controller
{
	/**
	 * Lists all MediaFormat models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new MediaFormatSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}