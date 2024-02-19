<?php
namespace api\extensions\swagger;

use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * Class ViewAction
 * @package api\extenstions\swagger
 */
class ViewAction extends Action
{
    /**
     * @var string
     * Open Api Swagger Json URL
     */
    public $apiJsonUrl;

    /**
     * Action runner
     * @return string
     */
    public function run() {
        Yii::$app->getResponse()->format = Response::FORMAT_HTML;

        return $this->controller->view->renderFile(__DIR__ . '/view.php', [
            'apiJsonUrl' => $this->apiJsonUrl,
        ], $this->controller);
    }
}
