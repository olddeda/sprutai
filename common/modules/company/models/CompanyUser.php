<?php
namespace common\modules\company\models;

use Yii;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;

use common\modules\rbac\components\DbManager;

use common\modules\user\models\User;

use common\modules\company\models\query\CompanyUserQuery;

/**
 * This is the model class for table "{{%company_user}}".
 *
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property \common\modules\company\models\Company $company
 * @property \common\modules\user\models\User $user
 */
class CompanyUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%company_user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['company_id', 'user_id', 'status'], 'required'],
            [['company_id', 'user_id', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['user_id', 'unique', 'targetAttribute' => ['company_id', 'user_id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('company-user', 'field_id'),
            'company_id' => Yii::t('company-user', 'field_company_id'),
            'user_id' => Yii::t('company-user', 'field_user_id'),
			'status' => Yii::t('company-user', 'field_status'),
			'created_by' => Yii::t('company-user', 'field_created_by'),
			'updated_by' => Yii::t('company-user', 'field_updated_by'),
			'created_at' => Yii::t('company-user', 'field_created_at'),
			'updated_at' => Yii::t('company-user', 'field_updated_at'),
			'user_fio' => Yii::t('company-user', 'field_user_fio'),
			'user_email' => Yii::t('company-user', 'field_user_email'),
			'user_phone' => Yii::t('company-user', 'field_user_phone'),
			'user_telegram' => Yii::t('company-user', 'field_user_telegram'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\company\models\query\CompanyUserQuery the active query used by this AR class.
     */
    public static function find() {
        return new CompanyUserQuery(get_called_class());
    }
	
	/**
	 * {@inheritdoc}
	 */
	public function delete($useStatus = true) {
		parent::delete($useStatus);
		
		$auth = Yii::$app->authManager;
		$task = $auth->getItem('Client.Company.Company');
		if ($auth->getAssignment($task->name, $this->user_id)) {
			$auth->revoke($task, $this->user_id);
			$auth->deleteAllCache();
		}
	}
	
	/**
	 * {@inheritdoc}
	 */
    public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		if ($insert) {
			
			$auth = Yii::$app->authManager;
			$task = $auth->getItem('Client.Company.Company');
			if (!$auth->getAssignment($task->name, $this->user_id)) {
				$auth->assign($task, $this->user_id);
				$auth->deleteAllCache();
			}
		}
	}
}
