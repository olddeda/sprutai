<?php
namespace api\modules\v1\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

use api\components\Controller as BaseController;

/**
 * Class Controller
 * @package api\modules\v1\components
 */
class Controller extends BaseController
{
    /**
     * @var array
     */
    static $filterParams = [];

    /**
     * @return array
     */
    public function actions() {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * @param ActiveRecord $model
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     */
    public function filteredParams(ActiveRecord $model): array {
        $attributes = $model->attributes;
        $filterParams = static::$filterParams;
        $className = (new \ReflectionClass($model))->getShortName();

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $params = [
            $className => [],
        ];
        if (!empty($requestParams)) {
            if (isset($filterParams['query']) && is_array($filterParams['query']) && isset($requestParams['query'])) {
                $queryParams = $this->_queryParam($requestParams['query']);
                if (count($queryParams)) {
                    if (is_array($queryParams)) {
                        $filterParams = static::$filterParams;
                        $className = (new \ReflectionClass($model))->getShortName();
                        foreach ($queryParams as $key => $value) {
                            $key = strtolower($key);
                            if (in_array($key, $filterParams['query']) && ArrayHelper::keyExists($key, $attributes, false)) {
                                $params[$className][$key] = $value;
                            }
                        }
                    }
                }
                unset($requestParams['query']);
            }
            foreach ($requestParams as $key => $value) {
                if( !is_scalar($key)) {
                    throw new BadRequestHttpException('Bad Request');
                }
                $key = strtolower($key);

                if (in_array(strtolower($key), $filterParams) || (in_array(strtolower($key), $filterParams) && ArrayHelper::keyExists($key, $attributes, false))) {
                    $params[$key] = $value;
                }
            }
        }

        return $params;
    }

    /**
     * @param $query
     * @return array|null
     */
    private function _queryParam($query): ?array
    {
        if (is_array($query)) {
            return $query;
        }
        else if (is_scalar($query) && $this->_isJson($query)) {
            return Json::decode($query);
        }
        return [];
    }

    /**
     * @param $string
     *
     * @return bool
     */
    private function _isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}