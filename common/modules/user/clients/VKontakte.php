<?php

namespace common\modules\user\clients;

use Yii;
use yii\authclient\clients\VKontakte as BaseVKontakte;

class VKontakte extends BaseVKontakte implements ClientInterface
{
    /**
	 * @inheritdoc
	 */
    public $scope = 'email';

	/**
	 * @inheritdoc
	 */
	public function getTitle() {
		return Yii::t('user', 'social_vkontakte');
	}
	
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_vkontakte');
	}
	
	/**
	 * @return string
	 */
	public function getIcon() {
		return 'vk';
	}
	
	/**
	 * @inheritdoc
	 */
    public function getEmail() {
        return $this->getAccessToken()->getParam('email');
    }

    /**
	 * @inheritdoc
	 */
    public function getUsername() {
        return isset($this->getUserAttributes()['screen_name']) ? $this->getUserAttributes()['screen_name'] : null;
    }
}
