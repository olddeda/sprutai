<?php

namespace common\modules\media\models\query;

/**
 * This is the ActiveQuery class for [[\common\modules\media\models\MediaFormat]].
 *
 * @see \common\modules\media\models\MediaFormat
 */
class MediaFormatQuery extends \common\modules\base\components\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \common\modules\media\models\MediaFormat[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\modules\media\models\MediaFormat|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}