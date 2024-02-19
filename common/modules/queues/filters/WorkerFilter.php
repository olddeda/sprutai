<?php
namespace common\modules\queues\filters;

use common\modules\queues\records\WorkerQuery;
use common\modules\queues\records\WorkerRecord;

/**
 * Class WorkerFilter
 * @package common\modules\queues\filters
 */
class WorkerFilter extends BaseFilter
{
    /**
     * @return WorkerQuery
     */
    public function search() {
        return WorkerRecord::find();
    }
}
