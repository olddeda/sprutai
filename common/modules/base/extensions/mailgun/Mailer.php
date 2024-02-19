<?php
namespace common\modules\base\extensions\mailgun;

use Yii;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;

use Mailgun\Mailgun;

/**
 * Mailer implements a mailer based on Mailgun.
 *
 * To use Mailer, you should configure it in the application configuration like the following,
 *
 * ~~~
 * 'components' => [
 *     ...
 *     'mailer' => [
 *         'class' => 'common\modules\base\extensions\mailgun',
 *         'key' => 'key-example',
 *         'domain' => 'mg.example.com',
 *     ],
 *     ...
 * ],
 * ~~~
 *
 * To send an email, you may use the following code:
 *
 * ~~~
 * Yii::$app->mailer->compose('contact/html', ['contactForm' => $form])
 *     ->setFrom('from@domain.com')
 *     ->setTo($form->email)
 *     ->setSubject($form->subject)
 *     ->send();
 * ~~~
 */
class Mailer extends BaseMailer
{
    /**
     * @var string message default class name.
     */
    public $messageClass = 'common\modules\base\extensions\mailgun\Message';

    /**
     * @var string Mailgun API credentials.
     */
    public $key;

    /**
     * @var string Mailgun domain.
     */
    public $domain;

    /**
     * @var string Mailgun endpoint.
     */
    public $endpoint = null;

    /**
     * @var Mailgun Mailgun instance.
     */
    private $_mailgun;

    /**
     * @return Mailgun Mailgun instance.
     * @throws InvalidConfigException
     */
    public function getMailgun() {
        if (!is_object($this->_mailgun)) {
            $this->_mailgun = $this->createMailgun();
        }

        return $this->_mailgun;
    }

    /**
     * @inheritdoc
     */
    protected function sendMessage($message) {
        Yii::info('Sending email', __METHOD__);

        $result = $this->getMailgun()->messages()->send(
            $this->domain,
            $message->getMessageBuilder()->getMessage()
        );

        return is_object($result) ? true : false;
    }

    /**
     * Creates Mailgun instance.
     * @return Mailgun Mailgun instance.
     * @throws InvalidConfigException if required params are not set.
     */
    protected function createMailgun() {
        if (!$this->key) {
            throw new InvalidConfigException('Mailer::key must be set.');
        }
        if (!$this->domain) {
            throw new InvalidConfigException('Mailer::domain must be set.');
        }
        return $this->endpoint ? Mailgun::create($this->key, $this->endpoint) : Mailgun::create($this->key);
    }
}
