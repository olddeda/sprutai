<?php
namespace common\modules\payment\widgets\dashboard;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\Debug;

use common\modules\dashboard\widgets\DashboardWidget;

use common\modules\payment\models\Payment;
use common\modules\payment\helpers\enum\Status;

class CounterPaymentsTotalDashboardWidget extends DashboardWidget {
	
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'counter-payments-total';
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultX() {
		return 0;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultY() {
		return 3;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultWidth() {
		return 3;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultHeight() {
		return 2;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMinWidth() {
		return 3;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMaxWidth() {
		return 3;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMinHeight() {
		return 2;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getMaxHeight() {
		return 1;
	}
	
	/**
	 * @return bool
	 */
	public function getHasBody() {
		return false;
	}
	
	/**
	 * @inheritdoc
	 */
	public function render($view, $params = []) {
		
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.Payment::tableName();
		
		$costs = Yii::$app->db->cache(function ($db) {
			return $db->createCommand('
				SELECT SUM(price)
				FROM '.Payment::tableName().'
				WHERE status = :status
			', [
				':status' => Status::PAID
			])->queryScalar();
		}, Yii::$app->params['cache.duration'], $dependency);
		
		return parent::render($this->getName(), [
			'costs' => $costs,
		]);
	}
}