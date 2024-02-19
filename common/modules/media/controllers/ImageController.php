<?php

namespace common\modules\media\controllers;

use Yii;

use common\modules\base\components\Controller;

use common\modules\media\models\MediaImage;
use common\modules\media\models\search\MediaImageSearch;

class ImageController extends Controller
{

	/**
	 * Lists all MediaImage models.
	 * @return mixed
	 */
	public function actionIndex() {
		$searchModel = new MediaImageSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single MediaImage model.
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {

		// Render view
		return $this->render('view', [
			'model' => MediaImage::findOwn($id, true, 'media'),
		]);
	}
}