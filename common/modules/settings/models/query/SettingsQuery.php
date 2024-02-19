<?php

namespace common\modules\settings\models\query;

use common\modules\base\components\ActiveQuery;
use common\modules\base\helpers\enum\Status;

/**
 * Class SettingsQuery
 * @package yii2mod\settings\models
 */
class SettingsQuery extends ActiveQuery
{
    /**
     * Scope for settings with enabled status
     * @return $this
     */
    public function enabled() {
        $this->andWhere(['status' => Status::ENABLED]);
        return $this;
    }

    /**
     * Scope for settings with disabled status
     * @return $this
     */
    public function disabled() {
        $this->andWhere(['status' => Status::DISABLED]);
        return $this;
    }
}