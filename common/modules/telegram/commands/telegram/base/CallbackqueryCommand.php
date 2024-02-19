<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use common\modules\telegram\models\TelegramChatUser;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\AnswerCallbackQuery;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * Callback query command
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var callable[]
     */
    protected static $callbacks = [];

    /**
     * Command execute method
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute() {
        $update = $this->getUpdate();
        $callback_query = $update->getCallbackQuery();
        $user_id = $callback_query->getFrom()->getId();
        $data = $callback_query->getData();

        if (preg_match('/^\/([^\s@]+)/', $data, $command)) {
            return $this->getTelegram()->executeCommand($command[1], $update);
        }

        if (strpos($data, 'new_member') !== false) {
            $this->parseNewMember($data);
        }

        if ($callback_query->getMessage()) {
            $conversation = new Conversation($user_id, $callback_query->getMessage()->getChat()->getId());
            if ($conversation->exists() && ($command = $conversation->getCommand())) {
                return $this->telegram->executeCommand($command);
            }
        }

        // Call all registered callbacks.
        foreach (self::$callbacks as $callback) {
            $callback($this->getUpdate()->getCallbackQuery());
        }

        return Request::answerCallbackQuery(['callback_query_id' => $this->getUpdate()->getCallbackQuery()->getId()]);
    }

    public function parseNewMember($data) {
        Request::answerCallbackQuery(['callback_query_id' => $this->getUpdate()->getCallbackQuery()->getId()]);

        $tmp = explode(':', $data);
        $number = $tmp[1];

        $chatId = $this->getCallbackQuery()->getMessage()->getChat()->getId();
        $userId = $this->getCallbackQuery()->getFrom()->getId();

        $model = TelegramChatUser::find()->where([
            'chat_id' => $chatId,
            'user_id' => $userId,
            'status' => 0,
        ])->one();
        if ($model) {
            if ($model->number == $number) {
                $result = Request::restrictChatMember([
                    'chat_id' => $chatId,
                    'user_id' => $userId,
                    'permissions' => [
                        'can_send_messages' => true,
                        'can_send_media_messages' => true,
                        'can_send_polls' => true,
                        'can_send_other_messages' => true,
                        'can_add_web_page_previews' => true,
                        'can_invite_users' => true,
                        'can_change_info' => false,
                        'can_pin_messages' => false,
                    ],
                ]);
                if ($result->isOk()) {
                    $this->_deleteTelegramUserChat($model, false);
                }
            }
            else {
                $result = Request::kickChatMember([
                    'chat_id' => $chatId,
                    'user_id' => $userId,
                ]);
                if ($result->isOk()) {
                    Request::unbanChatMember([
                        'chat_id' => $chatId,
                        'user_id' => $userId,
                    ]);

                    $this->_deleteTelegramUserChat($model);
                }
            }
        }
    }

    private function _deleteTelegramUserChat($model, $removeEnterMessage = true) {
        if (is_array($model->params)) {
            if ($removeEnterMessage && isset($model->params['enter_message_id'])) {
                Request::deleteMessage([
                    'chat_id' => $model->chat_id,
                    'message_id' => $model->params['enter_message_id'],
                ]);
            }

            if (isset($model->params['message_id'])) {
                Request::deleteMessage([
                    'chat_id' => $model->chat_id,
                    'message_id' => $model->params['message_id'],
                ]);
            }
        }

        if ($removeEnterMessage) {
            $model->delete(true);
        }
        else {
            $model->status = 1;
            $model->save();
        }
    }
}