<?php
namespace common\modules\base\components\bugsnag;

use yii\console\ErrorHandler;

/**
 * Class BugsnagConsoleErrorHandler
 * @package common\modules\base\components\bugsnag
 */
class BugsnagConsoleErrorHandler extends ErrorHandler
{
    use BugsnagErrorHandlerTrait;
}
