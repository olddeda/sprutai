<?php
namespace api\modules\v1\controllers\catalog;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use api\modules\v1\components\ActiveController as Controller;
use api\modules\v1\components\actions\IndexAction;

use api\models\catalog\search\CatalogFieldGroupSearch;
use api\models\catalog\CatalogFieldGroup;

/**
 * Class FieldGroupController
 * @package api\modules\v1\controllers
 */
class FieldGroupController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\catalog\CatalogFieldGroup';

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['dataFilter'] = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => 'api\models\catalog\search\CatalogFieldGroupSearch',
        ];

        $actions['create']['findModel'] = [$this, 'findModel'];
        $actions['update']['findModel'] = [$this, 'findModel'];
        $actions['delete']['findModel'] = [$this, 'findModel'];

        return $actions;
    }

    /**
     * @param IndexAction $action
     * @param $filter
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(IndexAction $action, $filter): ActiveDataProvider
    {
        $q = new Query();
        $updatedAt = $q->from('am_catalog_field_group')->max('updated_at');
        Yii::$app->response->headers->set("Last-Modified: ".gmdate("D, d M Y H:i:s", $updatedAt)." GMT");

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $searchModel = new CatalogFieldGroupSearch();
        return $searchModel->search($requestParams);
    }


    /**
     * @param integer $id
     * @param bool $owner
     *
     * @return array|ActiveRecord|null
     */
    public function findModel(int $id, $owner = false) {
        if (Yii::$app->user->can('Catalog.Moderator')) {
            $owner = false;
        };
        return CatalogFieldGroup::findBy($id, true, 'catalog-field-group', ['fields'], false, $owner);
    }

    /**
     * @OA\Get(
     *     path="/catalogs/fields/groups",
     *     tags={"catalog field group"},
     *     summary="Получение списка групп полей",
     *     description="Возвращает cписок групп полей",
     *     operationId="catalog_field_group_list",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Сортировка",
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *             default="id",
     *             enum={"id", "title", "status", "created_at", "updated_at"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Номер страницы",
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per-page",
     *         in="query",
     *         description="Количество записей на странице",
     *         @OA\Schema(
     *            type="integer",
     *            default=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="expand",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             default="",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"fields"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
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
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
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
     * @OA\Get(
     *     path="/catalogs/fields/groups/{id}",
     *     tags={"catalog field group"},
     *     summary="Получение данных группы полей",
     *     description="Возвращает данные группы полей",
     *     operationId="catalog_field_group_view",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID группы",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="expand",
     *         in="query",
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             default="",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"fields"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа полей не найдена"
     *     ),
     * )
     */

    /**
     * @OA\Post(
     *     path="/catalogs/fields/groups",
     *     tags={"catalog field group"},
     *     summary="Добавление новой группы полей",
     *     description="Добавляет и возвращает данные новой группы полей",
     *     operationId="catalog_field_group_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogFieldGroup"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     )
     * )
     */

    /**
     * @OA\Patch(
     *     path="/catalogs/fields/groups/{id}",
     *     tags={"catalog field group"},
     *     summary="Редактирование группы полей",
     *     description="Обновляет и возвращает данные измененной группы полей",
     *     operationId="catalog_field_group_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogFieldGroup"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID группы полей",
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
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogFieldGroup")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа полей не найдена"
     *     ),
     * )
     */

    /**
     * @OA\Delete(
     *     path="/catalogs/fields/groups/{id}",
     *     tags={"catalog field group"},
     *     summary="Удаление группы полей",
     *     description="Удаляет группу полей",
     *     operationId="catalog_field_group_delete",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID группы полей",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            default="1"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Успешно",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Нет прав",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Группа полей не найдена"
     *     ),
     * )
     */
}
