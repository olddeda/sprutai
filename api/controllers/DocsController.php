<?php
namespace api\controllers;

use common\modules\base\components\Debug;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Class DocsController
 * @package api\controllers
 */
class DocsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'text/html' => Response::FORMAT_HTML
                ]
            ]
        ]);
    }

    /**
     * @return array
     */
    public function actions()
    {
        return [
            'docs' => [
                'class' => 'api\extensions\swagger\ViewAction',
                'apiJsonUrl' => Url::to(['json'], true),
            ],
            'json' => [
                'class' => 'api\extensions\swagger\JsonAction',
                'dirs' => [
                    Yii::getAlias('@api/models'),
                    Yii::getAlias('@api/modules/v1'),
                ],
            ],
        ];
    }

    public function actionTest() {
        Debug::dump($_SERVER);die;
        print_r(Url::toRoute(['docs/json'], true));
    }
}