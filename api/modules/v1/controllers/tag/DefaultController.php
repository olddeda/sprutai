<?php
namespace api\modules\v1\controllers\tag;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use api\modules\v1\components\Controller;

use api\models\tag\Tag;
use api\models\tag\search\TagSearch;

/**
 * Class DefaultController
 * @package api\modules\v1\controllers\tag
 */
class DefaultController extends Controller
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = 'api\models\tag\Tag';

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
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['index'],
            ],
            'access' => [
                'except' => ['index'],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                ],
            ],
            [
                'class' => 'yii\filters\PageCache',
                'only' => ['index'],
                'duration' => 3600 * 365,
                'variations' => [
                    Yii::$app->request->get('query'),
                    Yii::$app->request->get('page'),
                    Yii::$app->request->get('per-page'),
                    Yii::$app->request->get('sort'),
                    Yii::$app->request->get('fields')
                ],
                'dependency' => [
                    'class' => 'yii\caching\DbDependency',
                    'sql' => 'SELECT MAX(updated_at) FROM '.Tag::tableName(),
                ],
            ],
        ]);
    }
    
    /**
     * @OA\Get (
     *     path="/tags",
     *     tags={"tag"},
     *     summary="Получение списка тегов",
     *     operationId="tags_list",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=false,
     *          description="Authorization",
     *          @OA\Schema(
     *            type="string",
     *            default="Bearer "
     *          )
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
     *                 @OA\Items(ref="#/components/schemas/Tag")
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
     *                 @OA\Items(ref="#/components/schemas/Tag")
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
    public function actionIndex($type = null) {
        $searchModel = new TagSearch();

        /** @var ActiveDataProvider $searchParams */
        $searchParams = $this->filteredParams($searchModel);

        $items = $searchModel->search($searchParams);

        return [
            'items' => $items,
            '_seo' => [],
            '_metaClear' => true
        ];
    }

    public function actionCreate() {
        print_r(Yii::$app->getRequest()->getBodyParams());
    }
}