<?php
namespace common\modules\queues\records;

use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * Class PushQuery
 * @package common\modules\queues\records
 */
class PushQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        $this->alias('push');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function byId($id) {
        return $this->andWhere(['push.id' => $id]);
    }

    /**
     * @param string $senderName
     * @param string $jobUid
     * @return $this
     */
    public function byJob($senderName, $jobUid) {
        return $this
            ->andWhere(['push.sender_name' => $senderName])
            ->andWhere(['push.job_uid' => $jobUid])
            ->orderBy(['push.id' => SORT_DESC])
            ->limit(1);
    }

    /**
     * @return $this
     */
    public function waiting() {
        return $this
            ->joinLastExec()
            ->andWhere(['or', ['push.last_exec_id' => null], ['last_exec.retry' => true]])
            ->andWhere(['push.stopped_at' => null]);
    }

    /**
     * @return $this
     */
    public function inProgress() {
        return $this
            ->andWhere(['is not', 'push.last_exec_id', null])
            ->joinLastExec()
            ->andWhere(['last_exec.finished_at' => null]);
    }

    /**
     * @return $this
     */
    public function done() {
        return $this
            ->joinLastExec()
            ->andWhere(['is not', 'last_exec.finished_at', null])
            ->andWhere(['last_exec.retry' => false]);
    }

    /**
     * @return $this
     */
    public function success() {
        return $this
            ->done()
            ->andWhere(['last_exec.error' => null]);
    }

    /**
     * @return $this
     */
    public function buried() {
        return $this
            ->done()
            ->andWhere(['is not', 'last_exec.error', null]);
    }

    /**
     * @return $this
     */
    public function hasFails() {
        return $this
            ->andWhere(['exists', new Query([
                'from' => ['exec' => ExecRecord::tableName()],
                'where' => '{{exec}}.[[push_id]] = {{push}}.[[id]] AND {{exec}}.[[error]] IS NOT NULL',
            ])]);
    }

    /**
     * @return $this
     */
    public function stopped() {
        return $this->andWhere(['is not', 'push.stopped_at', null]);
    }

    /**
     * @return $this
     */
    public function joinFirstExec() {
        return $this->leftJoin(
            ['first_exec' => ExecRecord::tableName()],
            '{{first_exec}}.[[id]] = {{push}}.[[first_exec_id]]'
        );
    }

    /**
     * @return $this
     */
    public function joinLastExec() {
        return $this->leftJoin(
            ['last_exec' => ExecRecord::tableName()],
            '{{last_exec}}.[[id]] = {{push}}.[[last_exec_id]]'
        );
    }

    /**
     * @inheritdoc
     * @return PushRecord[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PushRecord|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }
}
