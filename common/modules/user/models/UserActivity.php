<?php
namespace common\modules\user\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\query\UserQuery;
use common\modules\user\models\query\UserActivityQuery;

use common\modules\user\helpers\enum\ActivityType;

/**
 * Class UserLog
 *
 * @package app\models
 *
 * @property integer $id
 * @property integer $type
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $parent_module_type
 * @property integer $parent_module_id
 * @property integer $user_id
 * @property integer $from_user_id
 * @property integer $date_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property User $user
 * @property User $userFrom
 */
class UserActivity extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_activity}}';
    }

	/**
	 * @inheritdoc
	 *
	 * @return array
	 */
	public function rules() {
		return [
		    [['type', 'module_type', 'module_id', 'user_id', 'from_user_id', 'date_at'], 'required'],
			[['id', 'type', 'module_type', 'module_id', 'parent_module_type', 'parent_module_id', 'user_id', 'from_user_id', 'date_at', 'created_at', 'updated_at'], 'integer'],
		];
	}

	/**
	 * @return UserActivityQuery
	 */
	public static function find() {
		return new UserActivityQuery(get_called_class());
	}

    /**
     * @return UserQuery
     */
    public function getUser() {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @return UserQuery
     */
    public function getUserFrom() {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'from_user_id']);
    }

    /**
     * @return string
     */
    public function getTypeName() {
        return str_replace('activity_type_', '', ActivityType::getItem($this->type));
    }
}