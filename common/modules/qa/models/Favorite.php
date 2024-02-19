<?php
namespace common\modules\qa\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;


use common\modules\base\components\ActiveRecord;

use common\modules\qa\Module;

/**
 * This is the model class for table "qa_favorite".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $question_id
 * @property string $created_at
 * @property string $created_ip
 *
 * @property Question $question
 */
class Favorite extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%qa_favorite}}';
    }

    /**
     * @return array[]
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
                ],
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_ip'
                ],
                'value' => function ($event) {
                    $ip = ip2long(Yii::$app->request->getUserIP());

                    if ($ip > 0x7FFFFFFF) {
                        $ip -= 0x100000000;
                    }

                    return $ip;
                }
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'created_ip'
                ],
                'value' => function ($event) {
                    return long2ip($event->sender->created_ip);
                }
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['question_id'], 'required'],
            [['user_id', 'question_id', 'created_at', 'created_ip'], 'integer']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model', 'ID'),
            'user_id' => Module::t('model', 'User ID'),
            'question_id' => Module::t('model', 'Question ID'),
            'created_at' => Module::t('model', 'Created At'),
            'created_ip' => Module::t('model', 'Created Ip'),
        ];
    }

    /**
     * Question Relation
     * @return \yii\db\ActiveQueryInterface
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * @param $id
     * @return bool
     */
    public static function add($id)
    {
        $favorite = new self();
        $favorite->attributes = ['question_id' => $id];

        if ($favorite->save()) {
            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception|\Throwable
     */
    public static function remove($id)
    {
        /** @var \yii\db\ActiveQuery $query */
        $query = self::find()->where(['question_id' => $id, 'user_id' => Yii::$app->user->id]);

        if ($query->exists() && $query->one()->delete()) {
            return true;
        }

        return false;
    }

    /**
     * @param int $question_id
     * @return int
     */
    public static function removeRelation(int $question_id)
    {
        return self::deleteAll(
            'question_id=:question_id',
            [
                ':question_id' => $question_id,
            ]
        );
    }
}
