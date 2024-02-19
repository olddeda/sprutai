<?php
namespace common\modules\menu\models\query;

use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveQuery;
use common\modules\base\behaviors\tree\nestedsets\NestedSetsQueryBehavior;

/**
 * This is the ActiveQuery class for [[\common\modules\menu\models\MenuNested]].
 *
 * @see \common\modules\tag\models\TagNested
 */
class MenuNestedQuery extends ActiveQuery
{
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			NestedSetsQueryBehavior::class,
		]);
	}
}
