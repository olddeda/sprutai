<?php
namespace common\modules\payment\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use YandexMoney\API;

use common\modules\rbac\components\AccessControl;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\payment\components\YMComponent;


/**
 * Class YandexController
 * @package common\modules\payment\controllers
 */
class YandexController extends Controller
{
	public $clientId = '0E4832F0DDA777ACDC783A007C97C0251DFEB245E7A60C59581CF9BEF3BC35A5';
	public $redirectUri = 'https://dev.sprut.ai/client/payment/yandex/redirect';
	
	
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
						'actions' => ['redirect', 'test'],
					],
				],
			],
		]);
	}
	
	public function actionTest() {
		
		$auth_url = API::buildObtainTokenUrl($this->clientId, $this->redirectUri, ['account-info', 'operation-history', 'operation-details']);
		var_dump($auth_url);
		die;
		//$this->redirect($auth_url);
	}
	
	public function actionRedirect() {
		$result = API::getAccessToken($this->clientId, $_GET['code'], $this->redirectUri);
		if ($result->access_token){
			$api = new API($result->access_token);
			
			$accountInfo = $api->accountInfo();
			Debug::dump($accountInfo);
		}
		
	}
}