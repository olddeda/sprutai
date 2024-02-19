<?php
namespace common\modules\mailing\models;

use Yii;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\user\models\User;

use common\modules\mailing\models\query\MailingUserQuery;
use common\modules\mailing\helpers\enum\Type;

/**
 * This is the model class for table "{{%mailing_user}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $email
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 */
class MailingUser extends ActiveRecord
{
    /**
     * Get module type
     * @return int
     */
    public static function moduleType() {
        return ModuleType::MAILING_USER;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%mailing_user}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'email', 'status'], 'required'],
            [['email'], 'email'],
            [['type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('mailing_user', 'field_id'),
            'type' => Yii::t('mailing_user', 'field_type'),
            'email' => Yii::t('mailing_user', 'field_email'),
            'status' => Yii::t('mailing_user', 'field_status'),
            'created_at' => Yii::t('mailing_user', 'field_created_at'),
            'updated_at' => Yii::t('mailing_user', 'field_updated_at'),
        ];
    }
    
    /**
     * @inheritdoc
     * @return \common\modules\mailing\models\query\MailingUserQuery the active query used by this AR class.
     */
    public static function find() {
        return new MailingUserQuery(get_called_class());
    }
    
    /**
     * Get created user model
     * @return \common\modules\user\models\query\UserQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
    
    /**
     * Get updated user model
     * @return \common\modules\user\models\query\UserQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
