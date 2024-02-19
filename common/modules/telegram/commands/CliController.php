<?php
namespace common\modules\telegram\commands;

use common\modules\base\components\Debug;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

use PDO;
use PDOException;

use GuzzleHttp\Client;

/**
 * Class CliController
 * @package common\modules\telegram\commands
 *
 * @property \common\modules\telegram\Module $module
 */

class CliController extends Controller
{
    private $_telegram;

	/**
	 * Run telegram cycle
	 * @return mixed
	 */
	public function actionIndex() {
		$this->_handle();
		
		$seconds = 1;
		$micro = $seconds * 1000000;
		usleep($micro);

		return $this->actionIndex();
	}
	
	/**
	 * Run telegram one
	 */
	public function actionSingle() {
		$this->_handle();
	}
	
	/**
	 * Unset hook
	 */
	public function actionHookUnset() {
		
		try {
			
			// Create Telegram API object
			$telegram = $this->_getTelegram();
			
			// Delete webhook
			$result = $telegram->deleteWebhook();
			
			if ($result->isOk()) {
				echo $result->getDescription();
				echo PHP_EOL;
			}
		}
		catch (TelegramException $e) {
			echo $e->getMessage();
			echo PHP_EOL;
		}
	}
	
	public function actionTest() {
		
		/** @var \common\modules\base\extensions\telegram\Telegram $telegram */
		$telegram = Yii::$app->telegram;
		$telegram->sendMessage([
			'chat_id' => 357615556,
			'text' => 'Test',
		]);
	}
	
	/**
	 * Handle
	 */
	private function _handle() {
		
		try {
			$this->_getTelegram()->handleGetUpdates();
		} catch (TelegramException $e) {
			
			// Log telegram errors
			TelegramLog::error($e);
			
			echo "Error:".PHP_EOL;
			echo $e;
			
		} catch (TelegramLogException $e) {
			
			echo "Error:".PHP_EOL;
			echo $e;
		}
	}
	
	/**
	 * Get telegram object
	 *
	 * @return Telegram
	 * @throws TelegramException
	 */
	private function _getTelegram() {
	    if (is_null($this->_telegram)) {

            // Create Telegram API object
            $telegram = new Telegram($this->module->apiKey, $this->module->botName);

            // Enable proxy
            if ($this->module->proxyUrl) {
                $proxyUrl = $this->module->proxyUrl;
                if ($this->module->proxyUsername && $this->module->proxyPassword)
                    $proxyUrl = $this->module->proxyUsername.':'.$this->module->proxyPassword.'@'.$proxyUrl;
                $proxyUrl = $this->module->proxyScheme.'://'.$proxyUrl;

                Request::setClient(new Client([
                    'base_uri' => 'https://api.telegram.org',
                    'proxy' => $proxyUrl,
                ]));
            }

            // Add folder commands
            $telegram->addCommandsPath(Yii::getAlias('@common/modules/telegram').DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR.'telegram'.DIRECTORY_SEPARATOR.'base');

            // Enable admin users
            if ($this->module->adminsIds)
                $telegram->enableAdmins($this->module->adminsIds);

            // Enable MySQL
            if ($this->module->dbHost && $this->module->dbUser && $this->module->dbPassword && $this->module->dbName) {
                if ($this->module->dbExtrenal) {
                    $dsn = 'mysql:host='.$this->module->dbHost.';dbname='.$this->module->dbName;

                    $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'];

                    if (is_array($this->module->dbOptions))
                        $options = ArrayHelper::merge($options, $this->module->dbOptions);

                    try {
                        $pdo = new PDO($dsn, $this->module->dbUser, $this->module->dbPassword, $options);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                    }
                    catch (PDOException $e) {
                        throw new TelegramException($e->getMessage());
                    }

                    $telegram->enableExternalMysql($pdo);
                }
                else {
                    $telegram->enableMySql([
                        'host'     => $this->module->dbHost,
                        'user'     => $this->module->dbUser,
                        'password' => $this->module->dbPassword,
                        'database' => $this->module->dbName,
                    ]);
                }
            }

            // Set download path
            $path = Yii::getAlias('@runtime/tmp');
            if (!file_exists($path))
                mkdir($path);
            $telegram->setDownloadPath($path);

            // Requests Limiter (tries to prevent reaching Telegram API limits)
            //$telegram->enableLimiter();

            $this->_telegram = $telegram;
        }
		
		return $this->_telegram;
	}
}