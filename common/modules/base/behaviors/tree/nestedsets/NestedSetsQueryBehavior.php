<?php
namespace common\modules\base\behaviors\tree\nestedsets;

use yii\base\Behavior;
use yii\db\Expression;

/**
 * NestedSetsQueryBehavior
 *
 * @property \yii\db\ActiveQuery $owner
 */
class NestedSetsQueryBehavior extends Behavior
{
	/**
	 * Gets the root nodes.
	 * @return \yii\db\ActiveQuery the owner
	 */
	public function roots() {
		$model = new $this->owner->modelClass();
		
		$this->owner
			->andWhere([$model->leftAttribute => 1])
			->addOrderBy([$model->primaryKey()[0] => SORT_ASC]);
		
		return $this->owner;
	}
	
	/**
	 * Gets the leaf nodes.
	 * @return \yii\db\ActiveQuery the owner
	 */
	public function leaves() {
		$model = new $this->owner->modelClass();
		$db = $model->getDb();
		
		$columns = [$model->leftAttribute => SORT_ASC];
		
		if ($model->treeAttribute !== false) {
			$columns = [$model->treeAttribute => SORT_ASC] + $columns;
		}
		
		$this->owner
			->andWhere([$model->rightAttribute => new Expression($db->quoteColumnName($model->leftAttribute) . '+ 1')])
			->addOrderBy($columns);
		
		return $this->owner;
	}
}