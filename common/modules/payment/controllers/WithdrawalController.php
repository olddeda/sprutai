<?php
namespace common\modules\payment\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

use common\modules\base\components\Controller;
use common\modules\base\extensions\editable\EditableAction;
use common\modules\base\components\Debug;

use common\modules\payment\models\search\PaymentSearch;
use common\modules\payment\helpers\enum\Kind;
use common\modules\payment\models\Payment;

use common\modules\user\models\User;


class WithdrawalController extends Controller
{
	
	/**
	 * @inheritdoc
	 */
	public function actions() {
		return ArrayHelper::merge(parent::actions(), [
			'editable' => [
				'class' => EditableAction::class,
				'modelClass' => Payment::class,
			],
		]);
	}
	
	/**
	 * Lists all Payment withdrawal models.
	 *
	 * @return string
	 * @throws \yii\db\Exception
	 */
	public function actionIndex() {
		
		// Get all months
		$tmp = Yii::$app->db->createCommand('
			SELECT DATE_FORMAT(FROM_UNIXTIME(date_at), \'%m-%Y\')
			FROM '.Payment::tableName().'
			WHERE kind = :kind
			GROUP BY DATE_FORMAT(FROM_UNIXTIME(date_at), \'%m-%Y\')
			ORDER BY date_at DESC
		', [
			':kind' => Kind::WITHDRAWAL
		])->queryColumn();
		$months = [];
		foreach ($tmp as $month)
			$months[$month] = $month;
		
		// Get selected month or set default
		$currentMonth = Yii::$app->request->get('month', current($months));
		
		// Create search model and data provider
		$searchModel = new PaymentSearch();
		
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->joinWith([
			'user',
		]);
		$dataProvider->query->andWhere(Payment::tableName().'.kind = :kind AND DATE_FORMAT(FROM_UNIXTIME('.Payment::tableName().'.date_at), \'%m-%Y\') = :date', [
			':kind' => Kind::WITHDRAWAL,
			':date' => $currentMonth
		]);
		
		// Render view
		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'months' => $months,
			'currentMonth' => $currentMonth,
		]);
	}
	
	/**
	 * Displays a single Payment model.
	 * @param integer $id
	 *
	 * @return string
	 * @throws NotFoundHttpException
	 */
	public function actionView($id) {
		
		$model = Payment::findById($id, true, 'payment');
		
		$dataProvider = new ActiveDataProvider([
			'query' => $model->getWithdrawals()->joinWith([
				'paymentSource',
			])->orderBy([
				Payment::tableName().'.date_at' => SORT_DESC
			]),
			'pagination' => [
				'pageSize' => 50,
			],
			'sort' => false,
		]);
		
		// Render view
		return $this->render('view', [
			'model' => $model,
			'dataProvider' => $dataProvider,
		]);
	}
}