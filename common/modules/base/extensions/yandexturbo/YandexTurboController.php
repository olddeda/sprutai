<?php
namespace common\modules\base\extensions\yandexturbo;

use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class YandexTurboController
 * @package common\modules\base\extensions\yandexturbo
 */
class YandexTurboController extends Controller
{
    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): string {
        /** @var YandexTurbo $module */
        $module = $this->module;

        Yii::$app->response->format = Response::FORMAT_RAW;

        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/xml');

        return $module->getRssFeed();
    }
}