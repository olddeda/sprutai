<?php
namespace common\modules\base\components\bugsnag;

use Yii;

/**
 * Trait BugsnagErrorHandlerTrait
 * @package common\modules\base\components\bugsnag
 */
trait BugsnagErrorHandlerTrait
{
	/**
	 * @param $exception
	 */
    public function logException($exception) {
        parent::logException($exception);
        
        /** @var BugsnagComponent $bugsnag */
        $bugsnag = Yii::$app->bugsnag;
        if ($bugsnag) {
            $bugsnag->flush();
        }
    }

    public function handleException($exception) {
        /** @var BugsnagComponent $bugsnag */
//        $bugsnag = \Yii::$app->bugsnag;
//        if ($bugsnag) {
//            $bugsnag->notifyException($exception);
//        }

        parent::handleException($exception);
    }

    public function handleFatalError() {
        /** @var BugsnagComponent $bugsnag */
//        $bugsnag = \Yii::$app->bugsnag;
//        if ($bugsnag) {
//            $bugsnag->flush();
//        }

        parent::handleFatalError();
    }
}
