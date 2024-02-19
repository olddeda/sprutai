<?php
namespace api\modules\v1\controllers\telegram;

use api\models\tag\search\TagSearch;
use api\models\tag\Tag;
use api\models\telegram\TelegramChat;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\tag\models\TagModule;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use api\modules\v1\components\ActiveController as Controller;
use api\modules\v1\components\actions\IndexAction;

use api\models\telegram\search\TelegramChatSearch;

/**
 * Class ChatController
 * @package api\modules\v1\controllers\telegram
 */
class ChatController extends Controller
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = 'api\models\telegram\TelegramChat';

    /**
     * @var array
     */
    static $filterParams = ['sort', 'type', 'query' => [
        'id', 'title', 'username', 'status', 'date_at'
    ]];

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['index', 'tags'],
            ],
            'access' => [
                'except' => ['index', 'tags'],
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
            'searchModel' => 'api\models\telegram\search\TelegramChatSearch',
        ];

        unset($actions['create']);
        unset($actions['update']);
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
        return $this->_index();
    }
    
    /**
     * @OA\Get (
     *     path="/telegrams/chats",
     *     tags={"telegram-chat"},
     *     summary="Получение списка частов телеграмма",
     *     operationId="telegrams_chats_list",
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
     *                 @OA\Items(ref="#/components/schemas/TelegramChat")
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
     *                 @OA\Items(ref="#/components/schemas/TelegramChat")
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

    public function actionAdmin() {
        return $this->_index(true);
    }

    public function actionTags() {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        $search = new Tag();
        $search->load(ArrayHelper::getValue($requestParams, 'filter', []), '');

        $query = Tag::find()
            ->alias('c')
            ->joinWith('tagModules')
            ->leftJoin(TelegramChat::tableName(), TelegramChat::tableName().'.id = tm.module_id')
            ->where('c.status NOT IN (0, 3)')
            ->andWhere(['tm.module_type' => ModuleType::NONE])
            ->andWhere(['IS NOT', TelegramChat::tableName().'.id', NULL])
            ->andFilterWhere(['like', 'c.title', $search->title])
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'params' => Yii::$app->request->queryParams,
                'defaultOrder' => [
                    'title' => SORT_ASC,
                ],
            ]
        ]);

        return $dataProvider;
    }

    /**
     * @param bool $isAdmin
     *
     * @return ActiveDataProvider
     * @throws InvalidConfigException
     */
    private function _index(bool $isAdmin = false) {
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var TelegramChatSearch $searchModel */
        $searchModel = new TelegramChatSearch();
        return $searchModel->search($requestParams);
    }
}