<?php
namespace common\modules\user\clients;

use Yii;
use yii\authclient\clients\Google as BaseGoogle;

class Google extends BaseGoogle implements ClientInterface
{
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_google');
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

    /**
	 * @inheritdoc
	 */
    public function getUsername() {
        return;
    }
}
