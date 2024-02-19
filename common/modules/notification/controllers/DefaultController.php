<?php
namespace common\modules\notification\controllers;

use common\modules\base\components\Debug;
use Yii;

use common\modules\base\components\Controller;
use common\modules\notification\forms\NotifyForm;

/**
 * DefaultController implements the CRUD actions for Notification model.
 */
class DefaultController extends Controller
{
	/**
	 * Send notification
	 * @return mixed
	 */
	public function actionSend() {
		
		/** @var NotifyForm $model */
		$model = new NotifyForm();
		
		if ($model->load(Yii::$app->request->post()) && $model->send()) {
			Yii::$app->session->setFlash('success', Yii::t('notification-form', 'message_send_success'));
			return $this->refresh();
		}
		
		// Render
		return $this->render('send', [
			'model' => $model,
		]);
	}
}