<?php
namespace common\modules\queues\records;

use Yii;
use yii\db\ActiveRecord;

use common\modules\queues\Env;

/**
 * Class ExecRecord
 * @package common\modules\queues\records
 *
 * @property int $id
 * @property int $push_id
 * @property null|int $worker_id
 * @property int $attempt
 * @property int $started_at
 * @property null|int $finished_at
 * @property null|int $memory_usage
 * @property null|string $error
 * @property null|bool $retry
 *
 * @property PushRecord $push
 * @property null|WorkerRecord $worker
 *
 * @property int $duration
 * @property false|string $errorMessage
 */
class ExecRecord extends ActiveRecord
{
    private $_errorMessage;

    /**
     * @inheritdoc
     * @return ExecQuery the active query used by this AR class.
     */
    public static function find() {
        return Yii::createObject(ExecQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function getDb() {
        return Env::ensure()->db;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return Env::ensure()->execTableName;
    }

    /**
     * @return PushQuery
     */
    public function getPush() {
        return $this->hasOne(PushRecord::class, ['id' => 'push_id']);
    }

    /**
     * @return WorkerQuery
     */
    public function getWorker() {
        return $this->hasOne(WorkerRecord::class, ['id' => 'worker_id']);
    }

    /**
     * @return int
     */
    public function getDuration() {
        if ($this->finished_at) {
            return $this->finished_at - $this->started_at;
        }
        return time() - $this->started_at;
    }

    /**
     * @return bool
     */
    public function isDone() {
        return $this->finished_at !== null;
    }

    /**
     * @return bool
     */
    public function isFailed() {
        return $this->error !== null;
    }

    /**
     * @return false|string first error line
     */
    public function getErrorMessage() {
        if ($this->_errorMessage === null) {
            $this->_errorMessage = false;
            if ($this->error !== null) {
                $this->_errorMessage = trim(explode("\n", $this->error, 2)[0]);
            }
        }
        return $this->_errorMessage;
    }
}
