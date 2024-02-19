<?php
namespace common\modules\telegram\commands;

use common\modules\base\extensions\telegram\Telegram;
use common\modules\telegram\models\TelegramChat;
use common\modules\telegram\models\TelegramChatUser;
use Yii;
use yii\console\Controller;

/**
 * Class CronController
 * @package common\modules\telegram\commands
 *
 * @property \common\modules\telegram\Module $module
 */

class CronController extends Controller
{
	/**
	 * Run all commands in cicle
	 */
	public function actionIndex () {
		
		/**
		 * @var \common\modules\user\models\UserAccount $account
		 */
		$account = UserAccount::find()->where('code = :code AND provider = :provider', [
			':code' => $code,
			':provider' => 'telegram',
		])->one();
		if ($account) {
			$account->user_id = Yii::$app->user->id;
			$account->code = null;
			$account->save();
			
			// Set message
			Yii::$app->getSession()->setFlash('success', Yii::t('telegram', 'message_link_success'));
			
			Yii::$app->telegram->sendMessage([
				'chat_id' => $account->client_id,
				'text' => Yii::t('telegram', 'message_link_telegram_success'),
			]);
			
			return $this->redirect(['/user/settings/subscribe']);
		}
		else {
			throw new NotFoundHttpException(Yii::t('telegram', 'message_link_failed'));
		}
	}
	
	public function actionUpdateChatsCount() {

        /** @var \common\modules\base\extensions\telegram\Telegram $telegram */
        $telegram = Yii::$app->telegram;

        $chats = TelegramChat::find()->where([
            'status' => 1
        ])->all();
        foreach ($chats as $chat) {
            $response = $telegram->getChatMembersCount([
                'chat_id' => $chat->identifier,
            ]);
            if ($response->ok) {
                $chat->members_count = (int)$response->result;
                $chat->save();
            }
            else {
                echo $chat->title.':'.$chat->identifier.' => '.$response->description.PHP_EOL;
            }
        }
	}

	public function actionClearNewMember() {

	    /** @var Telegram $telegram */
	    $telegram = Yii::$app->telegram;

	    $models = TelegramChatUser::find()
            ->where([
	            '<', 'expire_at', time(),
            ])
            ->andWhere('status = 0')
            ->all()
        ;

	    /** @var TelegramChatUser $model */
        foreach ($models as $model) {

	        $result = $telegram->kickChatMember([
                'chat_id' => $model->chat_id,
                'user_id' => $model->user_id,
            ]);

	        if ($result->ok) {
	            $result = $telegram->unbanChatMember([
                    'chat_id' => $model->chat_id,
                    'user_id' => $model->user_id,
                ]);

	            if ($result->ok) {

                    if (is_array($model->params)) {
                        if (isset($model->params['enter_message_id'])) {
                            $result = $telegram->deleteMessage([
                                'chat_id' => $model->chat_id,
                                'message_id' => $model->params['enter_message_id'],
                            ]);
                        }
                        if (isset($model->params['message_id'])) {
                            $result = $telegram->deleteMessage([
                                'chat_id' => $model->chat_id,
                                'message_id' => $model->params['message_id'],
                            ]);
                        }
                    }
                }
            }

	        $model->delete(false);
        }
    }
}