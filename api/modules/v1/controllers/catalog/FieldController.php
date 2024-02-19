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

use api\models\catalog\search\CatalogFieldSearch;
use api\models\catalog\CatalogField;
use api\models\catalog\forms\CatalogFieldSwapForm;

/**
 * Class FieldController
 * @package api\modules\v1\controllers
 */
class FieldController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\catalog\CatalogField';

    /**
     * @return array
     */
    public function actions(): array
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['dataFilter'] = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => 'api\models\catalog\search\CatalogFieldSearch',
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
        $updatedAt = $q->from('am_catalog_field')->max('updated_at');
        Yii::$app->response->headers->set("Last-Modified: ".gmdate("D, d M Y H:i:s", $updatedAt)." GMT");

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $searchModel = new CatalogFieldSearch();
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
        return CatalogField::findBy($id, true, 'catalog-field', ['group'], false, $owner);
    }

    /**
     * @OA\Get(
     *     path="/catalogs/fields",
     *     tags={"catalog field"},
     *     summary="Получение списка полей",
     *     description="Возвращает cписок полей",
     *     operationId="catalog_field_list",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Сортировка",
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *             default="id",
     *             enum={"id", "type", "format", "title", "identifier", "status", "created_at", "updated_at"}
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
     *                 enum={"group"}
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
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
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
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
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
     *     path="/catalogs/fields/{id}",
     *     tags={"catalog field"},
     *     summary="Получение данных поля",
     *     description="Возвращает данные поля",
     *     operationId="catalog_field_view",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID поля",
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
     *                 enum={"group"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Поле не найдено"
     *     ),
     * )
     */

    /**
     * @OA\Post(
     *     path="/catalogs/fields",
     *     tags={"catalog field"},
     *     summary="Добавление нового поля",
     *     description="Добавляет и возвращает данные нового поля",
     *     operationId="catalog_field_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogField"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
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
     *     path="/catalogs/fields/{id}",
     *     tags={"catalog field"},
     *     summary="Редактирование поля",
     *     description="Обновляет и возвращает данные измененного поля",
     *     operationId="catalog_field_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogField"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID поля",
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
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
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
     *         description="Поле не найдено"
     *     ),
     * )
     */

    /**
     * @OA\Delete(
     *     path="/catalogs/fields/{id}",
     *     tags={"catalog field"},
     *     summary="Удаление поля",
     *     description="Удаляет поле",
     *     operationId="catalog_field_delete",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID поля",
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
     *         description="Поле не найдено"
     *     ),
     * )
     */

    /**
     * @OA\Post(
     *     path="/catalogs/fields/swap",
     *     tags={"catalog field"},
     *     summary="Замена порядковых номеров местами у полей",
     *     description="Заменяет порядковые номера местами у полей и возвращает новые данные",
     *     operationId="catalog_field_swap",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogFieldSwap"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
     *             ),
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *              @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CatalogField")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     )
     * )
     * @throws InvalidConfigException
     */
    public function actionSwap(): array
    {
        $form = new CatalogFieldSwapForm();
        $form->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($form->validate() && $form->run()) {
            return [
                'items' => [
                    $form->from,
                    $form->to,
                ]
            ];
        }

        return $form;
    }
}
