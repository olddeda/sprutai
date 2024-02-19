<?php
namespace common\modules\content\models;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use yii\db\Exception;

use common\modules\base\components\ActiveRecord;
use common\modules\base\behaviors\ArrayFieldBehavior;

use common\modules\rbac\helpers\enum\Role;

use common\modules\user\models\User;

use common\modules\content\models\query\ContentHistoryQuery;

/**
 * This is the model class for table "{{%content_history}}".
 *
 * @property int $id
 * @property int $content_id
 * @property int $user_id
 * @property array $json
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * Defined relations:
 * @property Content $content
 * @property User $user
 */
class ContentHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_history}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ArrayFieldBehavior::class,
                'attribute' => 'json',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content_id', 'json', 'status'], 'required'],
            [['content_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['json'], 'safe'],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::class, 'targetAttribute' => ['content_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            ['content_id', 'validateContent'],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateContent($attribute) {
        $query = Content::find()->where('id = :id', [
            ':id' => $this->$attribute
        ]);
        if (!Yii::$app->user->hasRole([Role::SUPERADMIN, Role::ADMIN, Role::EDITOR])) {
            $query->andWhere(['author_id' => Yii::$app->user->id]);
        }
        if (!$query->exists()) {
            $this->addError($attribute, Yii::t('content', 'error_not_exists'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('content-history', 'field_id'),
            'content_id' => Yii::t('content-history', 'field_content_id'),
            'user_id' => Yii::t('content-history', 'field_user_id'),
            'json' => Yii::t('content-history', 'field_json'),
            'status' => Yii::t('content-history', 'field_status'),
            'created_at' => Yii::t('content-history', 'field_created_at'),
            'updated_at' => Yii::t('content-history', 'field_updated_at'),
        ];
    }
	
	/**
	 * @return ActiveQuery
	 */
	public function getContent() {
		return $this->hasOne(Content::class, ['id' => 'content_id']);
	}

    /**
     * @return ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ContentHistoryQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentHistoryQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate() {
        $this->user_id = Yii::$app->user->id;

        return parent::beforeValidate();
    }
}
