<?php
namespace common\modules\content\widgets\dashboard;

use Yii;
use yii\caching\DbDependency;

use common\modules\base\components\Debug;

use common\modules\dashboard\widgets\DashboardWidget;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Type;
use common\modules\content\helpers\enum\Status;

class CounterNewsDashboardWidget extends DashboardWidget {
	
	/**
	 * @inheritdoc
	 */
	public function getName() {
		return 'counter-news';
	}
	
	/**
	 * @inheritdoc
	 */
	public function getDefaultX() {
		return 6;
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
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.Content::tableName().' WHERE type = '.Type::NEWS;
		
		$count = Yii::$app->db->cache(function ($db) {
			return $db->createCommand('
				SELECT COUNT(DISTINCT id)
				FROM '.Content::tableName().'
				WHERE status = :status
				AND type = :type
			', [
				':status' => Status::ENABLED,
				':type' => Type::NEWS,
			])->queryScalar();
		}, Yii::$app->params['cache.duration'], $dependency);
		
		$countModerated = Yii::$app->db->cache(function ($db) {
			return $db->createCommand('
				SELECT COUNT(DISTINCT id)
				FROM '.Content::tableName().'
				WHERE status = :status
				AND type = :type
			', [
				':status' => Status::MODERATED,
				':type' => Type::NEWS,
			])->queryScalar();
		}, Yii::$app->params['cache.duration'], $dependency);
		
		return parent::render($this->getName(), [
			'count' => $count,
			'countModerated' => $countModerated,
		]);
	}
}