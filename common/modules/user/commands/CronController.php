<?php
namespace common\modules\user\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Json;

use common\modules\user\models\User;
use common\modules\user\models\UserAccount;

use common\modules\telegram\models\TelegramUser;

class CronController extends Controller
{
	public function actionTelegramUsernameUpdate() {
		
		$count = 0;
		
		/** @var UserAccount[] $accounts */
		$accounts = UserAccount::find()->where(['provider' => 'telegram', 'username' => null])->all();
		if ($accounts) {
			foreach ($accounts as $account) {
				/** @var TelegramUser $telegramUser */
				$telegramUser = TelegramUser::find()->where(['id' => $account->client_id])->one();
				if ($telegramUser && $telegramUser->username) {
					$account->username = $telegramUser->username;
					$account->save();
					$count++;
				}
			}
		}
		
		echo "Update ".$count.PHP_EOL;
	}
	
	public function actionGithubUsernameUpdate() {
		
		$count = 0;
		
		/** @var UserAccount[] $accounts */
		$accounts = UserAccount::find()->where(['provider' => 'github', 'username' => null])->all();
		if ($accounts) {
			foreach ($accounts as $account) {
				if ($account->data) {
					$data = Json::decode($account->data, false);
					if ($data->login) {
						$account->username = $data->login;
						$account->save();
						$count++;
					}
				}
			}
		}
		
		echo "Update ".$count.PHP_EOL;
	}
}