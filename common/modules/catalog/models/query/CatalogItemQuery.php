<?php
namespace common\modules\catalog\models\query;

use common\modules\base\components\ActiveQuery;
use common\modules\base\components\ArrayHelper;

use common\modules\vote\behaviors\VoteQueryBehavior;

use common\modules\catalog\models\CatalogItem;

/**
 * This is the ActiveQuery class for [[\common\modules\catalog\models\CatalogItem]].
 *
 * @see \common\modules\catalog\models\CatalogItem
 */
class CatalogItemQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(),[
            [
                'class' => VoteQueryBehavior::class,
            ],
        ]);
    }
}
