<?php
namespace common\modules\queues\records;

use yii\db\ActiveQuery;

use common\modules\queues\Env;

/**
 * Class WorkerQuery
 * @package common\modules\queues\records
 */
class WorkerQuery extends ActiveQuery
{
    /**
     * @var Env
     */
    private $env;

    /**
     * @param string $modelClass
     * @param Env $env
     * @param array $config
     * @inheritdoc
     */
    public function __construct($modelClass, Env $env, array $config = []) {
        $this->env = $env;
        parent::__construct($modelClass, $config);
    }

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->alias('worker');
    }

    /**
     * @param string $host
     * @param int $pid
     * @return $this
     */
    public function byEvent($host, $pid) {
        return $this->andWhere([
            'worker.host' => $host,
            'worker.pid' => $pid,
        ]);
    }

    /**
     * @return $this
     */
    public function active() {
        return $this
            ->andWhere(['worker.finished_at' => null])
            ->leftJoin(['exec' => ExecRecord::tableName()], '{{exec}}.[[id]] = {{worker}}.[[last_exec_id]]')
            ->andWhere([
                'or',
                ['>', 'worker.pinged_at', time() - $this->env->workerPingInterval - 5],
                ['exec.finished_at' => null],
            ]);
    }

    /**
     * @inheritdoc
     * @return WorkerRecord[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return WorkerRecord|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }
}
