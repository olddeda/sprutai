<?php
namespace api\modules\v1\controllers\comment;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use api\modules\v1\components\ActiveController as Controller;
use api\modules\v1\components\actions\IndexAction;

use api\models\comment\Comment;
use api\models\comment\search\CommentSearch;
use yii\web\ServerErrorHttpException;

/**
 * Class DefaultController
 * @package api\modules\v1\controllers\comment
 */
class DefaultController extends Controller
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = 'api\models\comment\Comment';

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['index'],
            ],
            'access' => [
                'except' => ['index'],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['dataFilter'] = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => 'api\models\comment\search\CommentSearch',
        ];

        $actions['create']['findModel'] = [$this, 'findModel'];
        $actions['update']['findModel'] = [$this, 'findModel'];
        //$actions['delete']['findModel'] = [$this, 'findModel'];

        unset($actions['delete']);

        return $actions;
    }

    /**
     * @param IndexAction $action
     * @param $filter
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(IndexAction $action, $filter) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        if (isset($requestParams['module_type'])) {
            unset($requestParams['module_type']);
        }
        if (isset($requestParams['module_id'])) {
            unset($requestParams['module_id']);
        }

        $moduleType = Yii::$app->getRequest()->get('module_type');
        $moduleId = Yii::$app->getRequest()->get('module_id');

        /** @var CommentSearch $searchModel */
        $searchModel = new CommentSearch();
        return $searchModel->search($moduleType, $moduleId, $requestParams);
    }

    public function actionDelete($id) {
        $model = $this->findModel($id, true);

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        return $model;
    }

    /**
     * @param integer $id
     * @param bool $owner
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findModel($id, $owner = false) {
        return Comment::findBy($id, true, 'comments', [], false, $owner, null, [], function($query) {
            return $query->votes();
        });
    }
    
    /**
     * @OA\Get (path="/comments/{module_type}/{module_id}",
     *     tags={"comment"},
     *     summary="Получение списка комментариев",
     *     operationId="comments",
     *     @OA\Parameter(
     *         name="module_type",
     *         in="path",
     *         description="Тип модуля",
     *         required=true,
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             default="40",
     *             @OA\Items(
     *                 type="integer",
     *                 enum = {"40", "81"},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="module_id",
     *         in="path",
     *         description="ID модуля",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Номер страницы",
     *         @OA\Schema(
     *            type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Количество записей на странице",
     *         @OA\Schema(
     *            type="integer",
     *            default=100
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             ),
     *             @OA\Property(
     *                 property="_links",
     *                 @OA\Items(ref="#/components/schemas/Links")
     *             ),
     *             @OA\Property(
     *                 property="_meta",
     *                 @OA\Items(ref="#/components/schemas/Meta")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             ),
     *             @OA\Property(
     *                 property="_links",
     *                 @OA\Items(ref="#/components/schemas/Links")
     *             ),
     *             @OA\Property(
     *                 property="_meta",
     *                 @OA\Items(ref="#/components/schemas/Meta")
     *             )
     *         )
     *     )
     * )
     */

    /**
     * @OA\Post(
     *     path="/comments",
     *     tags={"comment"},
     *     summary="Добавление нового комментария",
     *     description="Добавляет и возвращает новый комментарий",
     *     operationId="comment_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/Comment"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Нет прав"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/comments/{id}",
     *     tags={"comment"},
     *     summary="Редактирование комментария",
     *     description="Обновляет и возвращает данные измененого комментария",
     *     operationId="comment_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/Comment"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID комментария",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            default="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Нет прав"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Комментарий не найден"
     *     )
     * )
     */

    /**
     * @OA\Delete(
     *     path="/comments/{id}",
     *     tags={"comment"},
     *     summary="Удаление комментария",
     *     description="Удаляет комментарий",
     *     operationId="comment_delete",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID комментария",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            default="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Comment")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Нет прав"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Комментарий не найден"
     *     )
     * )
     */
}