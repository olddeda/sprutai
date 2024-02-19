<?php
namespace common\modules\base\extensions\sendgrid;

use yii\mail\MessageInterface as BaseMessageInterface;

/**
 * Interface MessageInterface
 * @package common\modules\base\extensions\sendgrid
 */
interface MessageInterface extends BaseMessageInterface
{
    public function getHtmlBody();

    public function getTextBody();

    public function getTemplateId();

    public function getSubstitutions($index = 0);
}
