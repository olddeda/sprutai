<?php
namespace common\modules\base\components\bugsnag;

use yii\web\ErrorHandler;

/**
 * Class BugsnagWebErrorHandler
 * @package common\modules\base\components\bugsnag
 */
class BugsnagWebErrorHandler extends ErrorHandler
{
    use BugsnagErrorHandlerTrait;
}
