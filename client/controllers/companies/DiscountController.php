<?php
namespace client\controllers\companies;

use client\components\Controller;

use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

use common\modules\company\models\CompanyDiscount;

class DiscountController extends Controller
{
	
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
	 * List all discounts company
	 * @return string
	 */
	public function actionIndex() {
		
		/** @var \common\modules\company\models\query\CompanyDiscountQuery $query */
		$query = CompanyDiscount::findActive();
		
		$query->orderBy([
			'created_at' => SORT_DESC,
		]);
		
		// Prepare provider
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 100,
			],
		]);
		
		// Render view
		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
	}
}