<?php
namespace common\modules\qa\models\query;

use yii\db\ActiveQuery;

/**
 * Class QuestionQuery
 * @package common\modules\qa\models
 */
class QuestionQuery extends ActiveQuery
{

    /**
     * @param $limit
     * @return static
     */
    public function views($limit)
    {
        return $this->andWhere('views > :views', [':views' => $limit]);
    }

    /**
     * @return static
     */
    public function published()
    {
        return $this->andWhere(['status' => QuestionInterface::STATUS_PUBLISHED]);
    }

    /**
     * @return static
     */
    public function draft()
    {
        return $this->andWhere(['status' => QuestionInterface::STATUS_DRAFT]);
    }
}
