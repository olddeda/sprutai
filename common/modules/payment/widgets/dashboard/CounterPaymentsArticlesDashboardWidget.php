<?php
namespace common\modules\payment\widgets\dashboard;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\dashboard\widgets\DashboardWidget;

use common\modules\payment\models\Payment;
use common\modules\payment\helpers\enum\Status;

use common\modules\content\helpers\enum\Type;
use common\modules\content\models\Content;

class CounterPaymentsArticlesDashboardWidget extends DashboardWidget {
	
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'counter-payments-articles';
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultX() {
		return 3;
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
				SELECT SUM(p.price)
				FROM '.Payment::tableName().' AS p
				LEFT JOIN '.Content::tableName().' AS c
				ON c.id = p.module_id
				WHERE p.status = :status
				AND p.module_type = :module_type
				AND c.type = :type
			', [
				':status' => Status::PAID,
				':module_type' => ModuleType::CONTENT,
				':type' => Type::ARTICLE,
			])->queryScalar();
		}, Yii::$app->params['cache.duration'], $dependency);
		if (!$costs)
			$costs = 0;
		
		return parent::render($this->getName(), [
			'costs' => $costs,
		]);
	}
}