<?php

namespace common\modules\user\widgets;

use Yii;
use yii\base\Widget;

use common\modules\user\models\forms\SigninForm;

class Login extends Widget
{
    /**
	 * @var bool
	 */
    public $validate = true;

    /**
	 * @inheritdoc
	 */
    public function run() {
        $model  = Yii::createObject(SigninForm::className());
        $action = $this->validate ? null : ['/user/security/signin'];

        if ($this->validate && $model->load(Yii::$app->request->post()) && $model->login()) {
            return Yii::$app->response->redirect(Yii::$app->user->returnUrl);
        }

        return $this->render('login', [
            'model'  => $model,
            'action' => $action,
        ]);
    }
}
