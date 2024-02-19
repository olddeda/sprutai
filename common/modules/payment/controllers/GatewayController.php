<?php
namespace common\modules\payment\controllers;

use common\modules\content\helpers\enum\Type;
use common\modules\notification\components\Notification;
use common\modules\payment\helpers\enum\Kind;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\modules\rbac\components\AccessControl;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\content\models\Content;

use common\modules\payment\Module;
use common\modules\payment\components\Request;
use common\modules\payment\models\Payment;
use common\modules\payment\models\PaymentType;
use common\modules\payment\helpers\enum\Status;
use common\modules\payment\gateways\Megakassa;
use common\modules\payment\gateways\Robokassa;
use yii\helpers\Url;


/**
 * Class GatewayController
 * @package common\modules\payment\controllers
 */
class GatewayController extends Controller
{
	/**
	 * @var bool
	 */
	public $enableCsrfValidation = false;
	
	/**
	 * @return array
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'allow' => true,
						'actions' => [
							'test',
							'start', 'callback', 'success', 'failure',
							'callback-robokassa', 'success-robokassa', 'failure-robokassa',
							'callback-megakassa', 'success-megakassa', 'failure-megakassa',
                            'callback-robokassa-zwave'
						],
					],
					[
						'allow' => true,
						'actions' => ['create'],
						'roles' => ['@'],
					],
				],
			],
		]);
	}
	
	public function actionTest() {
		$success = false;
		$messageType = ($success) ? 'success' : 'danger';
		$messageText = ($success) ? Yii::t('payment', 'message_payment_success') : Yii::t('payment', 'message_payment_failed');
		
		Yii::$app->getSession()->setFlash($messageType, $messageText);
		
		return $this->redirect(Yii::$app->homeUrl);
	}
	
	public function actionCreate() {
		
		/** @var Module $module */
		$module = Yii::$app->getModule('payment');
		
		$moduleType = ArrayHelper::getValue(Yii::$app->request->post(), 'Payment.module_type');
		$moduleId = ArrayHelper::getValue(Yii::$app->request->post(), 'Payment.module_id');
		$paymentTypeId = ArrayHelper::getValue(Yii::$app->request->post(), 'Payment.payment_type_id');
		
		/** @var PaymentType $typeModel */
		$typeModel = PaymentType::findById($paymentTypeId, true, 'payment-type');
		
		if ($moduleType && $moduleId && $paymentTypeId) {
			
			/** @var Payment $model */
			$model = Payment::find()->where('kind = :kind AND module_type = :module_type AND module_id = :module_id && payment_type_id = :payment_type_id AND status = :status AND user_id = :user_id', [
				':kind' => Kind::ACCRUAL,
				':module_type' => $moduleType,
				':module_id' => $moduleId,
				':payment_type_id' => $paymentTypeId,
				':status' => Status::WAIT,
				':user_id' => Yii::$app->user->id,
			])->one();
			if (is_null($model)) {
				$model = new Payment();
				$model->kind = Kind::ACCRUAL;
				$model->module_type = $moduleType;
				$model->module_id = $moduleId;
				$model->payment_type_id = $paymentTypeId;
				$model->user_id = Yii::$app->user->id;
				$model->status = Status::WAIT;
				$model->date_at = time();
			}

			// Validate and save
			if ($model->load(Yii::$app->request->post())) {
				$model->descr = $typeModel->title;

                if ($model->module_type == ModuleType::CONTENT) {
                    $contentModel = Content::find()->where(['id' => $model->module_id])->one();
                    if ($contentModel) {
                        $model->to_user_id = $contentModel->author_id;
                        if ($paymentTypeModule = $contentModel->getPaymentTypeModule()->one()) {
                            if ($paymentTypeModule->price_fixed && $model->price < $paymentTypeModule->price) {
                                Yii::$app->getSession()->setFlash('danger', Yii::t('payment', 'message_payment_failed_min_price'));

                                return $this->redirect($this->_getRedirectUrl($model));
                            }
                        }
                    }
                }
				
				if ($model->type->price_fixed && $model->price < $model->type->price) {
					Yii::$app->getSession()->setFlash('danger', Yii::t('payment', 'message_payment_failed_min_price'));
					
					return $this->redirect($this->_getRedirectUrl($model));
				}
				
				if ($model->save()) {
					return $this->actionStart($module->gatewayCurrent, $model->id, $model->price, $model->descr, [
						'email' => $model->user->email,
						'phone' => $model->user->profile->phone,
					]);
				}
			}
		}
	}
	
	
	/**
	 * @param $gatewayName
	 * @param $id
	 * @param $amount
	 * @param string $description
	 * @param array $params
	 *
	 * @return string|\yii\web\Response
	 * @throws \Exception
	 */
    public function actionStart($gatewayName, $id, $amount, $description = '', array $params = []) {
        $process = Module::getInstance()->start($gatewayName, $id, $amount, $description, $params);

        if ($process->request->method === 'get') {
            return $this->redirect((string)$process->request);
        }
        else {
            $html = '';
            $html .= Html::beginForm($process->request->url, 'post', ['name' => 'redirectForm', 'target' => '_blank']);
            foreach ($process->request->params as $key => $value) {
                $html .= Html::hiddenInput($key, $value);
            }
            $html .= Html::endForm();
            $html .= Html::script('document.redirectForm.submit()');

            return $html;
        }
    }
	
	/**
	 * @param $gatewayName
	 *
	 * @throws \Exception
	 */
    public function actionCallback($gatewayName) {
        $process = Module::getInstance()->callback($gatewayName, $this->getRequest());
        echo $process->responseText;
    }
	
	/**
	 * @param $gatewayName
	 *
	 * @return array|mixed|\yii\web\Response
	 */
    public function actionSuccess($gatewayName) {
        Module::getInstance()->end($gatewayName, true, $this->getRequest());
	    return $this->redirect(Yii::$app->homeUrl);
    }
    
	/**
	 * @param $gatewayName
	 *
	 * @return array|mixed
	 */
    public function actionFailure($gatewayName) {
	    $error = Yii::$app->request->get('error');
	    if ($error)
		    return $error;
    	
        Module::getInstance()->end($gatewayName, false, $this->getRequest());
    }
	
	/**
	 * @throws \Exception
	 */
	public function actionCallbackRobokassa() {
		return $this->actionCallback(Robokassa::name());
	}

    /**
     * @throws \Exception
     */
    public function actionCallbackRobokassaZwave() {
        return $this->actionCallback('robokassaZwave');
    }
	
	/**
	 * @return array|mixed|\yii\web\Response
	 */
	public function actionSuccessRobokassa() {
		return $this->_redirect(Robokassa::name(), true);
	}
	
	/**
	 * @return array|mixed
	 */
	public function actionFailureRobokassa() {
		return $this->_redirect(Robokassa::name(), false);
	}
	
	/**
	 * @throws \Exception
	 */
	public function actionCallbackMegakassa() {
		return $this->actionCallback(Megakassa::name());
	}
	
	/**
	 * @return array|mixed|\yii\web\Response
	 */
	public function actionSuccessMegakassa() {
		return $this->_redirect(Megakassa::name(), true);
	}
	
	/**
	 * @return array|mixed
	 */
	public function actionFailureMegakassa() {
		return $this->_redirect(Megakassa::name(), false);
	}
	
	/**
	 * @param $gatewayName
	 * @param $success
	 *
	 * @return array|mixed|\yii\web\Response
	 */
	private function _redirect($gatewayName, $success) {
		$messageType = ($success) ? 'success' : 'danger';
		$messageText = ($success) ? Yii::t('payment', 'message_payment_success') : Yii::t('payment', 'message_payment_failed');
		
		Yii::$app->getSession()->setFlash($messageType, $messageText);
		
		$paymentId = null;
		
		switch ($gatewayName) {
			case Robokassa::name():
				$paymentId = Yii::$app->request->post('InvId');
				break;
			case Megakassa::name():
				$paymentId = Yii::$app->request->get('order_id');
				break;
		}
		
		if ($paymentId) {
			
			/** @var Payment $paymentModel */
			$paymentModel = Payment::findById($paymentId, true, 'payment');
			return $this->redirect($this->_getRedirectUrl($paymentModel));
		}
		
		if ($success)
			return $this->actionSuccess($gatewayName);
		return $this->actionFailure($gatewayName);
	}
	
	/**
	 * Get url
	 * @param Payment $paymentModel
	 *
	 * @return array
	 */
	private function _getRedirectUrl(Payment $paymentModel) {
		$url = ['/client'];
		
		if ($paymentModel->module_type == ModuleType::CONTENT) {
			
			/** @var Content $contentModel */
			$contentModel = Content::find()->where(['id' => $paymentModel->module_id])->one();
			
			if ($contentModel->type == Type::ARTICLE) {
				$url = ['/article/view', 'id' => $contentModel->id];
			}
			else if ($contentModel->type == Type::NEWS) {
				$url = ['/news/view', 'id' => $contentModel->id];
			}
			else if ($contentModel->type == Type::BLOG) {
				$url = ['/blog/view', 'id' => $contentModel->id];
			}
			else if ($contentModel->type == Type::PROJECT) {
				$url = ['/projects/view', 'id' => $contentModel->id];
			}
			else if ($contentModel->type == Type::PLUGIN) {
				$url = ['/plugins/view', 'id' => $contentModel->id];
			}
		}
		return $url;
	}
	
	/**
	 * @return Request
	 */
	protected function getRequest() {
		
		/** @var \yii\web\Request $request */
		$request = Yii::$app->request;
		
		$port = $request->port && $request->port !== 80 ? ':'.$request->port : '';

		return new Request([
			'method' => $request->method,
			'url' => $request->hostInfo.$port.str_replace('?'.$request->queryString, '', $request->url),
			'params' => ArrayHelper::merge($request->get(), $request->post()),
		]);
	}
}
