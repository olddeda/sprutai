<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use common\modules\telegram\commands\telegram\SystemCommand;
use common\modules\telegram\models\TelegramChat;
use common\modules\telegram\models\TelegramChatUser;
use common\modules\telegram\models\TelegramStop;
use common\modules\telegram\models\TelegramStopItem;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Yii;
use yii\db\Connection;

/**
 * Generic command
 */
class GenericCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'Generic';
	
	/**
	 * @var string
	 */
	protected $description = 'Handles generic commands or is executed by default when a command is not found';
	/**
	 * @var string
	 */
	protected $version = '1.1.0';
	
	/**
	 * Command execute method
	 *
	 * @return ServerResponse
	 */
	public function execute()
	{
		$message = $this->getMessage();

		if (is_null($message)) {
		    return Request::emptyResponse();
        }

		$this->newChatMembers();

        if (TelegramChat::find()->where([
            'identifier' => $message->getChat()->getId(),
            'is_partner' => 1
        ])->exists()) {
            return;
        }

        $text = mb_strtolower($message->getText(true));
        $text = str_replace('  ', ' ', $text);

        if (!is_null($text)) {
            foreach (TelegramStop::keywords() as $k) {
                $keywordId = $k['id'];
                $keywordText = mb_strtolower($k['keyword']);
                if (strpos($text, $keywordText) !== false) {

                    $response = Request::deleteMessage([
                        'chat_id' => $message->getChat()->getId(),
                        'message_id' => $message->getMessageId(),
                    ]);
                    if ($response->isOk()) {

                        /** @var TelegramStopItem $model */
                        $model = new TelegramStopItem();
                        $model->telegram_stop_id = $keywordId;
                        $model->telegram_chat_id = $message->getChat()->getId();
                        $model->telegram_user_id = $message->getFrom()->getId();
                        $model->text = $text;
                        $model->created_at = time();
                        $model->updated_at = time();
                        $model->save();
                    }
                }
            }
        }

        $user = $this->getFrom();
        if ($user) {
            $username = mb_strtolower($user->getUsername());
            $lastName = mb_strtolower($user->getLastName());
            $firstName = mb_strtolower($user->getFirstName());

            if (!is_null($username) || !is_null($lastName) || !is_null($firstName)) {
                foreach (TelegramStop::keywords() as $k) {
                    $keywordId = $k['id'];
                    $keywordText = mb_strtolower($k['keyword']);
                    $keywordKick = $k['kick'];
                    if (
                        $keywordKick &&
                        (
                            $username == $keywordText ||
                            strpos($lastName, $keywordText) !== false ||
                            strpos($firstName, $keywordText) !== false)
                    )
                    {
                        $response = Request::kickChatMember([
                            'chat_id' => $message->getChat()->getId(),
                            'user_id' => $message->getFrom()->getId(),
                        ]);

                        if ($response->isOk()) {

                            /** @var TelegramStopItem $model */
                            $model = new TelegramStopItem();
                            $model->telegram_stop_id = $keywordId;
                            $model->telegram_chat_id = $message->getChat()->getId();
                            $model->telegram_user_id = $message->getFrom()->getId();
                            $model->text = 'Kick user';
                            $model->created_at = time();
                            $model->updated_at = time();
                            $model->save();
                        }
                    }
                }
            }
        }
		
		return Request::emptyResponse();
	}

	public function newChatMembers() {
	    if (!$this->getMessage()) {
	        return;
        }

        if (!TelegramChat::find()->where([
            'identifier' => $this->getMessage()->getChat()->getId(),
            'is_spam_protect' => 1
        ])->exists()) {
            return;
        }

        $this->deleteMessages();

	    $users = $this->getMessage()->getNewChatMembers();
	    $chatId = $this->getMessage()->getChat()->getId();
	    $messageId = $this->getMessage()->getMessageId();

	    if (is_array($users)) {
            foreach ($users as $user) {
                if (TelegramChatUser::find()->where([
                    'status' => 10,
                    'user_id' => $user->getId(),
                ])->exists()) {
                    Request::sendMessage([
                        'chat_id' => $chatId,
                        'reply_to_message_id' => $messageId,
                        'text' => 'Оооо, я тебя знаю! Заходи, заходи!',
                    ]);
                    continue;
                }
                $numberFirst = rand(5, 20);
                $numberLast = rand(5, 20);
                $number = $numberFirst + $numberLast;

                $model = TelegramChatUser::find()->where([
                    'chat_id' => $chatId,
                    'user_id' => $user->getId(),
                ])->one();

                if (is_null($model)) {
                    $model = new TelegramChatUser();
                    $model->chat_id = $chatId;
                    $model->user_id = $user->getId();
                }

                $model->number = $number;
                $model->expire_at = time() + 60;
                $model->status = 0;
                $model->params = [
                    'enter_message_id' => $messageId
                ];

                if ($model->save()) {
                    $message = 'Докажите что вы не бот. В течении минуты вам нужно ответить сколько будет '.$numberFirst.' + '.$numberLast.'?';

                    $numbers = [$number];
                    for ($i = 0; $i < 3; $i++) {
                        $n = null;
                        while (is_null($n)) {
                            $r = rand(5, 20);
                            if ($r != $number) {
                                $n = $r;
                            }
                        }
                        $numbers[] = $n;
                    }
                    shuffle($numbers);

                    $buttons = [];
                    foreach ($numbers as $number) {
                        $buttons[] = [
                            'text' => $number,
                            'callback_data' => 'new_member:'.$number,
                        ];
                    }

                    $result = Request::restrictChatMember([
                        'chat_id' => $chatId,
                        'user_id' => $user->getId(),
                        'permissions' => [
                            'can_send_messages' => false,
                            'can_send_media_messages' => false,
                            'can_send_polls' => false,
                            'can_send_other_messages' => false,
                            'can_add_web_page_previews' => false,
                            'can_change_info' => false,
                            'can_invite_users' => false,
                            'can_pin_messages' => false,
                        ],
                    ]);

                    if ($result->isOk()) {
                        $result = Request::sendMessage([
                            'chat_id' => $chatId,
                            'reply_to_message_id' => $messageId,
                            'text' => $message,
                            'reply_markup' => new InlineKeyboard($buttons),
                        ]);

                        if ($result->isOk()) {
                            $params = $model->params;
                            $params['message_id'] = $result->getResult()->getMessageId();
                            $model->params = $params;
                            $model->save();

                            /** @var Connection $db */
                            $db = Yii::$app->dbTelegram;

                            $messages = $db
                                ->createCommand('
                                    SELECT *
                                    FROM message
                                    WHERE chat_id = :chat_id
                                    AND user_id = :user_id
                                    AND `date` BETWEEN (DATE_SUB(UTC_TIMESTAMP(), INTERVAL 5 MINUTE)) AND UTC_TIMESTAMP()
                                    AND new_chat_members IS NULL
                                ')
                                ->bindValue(':chat_id', $this->getChatId())
                                ->bindValue(':user_id', $this->getUserId())
                                ->queryAll()
                            ;

                            foreach ($messages as $message) {
                                if ($message['id'] == $messageId) {
                                    continue;
                                }

                                Request::deleteMessage([
                                    'chat_id' => $this->getChatId(),
                                    'message_id' => $message['id'],
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function deleteMessages() {
	    if (is_null($this->getMessage())) {
	        return;
        }

	    return;

        if (TelegramChatUser::find()->where([
            'status' => 1,
            'user_id' => $this->getUserId(),
        ])->exists()) {
            return;
        }

        /** @var Connection $db */
        $db = Yii::$app->dbTelegram;

        $messageId = $this->getMessage()->getMessageId();

        $date = $db
            ->createCommand("
                SELECT date
                FROM message
                WHERE chat_id = :chat_id
                AND user_id = :user_id
                AND new_chat_members IS NOT NULL
                AND `date` > '2020-12-17 29:29:29'
            ")
            ->bindValue(':chat_id', $this->getChatId())
            ->bindValue(':user_id', $this->getUserId())
            ->queryScalar()
        ;

        if (!$date) {
            return;
        }

        if (!$this->getText(true)) {
            return;
        }

        $usersIds = [];
        if ($this->getMessage()->getNewChatMembers()) {
            foreach ($this->getMessage()->getNewChatMembers() as $chatMember) {
                $ids[] = $chatMember->getId();
            }
        }

        if (in_array($this->getUserId(), $usersIds)) {
            return;
        }

        Request::deleteMessage([
            'chat_id' => $this->getChatId(),
            'message_id' => $messageId,
        ]);

        $messages = $db
            ->createCommand('
                SELECT *
                FROM message
                WHERE chat_id = :chat_id
                AND user_id = :user_id
                AND `date` BETWEEN (DATE_SUB(UTC_TIMESTAMP(), INTERVAL 10 MINUTE)) AND UTC_TIMESTAMP()
                AND new_chat_members IS NULL
            ')
            ->bindValue(':chat_id', $this->getChatId())
            ->bindValue(':user_id', $this->getUserId())
            ->queryAll()
        ;

        foreach ($messages as $message) {
            if ($message['id'] == $messageId) {
                continue;
            }

            Request::deleteMessage([
                'chat_id' => $this->getChatId(),
                'message_id' => $message['id'],
            ]);
        }
    }
}