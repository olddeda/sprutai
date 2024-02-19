<?php
namespace api\modules\v1\controllers\content;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\content\helpers\enum\Status as ContentStatus;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\models\ContentModule;
use common\modules\rbac\helpers\enum\Role;
use common\modules\seo\models\Seo;

use api\modules\v1\components\actions\IndexAction;
use api\modules\v1\components\ActiveController as Controller;

use api\models\catalog\search\CatalogItemSearch;

use api\models\content\Content;
use api\models\content\search\ContentSearch;

/**
 * Class DefaultController
 * @package api\modules\v1\controllers\content
 */
class DefaultController extends Controller
{
	/**
	 * @var string the model class name. This property must be set.
	 */
	public $modelClass = 'api\models\content\Content';
    
    /**
     * @var array
     */
	static $filterParams = ['sort', 'type', 'query' => [
	    'id', 'type', 'title', 'status', 'date_at'
    ]];
    
    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['index', 'view', 'view-slug', 'items', 'counts'],
            ],
            'access' => [
                'except' => ['index', 'view', 'view-slug', 'items', 'counts'],
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
            'searchModel' => 'api\models\content\search\ContentSearch',
        ];

        $actions['create']['findModel'] = [$this, 'findModel'];
        $actions['update']['findModel'] = [$this, 'findModel'];
        $actions['delete']['findModel'] = [$this, 'findModel'];

        unset($actions['view']);

        return $actions;
    }

    /**
     * @param IndexAction $action
     * @param $filter
     *
     * @return array
     * @throws InvalidConfigException
     */
    public function prepareDataProvider(IndexAction $action, $filter) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var ContentSearch $searchModel */
        $searchModel = new ContentSearch();

        $seo = [];
        $sort = Yii::$app->request->get('sort');
        if ($sort == 'popular') {
            $seo['title'] = 'Популярные материалы';
        }
        else if ($sort == 'discussed') {
            $seo['title'] = 'Обсуждаемые материалы';
        }
        else {
            $seo['title'] = 'Последние материалы';
        }
        $page = Yii::$app->request->get('page', 1);
        if ($page > 1) {
            $seo['title'].= ' - страница '.$page;
        }

        return [
            'items' => $searchModel->search($requestParams),
            '_seo' => $seo,
        ];
    }

    public function actionAdmin() {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var ContentSearch $searchModel */
        $searchModel = new ContentSearch();
        $searchModel->isModeAdmin = true;

        $seo = [];
        $sort = Yii::$app->request->get('sort');
        if ($sort == 'popular') {
            $seo['title'] = 'Популярные материалы';
        }
        else if ($sort == 'discussed') {
            $seo['title'] = 'Обсуждаемые материалы';
        }
        else {
            $seo['title'] = 'Последние материалы';
        }
        $page = Yii::$app->request->get('page', 1);
        if ($page > 1) {
            $seo['title'].= ' - страница '.$page;
        }

        $dataProvider = $searchModel->search($requestParams);

        if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
            $dataProvider->query->andWhere([
                Content::tableName().'.author_id' => Yii::$app->user->id
            ]);
        }

        return [
            'items' => $dataProvider,
            '_seo' => $seo,
        ];
    }

    /**
     * @OA\Get (path="/contents",
     *     tags={"content"},
     *     summary="Получение списка постов",
     *     operationId="contents_list",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=false,
     *          description="Authorization",
     *          @OA\Schema(
     *            type="string",
     *            default="Bearer "
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Сортировка",
     *         explode=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"newest", "popular", "discussed"}
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
     *             default="40",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"text", "seo"}
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
     * @OA\Get (path="/contents/{id}",
     *     tags={"content"},
     *     summary="Получение одного материала",
     *     operationId="contents_view",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=false,
     *          description="Authorization",
     *          @OA\Schema(
     *            type="string",
     *            default="Bearer "
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID материала",
     *         required=true,
     *         @OA\Schema(
     *            type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="expand",
     *         in="query",
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="40",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"text", "seo"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Материал не найден"
     *     )
     * )
     * @param $id
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionView($id) {
        return $this->findModelBy('id', $id);
    }

    /**
     * @OA\Get (path="/contents/slug/{slug}",
     *     tags={"content"},
     *     summary="Получение одного материала по slug",
     *     operationId="contents_view_slug",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=false,
     *          description="Authorization",
     *          @OA\Schema(
     *            type="string",
     *            default="Bearer "
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Slug материала",
     *         required=true,
     *         @OA\Schema(
     *            type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="expand",
     *         in="query",
     *         explode=true,
     *         @OA\Schema(
     *             type="array",
     *             default="40",
     *             @OA\Items(
     *                 type="string",
     *                 enum={"text", "seo"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пост не найден"
     *     )
     * )
     * @param $slug
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionViewSlug($slug) {
        return $this->findModelBy('slug', $slug);
    }

    /**
     * @OA\Post(path="/contents",
     *     tags={"content"},
     *     summary="Добавление нового материала",
     *     description="Добавляет и возвращает данные нового материала",
     *     operationId="content_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/ContentForm"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
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
     * @OA\Patch(path="/contents/{id}",
     *     tags={"content"},
     *     summary="Редактирование материала",
     *     description="Обновляет и возвращает данные измененого материала",
     *     operationId="content_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/ContentForm"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID материала",
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
     *                 @OA\Items(ref="#/components/schemas/Content")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Content")
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
     *         description="Материал не найден"
     *     ),
     * )
     */

    /**
     * @OA\Delete(path="/contents/{id}",
     *     tags={"content"},
     *     summary="Удаление материала",
     *     description="Удаляет материал",
     *     operationId="content_delete",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID материала",
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
     *         description="Материал не найден"
     *     ),
     * )
     */

    /**
     * @OA\Get(path="/contents/{id}/items",
     *     tags={"content"},
     *     summary="Получение списка устройств материала",
     *     description="Возвращает cписок устройств которые есть в материале",
     *     operationId="contents_items",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID материала",
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
	public function actionItems($id) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var CatalogItemSearch $searchModel */
        $searchModel = new CatalogItemSearch();

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $searchModel->search($requestParams);
        $dataProvider->query
            ->joinWith(['contentModule'])
            ->andWhere([
                ContentModule::tableName().'.content_id' => $id,
            ]);

        return $dataProvider;
    }

    /**
     * @OA\Get(path="/contents/counts",
     *     tags={"content"},
     *     summary="Получение количества материалов",
     *     description="Возращает количество материалов для разных типов",
     *     operationId="content_counts",
     *     security={{"Bearer": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(ref="#/components/schemas/ContentCount"),
     *         @OA\XmlContent(ref="#/components/schemas/ContentCount")
     *     )
     * )
     */
    public function actionCounts() {
	    $result = (new Query())
            ->select([
                '(
                    SELECT COUNT(*) 
                    FROM '.Content::tableName().' 
                    WHERE type IN ('.ContentType::NEWS.','.ContentType::ARTICLE.','.ContentType::BLOG.', '.ContentType::VIDEO.')
                    AND status = '.ContentStatus::ENABLED.'
                 ) AS total',
                '(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::NEWS.' AND status = '.ContentStatus::ENABLED.') AS news',
                '(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::ARTICLE.' AND status = '.ContentStatus::ENABLED.') AS articles',
                '(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::BLOG.' AND status = '.ContentStatus::ENABLED.') AS blogs',
                //'(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::PROJECT.' AND status = '.ContentStatus::ENABLED.') AS projects',
                //'(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::PLUGIN.' AND status = '.ContentStatus::ENABLED.') AS plugins',
                //'(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::PORTFOLIO.' AND status = '.ContentStatus::ENABLED.') AS portfolios',
                '(SELECT COUNT(*) FROM '.Content::tableName().' WHERE type = '.ContentType::VIDEO.' AND status = '.ContentStatus::ENABLED.') AS videos',
            ])
            ->one();
        ;
        return $result;
    }

    /**
     * @param integer $id
     * @param bool $owner
     *
     * @return array|ActiveRecord|null
     * @throws NotFoundHttpException
     */
    public function findModel($id, $owner = false) {
        $result = $this->findModelBy('id', $id);
        return $result['item'];
    }

    /**
     * @param $field
     * @param $value
     *
     * @return array
     * @throws NotFoundHttpException
     */
    private function findModelBy($field, $value) {
        $query = Content::find()->joinWith([
            'media',
            'seoRelation',
            'statistics',
            'stat',
            'tags',
            'company' => function($query) {
                $query->where([]);
            },
            'author' => function($query) {
                $query->joinWith('profile');
            },
            'contentModuleCatalogItems'
        ])->where([
        ])->andWhere([
            'in',
            Content::tableName().'.type',
            [
                ContentType::PAGE,
                ContentType::NEWS,
                ContentType::ARTICLE,
                ContentType::BLOG,
                ContentType::PLUGIN,
                ContentType::PROJECT,
                ContentType::PORTFOLIO,
                ContentType::EVENT,
                ContentType::VIDEO,
            ],
        ])->orderBy([
            'date_at' => SORT_DESC,
        ])->votes();

        if ($field == 'slug') {
            $query->andWhere([
                Seo::tableName().'.slugify' => $value,
            ]);
        }
        else {
            $query->andWhere([
                Content::tableName().'.id' => $value
            ]);
        }

        $model = $query->one();
        if (!$model) {
            throw new NotFoundHttpException(Yii::t('content', 'error_not_exists'));
        }

        if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
            if ($model->status != ContentStatus::ENABLED) {
                if ($model->author_id != Yii::$app->user->id) {
                    throw new NotFoundHttpException(Yii::t('content', 'error_not_exists'));
                }
                else {
                    if ($model->status == ContentStatus::MODERATED) {
                        throw new NotFoundHttpException(Yii::t('content', 'error_moderated'));
                    }
                }
            }
        }

        return [
            'item' => $model,
            '_seo' => $model->getSeoFields(),
        ];
    }
}