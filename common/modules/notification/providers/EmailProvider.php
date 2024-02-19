<?php

namespace common\modules\notification\providers;

use Yii;
use yii\base\Exception;
use yii\swiftmailer\Mailer;

use common\modules\notification\components\NotificationEvent;
use common\modules\notification\components\Provider;

/**
 * Class EmailProvider
 * @package common\modules\notification\providers
 */
class EmailProvider extends Provider
{
	/**
	 * @var string
	 */
    public $emailViewPath = '@common/modules/notification/tpl';
	
	/**
	 * @var array
	 */
    public $layouts = [
        'text' => '@common/modules/notification/tpl/layouts/text',
        'html' => '@common/modules/notification/tpl/layouts/html',
    ];
	
	/**
	 * @var array
	 */
    public $views = [
        'text' => 'email-base.text.tpl.php',
        'html' => 'email-base.html.tpl.php',
    ];
	
	/**
	 * @var array
	 */
    public $config = [
        'mailer' => [],
    ];

    /**
     * @param NotificationEvent $notification
     *
     * @throws Exception
     */
    public function send(NotificationEvent $notification) {
        if (empty($notification->to))
        	return;

        $provider = 'mailer';

        if (!empty($this->config['mailer'])) {
            $provider = $this->config['mailer'];
        }

        /** @var Mailer $mailer */
        $mailer = Yii::$app->get($provider);

        if (!$mailer){
            throw new Exception();
        }

        $mailer->view->params['notification'] = $notification;

        $mailer->viewPath = ($notification->path) ? $notification->path : $this->emailViewPath;

        if (!empty($notification->from)){
            $from = $notification->from;
        }
        else {
            if (isset($this->config['from'])) {
                $from = $this->config['from'];
            }
            else {
                $from = isset(Yii::$app->params['email.noreply']) ? Yii::$app->params['email.noreply'] : 'admin@localhost';
            }
        }

        $params = array_merge($notification->params, [
          	'subject' => $notification->subject,
          	'message' => $notification->message
        ]);

        if (isset($notification->layouts['text'])) {
            $mailer->textLayout = $notification->layouts['text'];
        }
        elseif (isset($this->layouts['text'])) {
            $mailer->textLayout = $this->layouts['text'];
        }
        
        if (isset($notification->layouts['html'])) {
            $mailer->htmlLayout = $notification->layouts['html'];
        }
        elseif (isset($this->layouts['html'])) {
            $mailer->htmlLayout = $this->layouts['html'];
        }

        $views = ($notification->view) ? $notification->view : $this->views;

        if (is_array($notification->to)) {
        	$emails = (is_array(reset($notification->to))) ? $notification->to : [$notification->to];
        }
        else {
            $emails = [$notification->to];
        }
        
        $compose = $mailer->compose($views, $params)->setFrom($from)->setSubject($notification->subject);
        
        foreach ($emails as $email) {
        	if (is_array($email)) {
        		foreach ($email as $e) {
        			$this->_sendEmail($compose, $e);
				}
			}
			else {
				$this->_sendEmail($email);
			}
        }

        unset($mailer);
    }
    
    private function _sendEmail($compose, $email) {
		$status = $compose->setTo($email)->send();
		$this->status[$email] = $status;
	}
}
