<?php
namespace common\modules\base\extensions\sendgrid;

use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;
use yii\mail\MailerInterface;
use yii\mail\MessageInterface;

use SendGrid;

/**
 * Class Mailer
 * @package common\modules\base\extensions\sendgrid
 */
class Mailer extends BaseMailer implements MailerInterface
{
    /**
     * @var string key for the sendgrid api
     */
    public $apiKey;

    /**
     * @var array a list of options for the sendgrid api
     */
    public $options = [];

    /**
     * @var SendGrid Send grid mailer instance
     */
    private $_sendGridMailer;

    /**
     * @return SendGrid Send grid mailer instance
     * @throws InvalidConfigException
     */
    public function getSendGridMailer() {
        if (!is_object($this->_sendGridMailer)) {
            $this->_sendGridMailer = $this->createSendGridMailer();
        }
        return $this->_sendGridMailer;
    }

    /**
     * Create send grid mail instance with stored params
     * @return SendGrid
     * @throws InvalidConfigException
     */
    public function createSendGridMailer() {
        if ($this->apiKey) {
            return new SendGrid($this->apiKey, $this->options);
        }
        throw new InvalidConfigException("You must configure mailer.");
    }

    /**
     * @param MessageInterface $message
     * @return bool
     * @throws SendgridException
     */
    public function sendMessage($message) {
        $response = $this->sendGridMailer->send($message->sendGridMessage);
        if ($response->statusCode() >= 400) {
            throw new SendgridException(sprintf(
                'Sendgrid returned %d with "%s" error',
                $response->statusCode(),
                $response->body()
            ));
        }
        return true;
    }
}
