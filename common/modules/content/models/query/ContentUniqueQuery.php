<?php
namespace common\modules\content\models\query;

use common\modules\base\components\ActiveQuery;

use common\modules\content\helpers\enum\StatusUnique;

/**
 * This is the ActiveQuery class for [[\common\modules\content\models\ContentUnique]].
 *
 * @see \common\modules\content\models\ContentUnique
 */
class ContentUniqueQuery extends ActiveQuery
{
    public function queue() {
        return $this->andWhere(['[[status]]' => StatusUnique::QUEUE]);
    }
	
	public function process() {
		return $this->andWhere(['[[status]]' => StatusUnique::PROCESS]);
	}
	
	public function complete() {
		return $this->andWhere(['[[status]]' => StatusUnique::COMPLETE]);
	}
}
