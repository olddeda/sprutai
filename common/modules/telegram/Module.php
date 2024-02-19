<?php
namespace common\modules\telegram;

use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\ArrayHelper;

use PDO;
use PDOException;

use Longman\TelegramBot\Exception\TelegramException;

use common\modules\telegram\models\TelegramCategory;

class Module extends BaseModule
{
	/**
	 * @var string
	 */
	public $apiKey = '';
	
	/**
	 * @var string
	 */
	public $botName = 'sprutai_bot';
	
	/**
	 * @var string
	 */
	public $botanKey = '';
	
	/**
	 * @var string
	 */
	public $dbName;
	
	/**
	 * @var
	 */
	public $dbHost;
	
	/**
	 * @var string
	 */
	public $dbUser;
	
	/**
	 * @var string
	 */
	public $dbPassword;
	
	/**
	 * @var array
	 */
	public $dbOptions;
	
	/**
	 * @var bool
	 */
	public $dbExtrenal = false;
	
	/**
	 * @var string
	 */
	public $proxyUrl;
	
	/**
	 * @var string
	 */
	public $proxyScheme = 'http';
	
	/**
	 * @var string
	 */
	public $proxyUsername;
	
	/**
	 * @var string
	 */
	public $proxyPassword;
	
	/**
	 * @var array
	 */
	public $adminsIds = [];
	
	/**
	 * @var array
	 */
	public $moderateIds = [];
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
		
		$this->adminsIds = array_unique($this->adminsIds);
		$this->moderateIds = array_unique($this->moderateIds);
	}

    /**
     * @return PDO
     * @throws TelegramException
     */
	public function getPDO() {
        $dsn = 'mysql:host='.$this->dbHost.';dbname='.$this->dbName;
        $options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'];

        if (is_array($this->dbOptions))
            $options = ArrayHelper::merge($options, $this->dbOptions);

        try {
            $pdo = new PDO($dsn, $this->dbUser, $this->dbPassword, $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        }
        catch (PDOException $e) {
            throw new TelegramException($e->getMessage());
        }
        return $pdo;
    }
}