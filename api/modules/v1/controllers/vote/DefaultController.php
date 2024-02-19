<?php
namespace api\modules\v1\controllers\vote;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;

use api\modules\v1\components\ActiveController as Controller;

use api\models\vote\Vote;

/**
 * Class DefaultController
 * @package api\modules\v1\controllers\vote
 */
class DefaultController extends Controller
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = 'api\models\vote\Vote';

    /**
     * @return array
     */
    public function actions() {
        $actions = parent::actions();

        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);

        return $actions;
    }

    public function actionLike($entityType, $entityId) {
        $entity = Yii::$app->getModule('vote')->encodeEntity($entityType);

        $oldValue = null;

        $model = Vote::find()->where('entity = :entity AND entity_id = :entity_id AND user_id = :user_id', [
            ':entity' => $entity,
            ':entity_id' => $entityId,
            ':user_id' => Yii::$app->user->id,
        ])->one();
        if (is_null($model)) {
            $model = new Vote();
            $model->entity = $entity;
            $model->entity_id = $entityId;
            $model->user_id = Yii::$app->user->id;
        }
        else {
            $oldValue = $model->value;
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->validate()) {
            if (!is_null($oldValue) && $oldValue == $model->value) {
                $model->delete();
                $model->value = null;
            }
            else {
                $model->save();
            }
        }

        return $model;
    }

    /**
     * @OA\Post(
     *     path="/votes/like/{entityType}/{entityId}",
     *     tags={"vote"},
     *     summary="Добавление, удаление лайка",
     *     description="Добавляет или удаляет лайк",
     *     operationId="vote_like",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/Vote"},
     *     @OA\Parameter(
     *         name="module_type",
     *         in="path",
     *         description="Тип модуля",
     *         required=true,
     *         @OA\Schema(
     *            type="integer",
     *            default="40"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="module_id",
     *         in="path",
     *         description="ID модуля",
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
     *                 @OA\Items(ref="#/components/schemas/Vote")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/Vote")
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
}