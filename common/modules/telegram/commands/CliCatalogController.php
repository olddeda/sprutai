<?php
namespace common\modules\telegram\commands;

use common\modules\telegram\Module;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use PDO;
use PDOException;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class CliCatalogController
 * @package common\modules\telegram\commands
 *
 * @property \common\modules\telegram\Module $module
 */

class CliCatalogController extends Controller
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

	        /** @var Module $module */
	        $module = Yii::$app->getModule('telegramCatalog');

            // Create Telegram API object
            $telegram = new Telegram($module->apiKey, $module->botName);

            // Add folder commands
            $telegram->addCommandsPath(Yii::getAlias('@common/modules/telegram').DIRECTORY_SEPARATOR.'commands'.DIRECTORY_SEPARATOR.'telegram_catalog'.DIRECTORY_SEPARATOR.'base');

            // Enable admin users
            if ($module->adminsIds)
                $telegram->enableAdmins($module->adminsIds);

            // Enable MySQL
            if ($module->dbHost && $module->dbUser && $module->dbPassword && $module->dbName) {
                if ($module->dbExtrenal) {
                    $dsn = 'mysql:host='.$module->dbHost.';dbname='.$module->dbName;

                    $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'];

                    if (is_array($module->dbOptions))
                        $options = ArrayHelper::merge($options, $module->dbOptions);

                    try {
                        $pdo = new PDO($dsn, $module->dbUser, $module->dbPassword, $options);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                    }
                    catch (PDOException $e) {
                        throw new TelegramException($e->getMessage());
                    }

                    $telegram->enableExternalMysql($pdo);
                }
                else {
                    $telegram->enableMySql([
                        'host'     => $module->dbHost,
                        'user'     => $module->dbUser,
                        'password' => $module->dbPassword,
                        'database' => $module->dbName,
                    ]);
                }
            }

            // Set download path
            $path = Yii::getAlias('@runtime/tmp');
            if (!file_exists($path))
                mkdir($path);
            $telegram->setDownloadPath($path);
            
            $this->_telegram = $telegram;
        }
		
		return $this->_telegram;
	}
}