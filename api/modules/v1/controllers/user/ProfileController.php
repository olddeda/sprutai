<?php
namespace api\modules\v1\controllers\user;

use api\models\user\User;
use Yii;
use yii\helpers\ArrayHelper;

use api\modules\v1\components\Controller as Controller;

use common\modules\base\components\Debug;

use api\models\user\UserProfile;

class ProfileController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'except' => ['update'],
            ],
        ]);
    }

    /**
     * OA\Patch(
     *     path="/users/profile",
     *     tags={"user profile"},
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
     *         response=401,
     *         description="Пользователь не авторизован"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *     )
     * )
     */
    public function actionUpdate() {

        /** @var UserProfile $model */
        $model = Yii::$app->user->identity->profile;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
        }
        else if (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return User::find()->where(['id' => $model->user_id])->one();
    }

    /**
     * @return UserProfile
     */
    public function findModel() {
        return Yii::$app->user->profile;
    }
}