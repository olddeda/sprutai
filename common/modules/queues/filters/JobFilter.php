<?php
namespace common\modules\queues\filters;

use DateTime;

use common\modules\queues\records\PushQuery;
use common\modules\queues\records\PushRecord;

/**
 * Class JobFilter
 * @package common\modules\queues\filters
 */
class JobFilter extends BaseFilter
{
    const IS_WAITING = 'waiting';
    const IS_IN_PROGRESS = 'in-progress';
    const IS_DONE = 'done';
    const IS_SUCCESS = 'success';
    const IS_BURIED  = 'buried';
    const IS_FAILED = 'failed';
    const IS_STOPPED = 'stopped';

    public $is;
    public $sender;
    public $class;
    public $pushed;
    public $contains;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['is', 'string'],
            ['is', 'in', 'range' => array_keys($this->scopeList())],
            ['sender', 'string'],
            ['class', 'string'],
            ['pushed', 'string'],
            ['pushed', 'match', 'pattern' => '/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/'],
            ['contains', 'string'],
            [['is', 'sender', 'class', 'pushed', 'contains'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'is' => 'Scope',
            'sender' => 'Sender',
            'class' => 'Job',
            'pushed' => 'Pushed',
            'contains' => 'Contains',
        ];
    }

    /**
     * @return array
     */
    public function scopeList() {
        return [
            self::IS_WAITING => 'Waiting',
            self::IS_IN_PROGRESS => 'In progress',
            self::IS_DONE => 'Done',
            self::IS_SUCCESS => 'Done successfully',
            self::IS_BURIED => 'Buried',
            self::IS_FAILED => 'Has failed attempts',
            self::IS_STOPPED => 'Stopped',
        ];
    }

    /**
     * @return array
     */
    public function senderList() {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('push.sender_name')
                ->groupBy('push.sender_name')
                ->orderBy('push.sender_name')
                ->column();
        }, 3600);
    }

    /**
     * @return array
     */
    public function classList() {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('push.job_class')
                ->groupBy('push.job_class')
                ->orderBy('push.job_class')
                ->column();
        }, 3600);
    }

    /**
     * @return PushQuery
     */
    public function search() {
        $query = PushRecord::find();
        if ($this->hasErrors()) {
            return $query->andWhere('1 = 0');
        }

        $query->andFilterWhere(['push.sender_name' => $this->sender]);
        $query->andFilterWhere(['like', 'push.job_class', $this->class]);
        $this->filterDateRange($query, 'push.pushed_at', $this->pushed);
        $query->andFilterWhere(['like', 'push.job_data', $this->contains]);

        if ($this->is === self::IS_WAITING) {
            $query->waiting();
        }
        elseif ($this->is === self::IS_IN_PROGRESS) {
            $query->inProgress();
        }
        elseif ($this->is === self::IS_DONE) {
            $query->done();
        }
        elseif ($this->is === self::IS_SUCCESS) {
            $query->success();
        }
        elseif ($this->is === self::IS_BURIED) {
            $query->buried();
        }
        elseif ($this->is === self::IS_FAILED) {
            $query->hasFails();
        }
        elseif ($this->is === self::IS_STOPPED) {
            $query->stopped();
        }

        return $query;
    }

    /**
     * @return array
     */
    public function searchClasses() {
        return $this->search()
            ->select(['name' => 'push.job_class', 'count' => 'COUNT(*)'])
            ->groupBy(['name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function searchSenders() {
        return $this->search()
            ->select(['name' => 'push.sender_name', 'count' => 'COUNT(*)'])
            ->groupBy(['name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @param PushQuery $query
     * @param string $name
     * @param string $value
     */
    private function filterDateRange(PushQuery $query, $name, $value) {
        $limits = explode(' - ', $value, 2);
        if (count($limits) === 2) {
            $begin = DateTime::createFromFormat('Y-m-d', $limits[0]);
            $end = DateTime::createFromFormat('Y-m-d', $limits[1]);
            if ($begin && $end) {
                $begin->setTime(0, 0, 0);
                $end->setTime(23, 59, 59);
                $query->andWhere(['between', $name, $begin->getTimestamp(), $end->getTimestamp()]);
            }
        }
    }
}
