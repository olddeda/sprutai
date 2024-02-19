<?php

namespace common\modules\dashboard\models\query;

/**
 * This is the ActiveQuery class for [[\common\modules\dashboard\models\Dashboard]].
 *
 * @see \common\modules\dashboard\models\Dashboard
 */
class DashboardQuery extends \common\modules\base\components\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\modules\dashboard\models\Dashboard[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\modules\dashboard\models\Dashboard|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
