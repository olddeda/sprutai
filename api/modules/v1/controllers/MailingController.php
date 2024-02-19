<?php
namespace api\modules\v1\controllers;

use api\helpers\enum\Error;
use Yii;

use api\components\ErrorException;

use api\modules\v1\components\Controller;

use common\modules\mailing\helpers\enum\Type;
use common\modules\mailing\models\MailingUser;
use yii\helpers\ArrayHelper;

class MailingController extends Controller {
    
    /**
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'except' => ['create'],
            ],
            'access' => [
                'except' => ['create'],
            ],
        ]);
    }


    public function actionCreate() {
        $email = Yii::$app->request->post('email');
        if (!$email) {
            throw new ErrorException(Error::ERROR_MAILING_USER_EMPTY_EMAIL);
        }
        
        $model = MailingUser::find()->where('email = :email AND type = :type', [
            ':email' => $email,
            ':type' => Type::HUB
        ])->one();
        if (!$model) {
            $model = new MailingUser();
            $model->type = Type::HUB;
            $model->email = $email;
            $model->status = 1;
            
            if (!$model->save()) {
                throw new ErrorException($model->errors);
            }
    
            return $model;
        }
        
        throw new ErrorException(Error::ERROR_MAILING_USER_EXISTS);
    }
}