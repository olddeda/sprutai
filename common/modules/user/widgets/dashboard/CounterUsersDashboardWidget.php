<?php
namespace common\modules\user\widgets\dashboard;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\dashboard\widgets\DashboardWidget;

use common\modules\user\models\User;

class CounterUsersDashboardWidget extends DashboardWidget {
	
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'counter-users';
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
		return 0;
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
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.User::tableName();
		
		$count = Yii::$app->db->cache(function ($db) {
			return $db->createCommand('
				SELECT COUNT(DISTINCT id)
				FROM '.User::tableName().'
				WHERE confirmed_at IS NOT NULL
			', [':status' => Status::ENABLED])->queryScalar();
		}, Yii::$app->params['cache.duration'], $dependency);
		
		return parent::render($this->getName(), [
			'count' => $count,
		]);
	}
}