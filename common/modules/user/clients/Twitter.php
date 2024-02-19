<?php

namespace common\modules\user\clients;

use Yii;
use yii\authclient\clients\Twitter as BaseTwitter;
use yii\helpers\ArrayHelper;

class Twitter extends BaseTwitter implements ClientInterface
{
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_twitter');
	}
	
	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->getName();
	}
	
	/**
     * @return string
     */
    public function getUsername() {
        return ArrayHelper::getValue($this->getUserAttributes(), 'screen_name');
    }

    /**
     * @return null Twitter does not provide user's email address
     */
    public function getEmail() {
        return null;
    }
}
