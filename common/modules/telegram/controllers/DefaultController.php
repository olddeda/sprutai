<?php
namespace common\modules\telegram\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

use common\modules\base\components\Controller;
use common\modules\base\components\Debug;

use common\modules\telegram\helpers\Helpers;

use common\modules\user\models\UserAccount;

/**
 * @property \common\modules\telegram\Module $module
 */

class DefaultController extends Controller
{
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'access' => [
				'rules' => [
					[
						'actions' => ['hook', 'hook-set', 'hook-unset'],
						'allow' => true,
					],
				],
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		if ($action->id == 'hook') {
			$this->enableCsrfValidation = false;
		}
		
		return parent::beforeAction($action);
	}
	
	/**
	 * Link user
	 * @param $code
	 *
	 * @return \yii\web\Response
	 * @throws NotFoundHttpException
	 */
	public function actionLink($code) {
		
		/**
		 * @var \common\modules\user\models\UserAccount $account
		 */
		$account = UserAccount::find()->where('code = :code AND provider = :provider', [
			':code' => $code,
			':provider' => 'telegram',
		])->one();
		if ($account) {
			$account->user_id = Yii::$app->user->id;
			$account->code = null;
			$account->save();
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('telegram', 'message_link_success'));
			
			Yii::$app->telegram->sendMessage([
				'chat_id' => $account->client_id,
				'text' => Yii::t('telegram', 'message_link_telegram_success'),
			]);
			
			return $this->redirect(['/user/settings/subscribe']);
		}
		else {
			throw new NotFoundHttpException(Yii::t('telegram', 'message_link_failed'));
		}
	}
	
	/**
	 * Hook
	 *
	 * @throws TelegramException
	 * @throws \Exception
	 * @throws \Longman\TelegramBot\Exception\TelegramLogException
	 */
	public function actionHook() {
		try {
			
			// Create Telegram API object
			$telegram = new Telegram($this->module->apiKey, $this->module->botName);
			
			// Enable botan integration
			if ($this->module->botanKey)
				$telegram->enableBotan($this->module->botanKey);
			
			// Add folder commands
			$telegram->addCommandsPath(Yii::getAlias('@common/modules/telegram').DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR.'telegram'.DIRECTORY_SEPARATOR.'base');
			
			// Enable admin users
			if ($this->module->adminsIds)
				$telegram->enableAdmins($this->module->adminsIds);
			
			// Enable MySQL
			$dbName = null;
			if ($this->module->dbHost && $this->module->dbUser && $this->module->dbPassword && $this->module->dbName) {
				$telegram->enableMySql([
					'host'     => $this->module->dbHost,
					'user'     => $this->module->dbUser,
					'password' => $this->module->dbPassword,
					'database' => $this->module->dbName,
				]);
			}
			
			if ($this->module->proxyUrl) {
				$proxyUrl = $this->module->proxyUrl;
				if ($this->module->proxyUsername && $this->module->proxyPassword)
					$proxyUrl = $this->module->proxyUsername.':'.$this->module->proxyPassword.'@'.$proxyUrl;
				$proxyUrl = 'http://'.$proxyUrl;
				
				Request::setClient(new \GuzzleHttp\Client([
					'base_uri' => 'https://api.telegram.org',
					'proxy' => $proxyUrl,
				]));
			}
			
			// Requests Limiter (tries to prevent reaching Telegram API limits)
			//$telegram->enableLimiter();
			
			// Handle telegram webhook request
			$telegram->handle();
			
		} catch (TelegramException $e) {
			
			Helpers::dump($e);
			
			// Log telegram errors
			TelegramLog::error($e);
			
		} catch (TelegramLogException $e) {
			
			Helpers::dump($e);
			
			echo $e;
		}
	}
	
	/**
	 * Set hook
	 */
	public function actionHookSet() {
		
		try {
			// Create Telegram API object
			$telegram = new Telegram($this->module->apiKey, $this->module->botName);
			
			if ($this->module->proxyUrl) {
				$proxyUrl = $this->module->proxyUrl;
				if ($this->module->proxyUsername && $this->module->proxyPassword)
					$proxyUrl = $this->module->proxyUsername.':'.$this->module->proxyPassword.'@'.$proxyUrl;
				$proxyUrl = 'http://'.$proxyUrl;
				
				Request::setClient(new \GuzzleHttp\Client([
					'base_uri' => 'https://api.telegram.org',
					'proxy' => $proxyUrl,
				]));
			}
			
			Debug::dump($this->module->apiKey);
			Debug::dump($this->module->botName);
			
			
			
			Debug::dump(Yii::$app->urlManager->createAbsoluteUrl('telegram/default/hook', 'https'));
			
			$result = $telegram->setWebhook(Yii::$app->urlManager->createAbsoluteUrl('telegram/default/hook', 'https'));
			if ($result->isOk()) {
				echo $result->getDescription();
			}
		}
		catch (TelegramException $e) {
			echo Debug::dump($e);
		}
	}
	
	/**
	 * Unset hook
	 */
	 public function actionHookUnset() {
		
		try {
			// Create Telegram API object
			$telegram = new Telegram($this->module->apiKey, $this->module->botName);
			
			if ($this->module->proxyUrl) {
				$proxyUrl = $this->module->proxyUrl;
				if ($this->module->proxyUsername && $this->module->proxyPassword)
					$proxyUrl = $this->module->proxyUsername.':'.$this->module->proxyPassword.'@'.$proxyUrl;
				$proxyUrl = 'http://'.$proxyUrl;
				
				Request::setClient(new \GuzzleHttp\Client([
					'base_uri' => 'https://api.telegram.org',
					'proxy' => $proxyUrl,
				]));
			}
			
			// Delete webhook
			$result = $telegram->deleteWebhook();
			if ($result->isOk()) {
				echo $result->getDescription();
			}
		}
		catch (TelegramException $e) {
			echo Debug::dump($e);
		}
	}
}