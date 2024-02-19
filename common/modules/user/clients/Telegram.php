<?php
namespace common\modules\user\clients;

use Yii;

use yii\authclient\BaseClient;

class Telegram extends BaseClient implements ClientInterface
{
	/**
	 * @inheritdoc
	 */
	protected function defaultTitle() {
		return Yii::t('user', 'social_telegram');
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function initUserAttributes() {
		return [];
	}
	
	/**
	 * @return string
	 */
	public function getIcon() {
		return $this->getName();
	}
	
	public function getEmail() {
		return;
	}
	
	/**
	 * @inheritdoc
	 */
	public function getUsername() {
		return;
	}
}
