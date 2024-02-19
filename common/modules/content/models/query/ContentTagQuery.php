<?php

namespace common\modules\content\models\query;

/**
 * This is the ActiveQuery class for [[\common\modules\content\models\ContentTag]].
 *
 * @see \common\modules\content\models\ContentTag
 */
class ContentTagQuery extends \common\modules\base\components\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\ContentTag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\ContentTag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
