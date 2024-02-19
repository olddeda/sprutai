<?php
namespace api\modules\v1\controllers\content;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;

use api\modules\v1\components\ActiveController as Controller;
use api\modules\v1\components\actions\IndexAction;

use api\models\content\search\ContentHistorySearch;

/**
 * Class HistoryController
 * @package api\modules\v1\controllers\content
 */
class HistoryController extends Controller
{
	/**
	 * @var string the model class name. This property must be set.
	 */
	public $modelClass = 'api\models\content\ContentHistory';

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        $actions['create']['findModel'] = [$this, 'findModel'];

        unset($actions['view'], $actions['update'], $actions['delete']);

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

        /** @var ContentHistorySearch $searchModel */
        $searchModel = new ContentHistorySearch();
        return $searchModel->search($requestParams);
    }

    /**
     * @OA\Get (path="/contents/{id}/history",
     *     tags={"content history"},
     *     summary="Получение списка истории материалов",
     *     operationId="contents_history_list",
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
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ContentHistory")
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
     *                 @OA\Items(ref="#/components/schemas/ContentHistory")
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
     * @OA\Post(path="/contents/{id}/history",
     *     tags={"content history"},
     *     summary="Добавление новой истории материала",
     *     description="Добавляет и возвращает данные новой истории материала",
     *     operationId="content_history_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/ContentForm"},
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/ContentHistory")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/ContentHistory")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     )
     * )
     */
}