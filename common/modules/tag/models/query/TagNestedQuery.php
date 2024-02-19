<?php
namespace common\modules\tag\models\query;

use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveQuery;
use common\modules\base\behaviors\tree\nestedsets\NestedSetsQueryBehavior;

/**
 * This is the ActiveQuery class for [[\common\modules\tag\models\TagNested]].
 *
 * @see \common\modules\tag\models\TagNested
 */
class TagNestedQuery extends ActiveQuery
{
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			NestedSetsQueryBehavior::className(),
		]);
	}
}
