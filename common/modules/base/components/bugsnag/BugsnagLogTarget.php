<?php
namespace common\modules\base\components\bugsnag;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;

/**
 * Class BugsnagLogTarget
 * @package common\modules\base\components\bugsnag
 */
class BugsnagLogTarget extends Target
{
	/**
	 * @var array
	 */
    static $bugsnagMessages = [];
    
    public function export() {
        /** @var BugsnagComponent $bugsnag */
        $bugsnag = Yii::$app->bugsnag;

        $bugsnag->messages = ArrayHelper::merge($bugsnag->messages, $this->messages);

        $messages = $this->messages;

        foreach ($messages as $messageData) {
            list($message, $level, $category, $timestamp, $traces, $memoryUsage) = $messageData;

            if (!$bugsnag->inException) {
                if ($bugsnag->sendWarnings && $level === Logger::LEVEL_WARNING) {
                    $bugsnag->notifyCustomWarning($message, $traces);
                }
                if ($level === Logger::LEVEL_ERROR) {
                	
                    if (is_string($message)) {
                        $bugsnag->notifyCustomError($message, $traces);
                    } elseif ($message instanceof \Throwable) {
                        $bugsnag->notifyException($message);
                    } else {
                        $message = VarDumper::dumpAsString($message);
                        $bugsnag->notifyCustomError($message, $traces);
                    }

//                    if ($message instanceof \Throwable) {
//                        $bugsnag->notifyException($message);
//                    }
//
//                    if ($message instanceof \Exception) {
//                        $bugsnag->notifyException($message);
//                    } elseif ($message instanceof \ParseError) {
//                        $message = $message;
//                        $bugsnag->notifyException($message);
//                    } else {
//                        $bugsnag->notifyCustomError($message, $traces);
//                    }
                }
            }
        }
    }
}
