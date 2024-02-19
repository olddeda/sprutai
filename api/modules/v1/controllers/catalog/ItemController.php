<?php
namespace api\modules\v1\controllers\catalog;

use api\models\catalog\CatalogItem;
use api\models\catalog\search\CatalogItemSearch;
use api\models\content\search\ContentSearch;
use api\models\favorite\Favorite;
use api\models\favorite\search\FavoriteSearch;
use api\models\seo\Seo;
use api\modules\v1\components\actions\IndexAction;
use api\modules\v1\components\ActiveController as Controller;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\content\models\ContentModule;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * Class ItemController
 * @package api\modules\v1\controllers
 */
class ItemController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\catalog\CatalogItem';

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        $tmp = ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['index', 'view', 'slug', 'contents', 'owners'],
            ],
            'access' => [
                'except' => ['index', 'view', 'slug', 'contents', 'owners'],
            ],
        ]);
        return $tmp;
    }

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['index']['dataFilter'] = [
            'class' => 'yii\data\ActiveDataFilter',
            'searchModel' => 'api\models\catalog\search\CatalogItemSearch',
        ];

        $actions['create']['findModel'] = [$this, 'findModel'];
        $actions['update']['findModel'] = [$this, 'findModel'];

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
        $q = new \yii\db\Query();
        $updatedAt = $q->from('am_catalog_item')->max('updated_at');
        Yii::$app->response->headers->set("Last-Modified: ".gmdate("D, d M Y H:i:s", $updatedAt)." GMT");

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var CatalogItemSearch $searchModel */
        $searchModel = new CatalogItemSearch();
        $result = $searchModel->search($requestParams);
        return $result;
    }

    /**
     * @param string $slug
     *
     * @return mixed|null
     * @throws Throwable
     * @throws NotFoundHttpException
     */
    public function actionSlug($slug) {
        $query = CatalogItem::find()->with([
            'media',
            'vendor' => function($query) {
                $query->alias('vendor')->with([
                    'links'
                ]);
            },
            'tags' => function($query) {
                $query->alias('tags')->with([
                    'links' => function ($query) {
                        $query->with([
                            'links'
                        ]);
                    }
                ]);
            },
        ])
            ->joinWith([
                'seoRelation'
            ])
            ->alias('t')
            ->where(Seo::tableName().'.slugify = :slugify', [
                ':slugify' => $slug
            ])
            ->andWhere(['<>', 't.status', Status::DELETED]);

        $model = $query->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('catalog-item', 'error_not_exists'));
        }

        return [
            'item' => $model,
            '_seo' => $model->getSeoFields(),
        ];
    }

    public function actionContents($id) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var ContentSearch $searchModel */
        $searchModel = new ContentSearch();

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $searchModel->search($requestParams);
        $dataProvider->query
            ->joinWith([
                'catalogItems'
            ])
            ->andWhere([
                ContentModule::tableName().'.module_id' => $id,
            ])
            ->groupBy([
                ContentModule::tableName().'.content_id'
            ])
        ;

        return $dataProvider;
    }

    public function actionOwners($id) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $searchModel = new FavoriteSearch();

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $searchModel->search($requestParams);
        $dataProvider->query->andWhere([
            Favorite::tableName().'.module_type' => ModuleType::CATALOG_ITEM,
            Favorite::tableName().'.module_id' => $id,
            Favorite::tableName().'.group_id' => 4,
        ])->groupBy('user_id');

        return $dataProvider;
    }

    /**
     * @param integer $id
     * @param bool $owner
     *
     * @return array|\yii\db\ActiveRecord|null
     */
    public function findModel($id, $owner = false) {
        if (Yii::$app->user->can('Catalog.Moderator')) {
            $owner = false;
        };
        return CatalogItem::findBy($id, true, 'catalog-item', ['tags', 'media'], false, $owner);
    }

    /**
     * @OA\Get(
     *     path="/catalogs/items",
     *     tags={"catalog item"},
     *     summary="Получение списка устройств",
     *     description="Возвращает cписок устройств",
     *     operationId="catalog_item_list",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Сортировка",
     *         explode=false,
     *         @OA\Schema(
     *             type="string",
     *             default="id",
     *             enum={"id", "vendor_id", "title", "model", "url", "comment", "system_manufacturer", "system_model", "status", "created_at", "updated_at"}
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
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
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
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
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
     *     path="/catalogs/items/{id}",
     *     tags={"catalog item"},
     *     summary="Получение данных устройства",
     *     description="Возвращает данные устройства",
     *     operationId="catalog_item_view",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID устройства",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Устройство не найдена"
     *     ),
     * )
     */

    /**
     * @OA\Get(
     *     path="/catalogs/items/slug/{slug}",
     *     tags={"catalog item"},
     *     summary="Получение данных устройства по slug",
     *     description="Возвращает данные устройства",
     *     operationId="catalog_item_view_by_slug",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug устройства",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Устройство не найдено"
     *     ),
     * )
     */

    /**
     * @OA\Post(
     *     path="/catalogs/items",
     *     tags={"catalog item"},
     *     summary="Добавление нового устройства",
     *     description="Добавляет и возвращает данные нового устройства",
     *     operationId="catalog_item_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogItem"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
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
     *     path="/catalogs/items/{id}",
     *     tags={"catalog item"},
     *     summary="Редактирование устройства",
     *     description="Обновляет и возвращает данные измененого устройства",
     *     operationId="catalog_item_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/CatalogItem"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID устройства",
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
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/CatalogItem")
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
     *         description="Устройство не найдено"
     *     ),
     * )
     */

    /**
     * @OA\Delete(
     *     path="/catalogs/items/{id}",
     *     tags={"catalog item"},
     *     summary="Удаление устройства",
     *     description="Удаляет устройство",
     *     operationId="catalog_item_delete",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID устройства",
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
     *         description="Устройство не найдено"
     *     ),
     * )
     */

    /**
     * @OA\Get(
     *     path="/catalogs/items/{id}/contents",
     *     tags={"catalog item"},
     *     summary="Получение списка материалов устройства",
     *     description="Возвращает cписок материалов которые относятся к данному устройству",
     *     operationId="catalog_item_contents",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID устройства",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
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
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Content")
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
     *                 @OA\Items(ref="#/components/schemas/Content")
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
     *     path="/catalogs/items/{id}/owners",
     *     tags={"catalog item"},
     *     summary="Получение списка владельцев устройства",
     *     description="Возвращает cписок пользователей которые владеют данным устройством",
     *     operationId="catalog_item_owners",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID устройства",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
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
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Favorite")
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
     *                 @OA\Items(ref="#/components/schemas/Favorite")
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
}
