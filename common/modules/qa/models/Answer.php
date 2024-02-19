<?php
namespace common\modules\qa\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\helpers\HtmlPurifier;
use yii\helpers\Markdown;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;

use common\modules\qa\Module;
use common\modules\qa\models\interfaces\AnswerInterface;

/**
 * Answer Model
 * @package common\modules\qa\models
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $question_id
 * @property string $content
 * @property integer $votes
 * @property integer $status
 * @property integer $is_correct
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Question $question
 * @property User $user
 */
class Answer extends ActiveRecord implements AnswerInterface
{
    /**
     * Markdown processed content
     * @var string
     */
    public $body;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%qa_answer}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'body'
                ],
                'value' => function ($event) {
                    return HtmlPurifier::process(Markdown::process($event->sender->content, 'gfm-comment'));
                }
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['question_id'], 'exist', 'targetClass' => Question::className(), 'targetAttribute' => 'id']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model', 'ID'),
            'content' => Module::t('model', 'Content'),
            'status' => Module::t('model', 'Status'),
        ];
    }

    /**
     * @return Question
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * User Relation
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    /**
     * @param int $questionID
     * @return int
     */
    public static function removeRelation($questionID)
    {
        return self::deleteAll(
            'question_id=:question_id',
            [
                ':question_id' => $questionID,
            ]
        );
    }

    /**
     * Apply possible answers order to query
     * @param ActiveQuery $query
     * @param $order
     * @return string
     */
    public static function applyOrder(ActiveQuery $query, $order)
    {
        switch ($order) {
            case 'oldest':
                $query->orderBy('created_at DESC');
                break;

            case 'active':
                $query->orderBy('created_at ASC');
                break;

            case 'votes':
            default:
                $query->orderBy('votes DESC');
                break;
        }

        return $order;
    }

    /**
     * Check if current user can edit this model
     * @return bool
     */
    public function isAuthor()
    {
        return $this->user_id == Yii::$app->user->id;
    }

    /**
     * Check if this answer is correct
     * @return bool
     */
    public function isCorrect()
    {
        return $this->is_correct;
    }

    /**
     * Toggles correct or not
     * @return bool
     */
    public function toggleCorrect()
    {
        $this->is_correct = ! $this->isCorrect();

        return $this->save();
    }

    /**
     * Formatted date
     * @return string
     */
    public function getUpdated()
    {
        return Module::getInstance()->getDate($this, 'updated_at');
    }

    /**
     * Formatted date
     * @return string
     */
    public function getCreated()
    {
        return Module::getInstance()->getDate($this, 'created_at');
    }

    /**
     * Formatted user
     * @return int
     */
    public function getUserName()
    {
        return $this->user ? Module::getInstance()->getUserName($this->user, 'id') : $this->user_id;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            Question::incrementAnswers($this->question_id);
        }
    }

    /**
     * This is invoked after the record is deleted.
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Question::decrementAnswers($this->question_id);
        Vote::removeRelation($this);
    }
}
