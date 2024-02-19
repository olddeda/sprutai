<?php
namespace common\modules\user\clients;

use Yii;
use yii\authclient\clients\GitHub as BaseGitHub;

class GitHub extends BaseGitHub implements ClientInterface
{
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_github');
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
        return isset($this->getUserAttributes()['login']) ? $this->getUserAttributes()['login'] : null;
    }
}
