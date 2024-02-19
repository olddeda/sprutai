<?php
namespace client\controllers;

use Yii;

use common\modules\base\helpers\enum\Status;

use common\modules\rbac\components\AccessControl;

use common\modules\content\models\Page;
use common\modules\content\helpers\enum\PageType;

use common\modules\user\models\User;

use client\components\Controller;
use client\forms\CooperationForm;

class PageController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return array_merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => ['view'],
						'roles' => ['?', '@'],
					],
				],
			],
		]);
	}
	
	/**
	 * Displays a single Content model.
	 * @param integer $id
	 *
	 * @return string
	 */
	public function actionView($id) {
		$conditions = [];
		if (!Yii::$app->user->isAdmin && !Yii::$app->user->isEditor)
			$conditions['status'] = Status::ENABLED;
		
		/** @var Page $model */
		$model = Page::findById($id, true, 'content-page', [], false, $conditions);
		
		
		$params = ['model' => $model];
		
		if ($model->page_type == PageType::PATH) {
			if ($model->page_path == 'contacts') {
				
				/** @var CooperationForm $modelForm */
				$modelForm = new CooperationForm();
				
				if (!Yii::$app->user->isGuest) {
					
					/** @var User $user */
					$user = Yii::$app->user->identity;
					$modelForm->name = $user->getFio(false);
					$modelForm->email = $user->email;
					$modelForm->phone = $user->profile->phone;
				}
				
				if ($modelForm->load(Yii::$app->request->post()) && $model->validate() && $modelForm->send()) {
					Yii::$app->session->setFlash('success', Yii::t('page_contacts', 'message_send_success'));
					return $this->refresh();
				}
				
				$params['modelForm'] = $modelForm;
			}
			else if ($model->page_path == 'cooperation') {
				
				/** @var CooperationForm $modelForm */
				$modelForm = new CooperationForm();
				
				if (!Yii::$app->user->isGuest) {
					
					/** @var User $user */
					$user = Yii::$app->user->identity;
					$modelForm->name = $user->getFio(false);
					$modelForm->email = $user->email;
					$modelForm->phone = $user->profile->phone;
				}
				
				if ($modelForm->load(Yii::$app->request->post()) && $model->validate() && $modelForm->send()) {
					Yii::$app->session->setFlash('success', Yii::t('page_cooperation', 'message_send_success'));
					return $this->refresh();
				}
				
				$params['modelForm'] = $modelForm;
			}
		}
		
		// Render view
		$template = ($model->page_type == PageType::PATH) ? $model->page_path : 'view';
		return $this->render($template, $params);
	}
}