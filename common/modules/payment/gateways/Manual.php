<?php
namespace common\modules\payment\gateways;

use common\modules\payment\components\Process;
use common\modules\payment\components\Request;

use common\modules\payment\helpers\enum\Result;
use common\modules\payment\helpers\enum\State;

/**
 * Class Manual
 * @package common\modules\payment\gateways
 */
class Manual extends Base
{
    /**
     * @param string $id
     * @param integer|double $amount
     * @param string $description
     * @param array $params
     * @return \common\modules\payment\components\Process
     */
    public function start($id, $amount, $description, $params) {
        return new Process([
            'transactionId' => $id,
            'state' => State::WAIT_RESULT,
            'result' => Result::SUCCEED,
        ]);
    }

    /**
     * @param Request $requestModel
     * @return Process
     */
    public function callback(Request $requestModel) {
        return new Process([
            'state' => State::COMPLETE,
            'result' => $requestModel->params['result'],
        ]);
    }

}
