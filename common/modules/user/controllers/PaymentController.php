<?php
namespace common\modules\user\controllers;

use common\modules\user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Controller;

use common\modules\payment\models\Payment;
use common\modules\payment\models\search\PaymentSearch;
use common\modules\payment\helpers\enum\StatusUser;


class PaymentController extends Controller
{
	
	/**
	 * Lists all Payments models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionIndex(int $id = 0) {
		if (!$id || ($id && ($id != Yii::$app->user->id && !Yii::$app->user->getIsAdmin())))
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		/** @var PaymentSearch $searchModel */
		$searchModel = new PaymentSearch();
		
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			Payment::tableName().'.user_id' => $id,
		]);
		$dataProvider->pagination = ['pageSize' => 50];
		
		return $this->render('index', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}
	
	/**
	 * List all accruals Payments models.
	 * @param int $id
	 *
	 * @return string
	 */
	public function actionAccruals(int $id = 0) {
		if (!$id || ($id && ($id != Yii::$app->user->id && !Yii::$app->user->getIsAdmin())))
			$id = Yii::$app->user->id;
		
		/** @var User $model */
		$model = User::findById($id, true, 'user');
		
		/** @var PaymentSearch $searchModel */
		$searchModel = new PaymentSearch();
		
		/** @var ActiveDataProvider $dataProvider */
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			Payment::tableName().'.to_user_id' => $id,
		]);
		$dataProvider->query->andWhere(['in', Payment::tableName().'.status', [StatusUser::PAID, StatusUser::COMPLETED]]);
		$dataProvider->pagination = ['pageSize' => 50];
		
		return $this->render('accruals', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	
	}
}