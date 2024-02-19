<?php
namespace common\modules\base\behaviors\tree\nestedsets;

trait NestedSetsQueryTrait
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function roots() {
    	
        /** @var \yii\db\ActiveQuery $this */
        $class = $this->modelClass;
        $model = new $class;
        return $this->andWhere([$model->leftAttribute => 1]);
    }
}
