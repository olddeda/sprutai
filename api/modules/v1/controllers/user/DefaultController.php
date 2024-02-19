<?php
namespace api\modules\v1\controllers\user;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

use common\modules\user\helpers\enum\ActivityType;

use api\modules\v1\components\ActiveController as Controller;

use api\models\user\User;
use api\models\user\search\UserActivitySearch;
use api\models\user\UserActivity;

class DefaultController extends Controller
{
    /**
     * @var string
     */
    public $modelClass = 'api\models\user\User';

    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'optional' => ['username', 'activity', 'activity-stats'],
            ],
            'access' => [
                'except' => ['username', 'activity', 'activity-stats'],
            ],
        ]);
    }

    /**
     * @OA\Get (path="/users/{username}",
     *     tags={"user"},
     *     summary="Получение данных пользователя",
     *     operationId="user_username",
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
     *      @OA\Parameter(
     *         name="username",
     *         in="path",
     *         description="Username пользователя",
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
     *                 @OA\Items(ref="#/components/schemas/User")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/User")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден"
     *     ),
     * )
     */
    public function actionUsername($username) {
        $user = User::findUserByUsername($username);
        if (is_null($user)) {
            throw new NotFoundHttpException(Yii::t('user', 'error_not_exists'));
        }
        return [
            'item' => $user,
        ];
    }

    /**
     * @OA\Patch(
     *     path="/users/profile",
     *     tags={"user"},
     *     summary="Редактирование профиля пользователя",
     *     description="Обновляет и возвращает профиль пользователя",
     *     operationId="user_profile_update",
     *     security={{"Bearer": {}}},
     *     requestBody={"$ref": "#/components/requestBodies/UserProfileForm"},
     *     @OA\Response(
     *         response=204,
     *         description="Успешно",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/UserProfile")
     *             )
     *         ),
     *         @OA\XmlContent(
     *             @OA\Xml(
     *                 name="response",
     *                 wrapped=true
     *             ),
     *             @OA\Property(
     *                 property="item",
     *                 @OA\Items(ref="#/components/schemas/UserProfile")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешно"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     )
     * )
     */
    public function actionProfileUpdate() {

    }

    public function actionActivity($id) {
        $user = User::findById($id, true, 'user');

        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        $requestParams['filter']['user_id'] = $id;

        /** @var UserActivitySearch $searchModel */
        $searchModel = new UserActivitySearch();

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $searchModel->search($requestParams);

        $dataProvider->query->andWhere(['in', UserActivity::tableName().'.type', [
            ActivityType::SIGNUP,
            ActivityType::CONTENT,
            ActivityType::COMMENT,
            ActivityType::REVIEW,
            ActivityType::LIKES,
            ActivityType::LIKED,
            ActivityType::SUBSCRIBES,
            ActivityType::SUBSCRIBED,
            ActivityType::ACHIEVEMENT,
        ]]);

        return $dataProvider;
    }

    public function actionActivityStats($id) {
        $user = User::findById($id, true, 'user');

        $query = (new Query())
            ->select([
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.') AS all',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::CONTENT.') AS content',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::COMMENT.') AS comment',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::REVIEW.') AS review',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::LIKES.') AS likes',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::LIKED.') AS liked',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::SUBSCRIBES.') AS subscribes',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::SUBSCRIBED.') AS subscribed',
                '(SELECT COUNT(type) FROM '.UserActivity::tableName().' WHERE user_id = '.$user->id.' AND type = '.ActivityType::ACHIEVEMENT.') AS achievement',
            ])
        ;

        return $query->one();
    }
}