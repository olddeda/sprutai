<?php
namespace common\modules\vote\traits;

use common\modules\vote\Module;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Trait ModuleTrait
 * @package common\modules\vote\traits
 */
trait ModuleTrait
{
    /**
     * @return \common\modules\vote\Module
     * @throws InvalidConfigException
     */
    public static function getModule() {
        if (Yii::$app->hasModule('vote') && ($module = Yii::$app->getModule('vote')) instanceof Module) {
            return $module;
        }
        throw new InvalidConfigException('Module "vote" is not set.');
    }
}
