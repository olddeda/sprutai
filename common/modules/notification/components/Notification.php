<?php
namespace common\modules\notification\components;

use Yii;
use yii\base\Component;
use yii\db\Query;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Debug;

use common\modules\notification\jobs\NotificationJob;

use common\modules\user\models\User;
use common\modules\user\models\UserAccount;
use common\modules\user\models\UserSubscribe;
use common\modules\user\helpers\enum\Subscribe;

/**
 * Class Notification
 * @package common\modules\notification\components
 */
class Notification extends Component
{
	private static $_emails;
	private static $_telegramUsers;
	private static $_flags;
	
	public function init() {
		parent::init();
		
		$this->_loadEmails();
		$this->_loadTelegramUsers();
		$this->_loadFlags();
	}
	
	/**
	 * Queue all
	 * @param array $toIds
	 * @param string $subject
	 * @param string $message
	 * @param string $type
	 */
	public function queue(array $toIds, string $subject, string $message, string $type) {
		$this->queueEmail($toIds, $subject, $message, $type);
		$this->queueTelegram($toIds, $message, $type);
	}
	
	/**
	 * Queue mail
	 *
	 * @param array $toIds
	 * @param string $subject
	 * @param string $message
	 * @param string $type
	 */
	public function queueEmail(array $toIds, string $subject, string $message, string $type) {
		$emails = [];
		foreach ($toIds as $toId) {
			if ($email = $this->_getEmail($toId, $type))
				$emails[] = $email;
		}
		
		Yii::$app->queue->push(new NotificationJob([
			'triggerClass' => self::class,
			'name' => 'Email',
			'data' => [
				'to' => $emails,
				'subject' => $subject,
				'message' => $message,
			],
		]));
	}

    /**
     * Queue mail
     *
     * @param array $emails
     * @param string $subject
     * @param string $message
     * @param array $data
     */
    public function queueEmails(array $emails, string $subject, string $message, array $data = []) {
        $data = ArrayHelper::merge([
            'to' => $emails,
            'subject' => $subject,
            'message' => $message,
        ], $data);

        Yii::$app->queue->push(new NotificationJob([
            'triggerClass' => self::class,
            'name' => 'Email',
            'data' => $data,
        ]));
    }
	
	/**
	 * Queue telegram
	 *
	 * @param array $toIds
	 * @param string $message
	 * @param string $type
	 */
	public function queueTelegram(array $toIds, string $message, string $type) {
		$userIds = [];
		foreach ($toIds as $toId) {
			if ($userId = $this->_getTelegramUser($toId, $type))
				$userIds[] = $userId;
		}

		$this->queueTelegramIds($userIds, $message);
	}

    /**
     * Queue telegram
     *
     * @param array $toIds
     * @param string $message
     * @param array $params
     */
	public function queueTelegramIds(array $toIds, string $message, array $params = []) {
		Yii::$app->queue->push(new NotificationJob([
			'triggerClass' => self::class,
			'name' => 'Telegram',
			'data' => [
				'toId' => $toIds,
				'message' => $message,
                'params' => $params,
			],
		]));
	}
	
	/**
	 * Check and get user email
	 * @param integer $userId
	 * @param string $type
	 *
	 * @return string|null
	 */
	private function _getEmail(int $userId, string $type) {
		if (isset(static::$_emails[$userId]) && isset(static::$_flags[$userId])) {
			if (in_array(static::$_flags[$userId][$type], [Subscribe::ALL, Subscribe::EMAIL]))
				return static::$_emails[$userId];
		}
		return null;
	}
	
	/**
	 * Check and get telegram user id
	 * @param integer $userId
	 * @param string $type
	 *
	 * @return string|null
	 */
	private function _getTelegramUser(int $userId, string $type) {
		if (isset(static::$_telegramUsers[$userId]) && isset(static::$_flags[$userId])) {
			if (in_array(static::$_flags[$userId][$type], [Subscribe::ALL, Subscribe::TELEGRAM]))
				return static::$_telegramUsers[$userId];
		}
		return null;
	}
	
	/**
	 * Load emails
	 */
	private function _loadEmails() {
		if (is_null(static::$_emails)) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.User::tableName();
			
			$ids = [];
			$query = (new Query(null, $dependency))
				->cache()
				->select('id, email')
				->from(User::tableName())
				->where('confirmed_at IS NOT NULL AND deleted_at IS NULL AND blocked_at IS NULL');
			foreach ($query->batch() as $rows) {
				$ids = ArrayHelper::merge($ids, (ArrayHelper::map($rows, 'id', 'email')));
			}
			static::$_emails = $ids;
		}
	}
	
	/**
	 * Load telegram users
	 */
	private function _loadTelegramUsers() {
		if (is_null(static::$_telegramUsers)) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.UserAccount::tableName();
			
			$ids = [];
			$query = (new Query(null, $dependency))
				->cache()
				->select('user_id, client_id')
				->from(UserAccount::tableName())
				->andWhere(['provider' => 'telegram']);
			foreach ($query->batch() as $rows) {
				$ids = ArrayHelper::merge($ids, (ArrayHelper::map($rows, 'user_id', 'client_id')));
			}
			static::$_telegramUsers = $ids;
		}
	}
	
	/**
	 * Load subscribe flags
	 */
	private function _loadFlags() {
		if (is_null(static::$_flags)) {
			$dependency = new DbDependency();
			$dependency->sql = 'SELECT MAX(updated_at) FROM '.UserSubscribe::tableName();
			
			$ids = [];
			$query = (new Query(null, $dependency))
				->cache()
				->select('*')
				->from(UserSubscribe::tableName());
			foreach ($query->batch() as $rows) {
				foreach ($rows as $row) {
					$ids[$row['user_id']] = [
						'system' => $row['flag_system'],
						'author' => $row['flag_author'],
						'article' => $row['flag_article'],
						'news' => $row['flag_news'],
						'blog' => $row['flag_blog'],
						'project' => $row['flag_project'],
						'plugin' => $row['flag_plugin'],
						'item' => $row['flag_item'],
						'comment' => $row['flag_comment'],
						'vote' => $row['flag_vote'],
						'qa' => $row['flag_qa'],
					];
				}
			}
			static::$_flags = $ids;
		}
	}
}