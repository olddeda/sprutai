<?php
namespace common\modules\user\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\query\UserSubscribeQuery;

/**
 * This is the model class for table "{{%user_subscribe}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $flag_system
 * @property int $flag_author
 * @property int $flag_article
 * @property int $flag_news
 * @property int $flag_project
 * @property int $flag_blog
 * @property int $flag_plugin
 * @property int $flag_item
 * @property int $flag_comment
 * @property int $flag_vote
 * @property int $flag_qa
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property \common\modules\user\models\User $user
 */
class UserSubscribe extends ActiveRecord
{
	/**
	 * @var \common\modules\user\Module
	 */
	protected $module;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		$this->module = Yii::$app->getModule('user');
		parent::init();
	}
	
	/**
	 * @inheritdoc
	 */
	public static function getDb() {
		return Yii::$app->get('db');
	}
	
	/**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%user_subscribe}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id'], 'required'],
            [['user_id', 'flag_system', 'flag_author', 'flag_article', 'flag_news',  'flag_project',  'flag_blog',  'flag_plugin', 'flag_item', 'flag_comment', 'flag_vote', 'flag_qa', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'user_id' => Yii::t('user-subscribe', 'field_user_id'),
	        'flag_system' => Yii::t('user-subscribe', 'field_flag_system'),
			'flag_author' => Yii::t('user-subscribe', 'field_flag_author'),
            'flag_article' => Yii::t('user-subscribe', 'field_flag_article'),
			'flag_news' => Yii::t('user-subscribe', 'field_flag_news'),
			'flag_project' => Yii::t('user-subscribe', 'field_flag_project'),
			'flag_blog' => Yii::t('user-subscribe', 'field_flag_blog'),
			'flag_plugin' => Yii::t('user-subscribe', 'field_flag_plugin'),
            'flag_item' => Yii::t('user-subscribe', 'field_flag_item'),
            'flag_comment' => Yii::t('user-subscribe', 'field_flag_comment'),
			'flag_vote' => Yii::t('user-subscribe', 'field_flag_vote'),
            'flag_qa' => Yii::t('user-subscribe', 'field_flag_qa'),
            'created_at' => Yii::t('user-subscribe', 'field_created_at'),
            'updated_at' => Yii::t('user-subscribe', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\user\models\query\UserSubscribeQuery the active query used by this AR class.
     */
    public static function find() {
        return new UserSubscribeQuery(get_called_class());
    }
	
	/**
	 * @return \yii\db\ActiveQueryInterface
	 */
	public function getUser() {
		return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
	}
}
