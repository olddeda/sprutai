<?php
namespace common\modules\base\extensions\sendgrid;

use yii\base\Exception;

/**
 * Class SendgridException
 * @package common\modules\base\extensions\sendgrid
 */
class SendgridException extends Exception
{
    public function getName() {
        return 'SendGrid Client Exception';
    }
}
