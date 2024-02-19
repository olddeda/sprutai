<?php
namespace api\extensions\swagger;

use Yii;
use yii\base\Action;
use yii\web\Response;

use Symfony\Component\Finder\Finder;

/**
 * Class JsonAction
 * @package api\extenstions\swagger
 */
class JsonAction extends Action
{
    /**
     * @var string|array|Finder directory(s) or filename(s) with open api annotations.
     */
    public $dirs;

    /**
     * @var array
     *   exclude: string|array $exclude The directory(s) or filename(s) to exclude (as absolute or relative paths)
     *   analyser: defaults to StaticAnalyser
     *   analysis: defaults to a new Analysis
     *   processors: defaults to the registered processors in Analysis
     */
    public $scanOptions = [];

    /**
     * @inheritdoc
     */
    public function run() {
        $this->initCors();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return \OpenApi\scan($this->dirs, $this->scanOptions);
    }

    /**
     * Init cors.
     */
    protected function initCors() {
        $headers = Yii::$app->getResponse()->getHeaders();

        $headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $headers->set('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT');
        $headers->set('Access-Control-Allow-Origin', '*');
        $headers->set('Allow', 'OPTIONS,HEAD,GET');
    }
}
