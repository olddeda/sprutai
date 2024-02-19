<?php
namespace common\modules\base\behaviors\tree\adjacency;

use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\Status;
use common\modules\content\models\Content;

trait AdjacencyListQueryTrait
{
	private $_tree = [];
	
    /**
     * @return \yii\db\ActiveQuery
     */
    public function roots() {
    	
        /** @var \yii\db\ActiveQuery $this */
        $class = $this->modelClass;
        $model = new $class;
        return $this->andWhere([$model->parentAttribute => 0])->andWhere('status != :status_temp', [':status_temp' => Status::TEMP]);
    }
}
