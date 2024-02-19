<?php
namespace common\modules\payment\controllers;

use Yii;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\payment\models\search\PaymentSearch;
use common\modules\payment\helpers\enum\Kind;
use common\modules\payment\models\Payment;


class DefaultController extends Controller
{
	
	public function actionIndex() {
		$searchModel = new PaymentSearch();
		
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			Payment::tableName().'.kind' => Kind::ACCRUAL
		]);
		
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
}