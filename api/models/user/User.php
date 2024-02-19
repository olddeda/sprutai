<?php
namespace api\models\user;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\Link;
use yii\helpers\Url;

use common\modules\user\models\User as BaseUser;

use api\models\user\query\UserQuery;

/**
 * Class User
 * @package api\models\user
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="username", type="string", description="username"),
 *     @OA\Property(property="name", type="string", description="Имя"),
 *     @OA\Property(property="image", type="object",
 *         @OA\Property(property="path", type="string", description="Путь к изображению"),
 *         @OA\Property(property="file", type="string", description="Название файла изображения")
 *     ),
 *     @OA\Property(property="profile", type="object", ref="#/components/schemas/UserProfile"),
 *     @OA\Property(property="contacts", type="object",
 *         @OA\Property(property="telegram", type="string", description="Логин в телеграмме")
 *     ),
 *     @OA\Property(property="stats", type="object",
 *         @OA\Property(property="catalog_items", type="string", description="Количество устройств пользователя")
 *     ),
 *     @OA\Property(property="is_online", type="boolean", description="Признак онлайна"),
 *     @OA\Property(property="created_at", type="integer", description="Дата регистрации"),
 *     @OA\Property(property="last_visit_at", type="integer", description="Последний визит")
 * )
 */
class User extends BaseUser
{
    /** @var int */
    public $count_catalog_items;

	/**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the columns whose values have been populated into this record.
	 */
	public function fields() {

	    $fields = [
		    'id',
            'username',
            'name' => function ($data) {
		        return $data->getAuthorName();
            },
            'image' => function ($data) {
		        return $data->getMediaImage();
            },
            'profile',
            'contacts' => function ($data) {
	            return [
	                'telegram' => $data->telegram && $data->telegram->username ? $data->telegram->username : null,
                ];
            },
            'is_online' => function($data) {
		        return $data->getIsOnline();
            },
            'stats' => function ($data) {
	            return [
	                'catalog_items' => $data->count_catalog_items,
                ];
            },
            'created_at',
            'last_visit_at' => function ($data) {
	            return $data->lastvisit_at;
            },
        ];
	    if ($this->id === Yii::$app->user->id) {
	        $fields['rights'] = function ($data) {
	            return $data->getRights();
            };
        }
	    return $fields;
	}

	/**
	 * @inheritdoc
	 *
	 * The default implementation returns the names of the relations that have been populated into this record.
	 */
	public function extraFields() {
		return ['profile'];
	}

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function getLinks() {
		return [
			Link::REL_SELF => Url::to(['user/view', 'id' => $this->id], true),
		];
	}

    /**
     * @return ActiveQuery
     */
    public static function find() {
        return new UserQuery(get_called_class());
    }

	/**
	 * Get profile
	 * @return ActiveQuery
	 */
	public function getProfile() {
		return $this->hasOne(UserProfile::class, ['user_id' => 'id']);
	}

	/**
	 * Get token
	 * @return array|null|\api\models\UserToken
	 */
	public function getToken() {
		$query = $this->hasMany(UserToken::class, ['user_id' => 'id']);
		$query->andWhere(UserToken::tableName().'.type = :type', [
			':type' => UserToken::TYPE_API,
		]);
		$token = $query->one();
		if ($token && !$token->getIsExpired())
			return $token;
		return null;
	}

	/**
	 * Get token data
	 * @return array
	 */
	public function getTokenData() {
		$token = $this->getToken();
		return [
			'code' => $token->code,
			'expire' => $token->created_at + $this->module->apiWithin,
		];
	}

	/**
	 * @inheritdoc
	 */
	public static function findIdentityByAccessToken($token, $type = null) {
		$query = self::find();
		$query->joinWith('tokens');
		$query->andWhere(UserToken::tableName().'.code = :code AND '.UserToken::tableName().'.type = :type', [
			':code' => $token,
			':type' => UserToken::TYPE_API,
		]);
		$user = $query->one();
		if ($user) {
		    $user->touch('lastvisit_at');
            return new static($user);
        }
		return null;
	}

	/**
	 * Finds a user by the given condition.
	 *
	 * @param mixed $condition Condition to be used on search.
	 *
	 * @return UserQuery|object|ActiveQuery
     */
	public static function findUser($condition) {
		return self::find()->andWhere($condition);
	}

	/**
	 * Finds a user by the given username or email.
	 *
	 * @param string $usernameOrEmail Username or email to be used on search.
	 *
	 * @return User|array|ActiveRecord|null
	 * @throws InvalidConfigException
	 */
	public static function findUserByUsernameOrEmail($usernameOrEmail) {
		if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL))
			return self::findUserByEmail($usernameOrEmail);
		return self::findUserByUsername($usernameOrEmail);
	}

	/**
	 * Finds a user by the given email.
	 *
	 * @param string $email Email to be used on search.
	 *
	 * @return array|ActiveRecord|null
     */
	public static function findUserByEmail($email) {
		return self::findUser(['email' => $email])->one();
	}

	/**
	 * Finds a user by the given username.
	 *
	 * @param string $username Username to be used on search.
	 *
	 * @return array|ActiveRecord|null
     */
	public static function findUserByUsername($username) {
		return self::findUser(['username' => $username])->one();
	}
	
	/**
	 * @return array|null
	 */
	public function getMediaImage() {
		$image = $this->avatar;
		if ($image) {
			$imageInfo = $image->getImageInfo(true);
			return [
				'path' => $imageInfo['http'],
				'file' => $imageInfo['file'],
			];
		}
		return null;
	}

    /**
     * Get user is online
     *
     * @return bool
     */
	public function getIsOnline() {
	    return $this->lastvisit_at + 300 > time();
    }

    public function getRights() {
	    $tmp = [];

	    $roles = array_keys(array_change_key_case(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id), CASE_LOWER));

        $permissions = [];
        $permissionsKeys = array_keys(array_change_key_case(Yii::$app->authManager->getPermissionsByUser(Yii::$app->user->id)));
        if ($permissionsKeys) {
            foreach ($permissionsKeys as $key) {
                if (strpos($key, 'client.') === false)
                    $permissions[] = $key;
            }
        }

	    return [
	        'roles' => $roles,
            'permissions' => $permissions,
        ];
    }
}