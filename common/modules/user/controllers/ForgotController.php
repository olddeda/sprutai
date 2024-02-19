<?php

namespace common\modules\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

use common\modules\base\components\Controller;

use common\modules\user\Finder;
use common\modules\user\models\UserToken;
use common\modules\user\models\forms\ForgotForm;
use common\modules\user\traits\AjaxValidationTrait;

/**
 * ForgotController manages password recovery process.
 *
 * @property \common\modules\user\Module $module
 */
class ForgotController extends Controller
{
    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param \common\modules\user\Finder $finder
     * @param array $config
     */
    public function __construct($id, $module, Finder $finder, $config = []) {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /**
	 * @inheritdoc
	 */
    public function behaviors() {
        return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => ['index', 'reset'],
						'roles' => ['?']
					],
				],
			],
		]);
    }

    /**
     * Show page where user can request password recovery.
     *
	 * @return string
	 * @throws NotFoundHttpException
	 * @throws \yii\base\ExitException
	 * @throws \yii\base\InvalidConfigException
	 */
    public function actionIndex() {
        if (!$this->module->enablePasswordRecovery)
            throw new NotFoundHttpException();

        /**
		 * @var \common\modules\user\models\forms\ForgotForm $model
		 */
        $model = Yii::createObject([
            'class' => ForgotForm::className(),
            'scenario' => ForgotForm::SCENARIO_REQUEST,
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->sendForgotMessage()) {
            return $this->render('index_complete', [
                'title' => Yii::t('user', 'message_recovery_message_sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Display page where user can reset password.
     *
     * @param int $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $code) {
        if (!$this->module->enablePasswordRecovery)
            throw new NotFoundHttpException();

        /**
		 * @var \common\modules\user\models\UserToken $token
		 */
        $token = $this->finder->findToken([
			'user_id' => $id,
			'code' => $code,
			'type' => UserToken::TYPE_RECOVERY
		])->one();

        if ($token === null || $token->isExpired || $token->user === null) {
            Yii::$app->session->setFlash('inline-danger', Yii::t('user', 'message_recovery_link_is_invalid_or_expired'));

            return $this->render('reset_complete', [
                'title'  => Yii::t('user', 'message_invalid_or_expired_link'),
                'module' => $this->module,
            ]);
        }

        /**
		 * @var \common\modules\user\models\forms\ForgotForm $model
		 */
        $model = Yii::createObject([
            'class'=> ForgotForm::className(),
            'scenario' => ForgotForm::SCENARIO_RESET,
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            return $this->render('reset_complete', [
                'title'  => Yii::t('user', 'message_password_has_been_changed'),
                'module' => $this->module,
            ]);
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}
