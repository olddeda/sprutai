<?php
namespace common\modules\queues\records;

use yii\db\ActiveQuery;

/**
 * Class ExecQuery
 * @package common\modules\queues\records
 */
class ExecQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->alias('exec');
    }

    /**
     * @inheritdoc
     * @return ExecRecord[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ExecRecord|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }
}
