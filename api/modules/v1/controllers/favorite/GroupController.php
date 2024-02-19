<?php
namespace api\modules\v1\controllers\favorite;

use Yii;
use yii\base\ExitException;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;

use api\modules\v1\components\ActiveController as Controller;
use api\modules\v1\components\actions\IndexAction;

use api\models\favorite\FavoriteGroup;
use api\models\favorite\Favorite;
use api\models\favorite\search\FavoriteGroupSearch;

/**
 * Class GroupController
 * @package api\modules\v1\controllers/favorite
 */
class GroupController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\favorite\FavoriteGroup';

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
            'searchModel' => 'api\models\favorite\search\FavoriteGroupSearch',
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
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }

        /** @var FavoriteGroupSearch $searchModel */
        $searchModel = new FavoriteGroupSearch();
        return $searchModel->search($requestParams);
    }

    /**
     * @param $id
     * @param $module_id
     *
     * @return array|ActiveRecord|null
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionFavoriteSet($id, $module_id) {
        $model = $this->findModel($id, true);

        $item = Favorite::find()->where([
            'group_id' => $model->id,
            'module_type' => $model->module_type,
            'module_id' => $module_id,
            'user_id' => Yii::$app->user->id,
        ])->one();
        if ($item) {
            $item->delete();
        }
        else {
            $model->favoriteAdd($module_id);
        }

        return $model;
    }

    /**
     * @param $module_type
     * @param $module_id
     *
     * @throws ExitException
     */
    public function actionFavoriteClear($module_type, $module_id) {
        Favorite::deleteAll([
            'module_type' => (int)$module_type,
            'module_id' => (int)$module_id,
            'user_id' => Yii::$app->user->id,
        ]);

        Yii::$app->getResponse()->setStatusCode(200);
        Yii::$app->end();
    }

    /**
     * @param integer $id
     * @param bool $owner
     *
     * @return array|ActiveRecord|null
     */
    public function findModel($id, $owner = false) {
        if (in_array($id, [1, 2, 3,4])) {
            $owner = false;
        }
        return FavoriteGroup::findBy($id, true, 'favorite-group', [], false, $owner);
    }

    /**
     * @OA\Get(
     *     path="/favorites/groups",
     *     tags={"favorite group"},
     *     summary="Получение списка групп избранного",
     *     description="Возвращает cписок групп избранного",
     *     operationId="favorite_group_list",
     *     security={{"Bearer":{}}},
     *     @OA\Parameter(
     *         name="module_type",
     *         in="query",
     *         description="Тип модуля",
     *         required=true,
     *         explode=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(
     *                 type="integer",
     *                 enum = {"40", "81"},
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="module_id",
     *         in="query",
     *         description="ID модуля",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Тип материала",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/FavoriteGroup")
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
     *                 @OA\Items(ref="#/components/schemas/FavoriteGroup")
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
     *     path="/favorites/groups",
     *     tags={"favorite group"},
     *     summary="Добавление нового группы избранного",
     *     description="Добавляет и возвращает данные новой группы избранного",
     *     operationId="favorite_group_create",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/FavoriteGroup"},
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
     *     @OA\Response(
     *         response=201,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/FavoriteGroup")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/FavoriteGroup")
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
     *     path="/favorites/groups/{id}",
     *     tags={"favorite group"},
     *     summary="Редактирование группы избранного",
     *     description="Обновляет и возвращает данные измененой группы избранного",
     *     operationId="favorite_group_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/FavoriteGroup"},
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
     *         name="id",
     *         in="path",
     *         description="ID группы избранного",
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
     *                 @OA\Items(ref="#/components/schemas/FavoriteGroup")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/FavoriteGroup")
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
     *         description="Группа избранного не найдена"
     *     ),
     * )
     */

    /**
     * @OA\Delete(
     *     path="/favorites/groups/{id}",
     *     tags={"favorite group"},
     *     summary="Удаление группы избранного",
     *     description="Удаляет группы избранного",
     *     operationId="favorite_group_delete",
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID группы избранного",
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
     *         description="Группа избранного не найден"
     *     ),
     * )
     */
}
