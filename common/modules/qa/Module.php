<?php
namespace common\modules\qa;

use common\modules\user\models\User;
use Yii;
use yii\base\InvalidCallException;
use yii\helpers\Url;
use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package common\modules\qa
 */
class Module extends BaseModule
{
    /**
     * Translation category for module
     */
    const TRANSLATION = 'qa';

    /**
     * Formatter function for name in user model, or callable
     * @var string|callable
     */
    public $userNameFormatter = 'getId';

    /**
     * Formatter function for date in answer and question models, or callable
     * @var string|callable
     */
    public $dateFormatter;

    /**
     * Alias function for [[Yii::t()]]
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t(self::TRANSLATION.$category, $message, $params, $language);
    }

    /**
     * Alias function for [[Url::toRoute()]]
     * @param $route
     * @param bool $scheme
     * @return string
     */
    public static function url($route, $scheme = false)
    {
        return Url::toRoute($route, $scheme);
    }

    /**
     * @param User $model
     * @param string $attribute
     * @return string
     * @throws InvalidCallException
     */
    public function getUserName($model, $attribute)
    {
        return $this->callConfigFunction($model, 'userNameFormatter', $attribute, function($modelInstance) {
            return $modelInstance->id;
        });
    }

    /**
     * @param $model
     * @param string $attribute
     * @return string
     */
    public function getDate($model, $attribute)
    {
        return $this->callConfigFunction($model, 'dateFormatter', $attribute, function($modelInstance) use($attribute) {
            return Yii::$app->formatter->asRelativeTime($modelInstance->{$attribute});
        });
    }

    /**
     * @param User $model
     * @param string $functionName
     * @param string $attribute
     * @param null $defaultFunction
     * @return string
     */
    protected function callConfigFunction($model, $functionName, $attribute, $defaultFunction = null)
    {
        if (is_callable($this->{$functionName})) {
            return call_user_func($this->{$functionName}, $model, $attribute);
        }
        else if (method_exists($model, $this->{$functionName})) {
            return call_user_func([$model, $this->{$functionName}], $model, $attribute);
        }
        else if ($defaultFunction instanceof \Closure) {
            return $defaultFunction($model, $attribute);
        }
        else throw new InvalidCallException("Invalid {$functionName} function");
    }
}
