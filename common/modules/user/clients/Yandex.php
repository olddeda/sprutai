<?php

namespace common\modules\user\clients;

use Yii;
use yii\authclient\clients\Yandex as BaseYandex;

class Yandex extends BaseYandex implements ClientInterface
{
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_yandex');
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
        $emails = isset($this->getUserAttributes()['emails']) ? $this->getUserAttributes()['emails'] : null;
        if ($emails !== null && isset($emails[0])) {
            return $emails[0];
        }
		else {
            return null;
        }
    }

    /**
	 * @inheritdoc
	 */
    public function getUsername() {
        return isset($this->getUserAttributes()['login']) ? $this->getUserAttributes()['login'] : null;
    }
}
