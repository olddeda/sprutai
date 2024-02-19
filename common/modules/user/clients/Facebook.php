<?php

namespace common\modules\user\clients;

use Yii;
use yii\authclient\clients\Facebook as BaseFacebook;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class Facebook extends BaseFacebook implements ClientInterface
{
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_facebook');
	}
	
	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->getName();
	}

    /**
	 * @inheritdoc
	 */
    public function getEmail() {
        return isset($this->getUserAttributes()['email']) ? $this->getUserAttributes()['email'] : null;
    }

    /** @inheritdoc */
    public function getUsername() {
        return;
    }
}