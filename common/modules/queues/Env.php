<?php
namespace common\modules\queues;

use Yii;
use yii\base\BaseObject;
use yii\caching\Cache;
use yii\db\Connection;
use yii\di\Instance;

/**
 * Class Env
 * @package common\modules\queue
 */
class Env extends BaseObject
{
    /**
     * @var Cache|array|string
     */
    public $cache = 'cache';
    
    /**
     * @var Connection|array|string
     */
    public $db = 'db';
    
    /**
     * @var string
     */
    public $pushTableName = '{{%queue_push}}';
    
    /**
     * @var string
     */
    public $execTableName = '{{%queue_exec}}';
    
    /**
     * @var string
     */
    public $workerTableName = '{{%queue_worker}}';
    
    /**
     * @var int
     */
    public $workerPingInterval = 15;
    
	/**
	 * @return object
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\di\NotInstantiableException
	 */
    public static function ensure() {
        return Yii::$container->get(static::class);
    }

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->cache = Instance::ensure($this->cache, Cache::class);
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * @return bool
     */
    public function canListenWorkerLoop() {
        return !!$this->workerPingInterval;
    }
	
	/**
	 * @return false|null|string|string[]
	 * @throws \yii\db\Exception
	 */
    public function getHost() {
        if ($this->db->driverName === 'mysql') {
            $host = $this->db->createCommand('SELECT `HOST` FROM `information_schema`.`PROCESSLIST` WHERE `ID` = CONNECTION_ID()')->queryScalar();
            return preg_replace('/:\d+$/', '', $host);
        }

        if ($this->db->driverName === 'pgsql') {
            return $this->db->createCommand('SELECT inet_client_addr()')->queryScalar();
        }

        return '127.0.0.1'; // By default
    }
}
