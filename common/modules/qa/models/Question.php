<?php
namespace common\modules\qa\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQueryInterface;
use yii\helpers\HtmlPurifier;
use yii\helpers\Inflector;
use yii\helpers\Markdown;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;

use common\modules\qa\Module;
use common\modules\qa\models\interfaces\QuestionInterface;
use common\modules\qa\models\query\QuestionQuery;

/**
 * Question Model
 * @package common\modules\qa\models
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $alias
 * @property string $content
 * @property string $tags
 * @property integer $answers
 * @property integer $views
 * @property integer $votes
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class Question extends ActiveRecord implements QuestionInterface
{
    /**
     * Old tags populated after find record
     * @var string
     */
    protected $_oldTags = '';

    /**
     * Markdown processed content
     * @var string
     */
    public $body;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%qa_question}}';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'alias'
                ],
                'value' => function ($event) {
                    return Inflector::slug($event->sender->title);
                }
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'body'
                ],
                'value' => function ($event) {
                    return HtmlPurifier::process(Markdown::process($event->sender->content, 'gfm'));
                }
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'tags'
                ],
                'value' => function ($event) {
                    return $event->sender->tags ? strip_tags($event->sender->tags) : $event->sender->tags;
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
            [['title', 'content', 'tags'], 'required'],
            [['tags'], 'normalizeTags']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('model', 'ID'),
            'title' => Module::t('model', 'Title'),
            'alias' => Module::t('model', 'Alias'),
            'content' => Module::t('model', 'Content'),
            'tags' => Module::t('model', 'Tags'),
            'status' => Module::t('model', 'Status'),
        ];
    }

    /**
     * @return QuestionQuery
     */
    public static function find()
    {
        return new QuestionQuery(get_called_class());
    }

    /**
     * Answer Relation
     * @return ActiveQueryInterface
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::class, ['question_id' => 'id']);
    }

    /**
     * User Relation
     * @return ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

    /**
     * Favorite Relation
     * @return ActiveQueryInterface
     */
    public function getFavorite()
    {
        return $this->hasOne(Favorite::class, ['question_id' => 'id']);
    }

    /**
     * Favorite Relation
     * @return ActiveQueryInterface
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorite::class, ['question_id' => 'id']);
    }

    /**
     * @return int|string
     */
    public function getFavoriteCount()
    {
        return $this->hasMany(Favorite::class, ['question_id' => 'id'])->count();
    }

    /**
     * @param $id
     */
    public static function incrementAnswers($id)
    {
        self::updateAllCounters(['answers' => 1], ['id' => $id]);
    }

    /**
     * @param $id
     */
    public static function decrementAnswers($id)
    {
        self::updateAllCounters(['answers' => -1], ['id' => $id]);
    }

    /**
     * Normalizes the user-entered tags.
     */
    public function normalizeTags($attribute, $params)
    {
        $this->tags = Tag::array2String(array_unique(Tag::string2Array($this->tags)));
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
     * @param bool $user
     * @return bool
     */
    public function isFavorite($user = false)
    {
        $user = ($user) ? $user : Yii::$app->user;

        return Favorite::find()->where(['user_id' => $user->id, 'question_id' => $this->id])->exists();
    }

    /**
     * @param $data
     * @return bool
     */
    public function haveDraft($data)
    {
        return isset($data['draft']);
    }

    /**
     * @return bool
     */
    public function isDraft()
    {
        return $this->status == QuestionInterface::STATUS_DRAFT;
    }

    /**
     * Check if is given user unique
     * @return bool
     */
    public function isUserUnique()
    {
        return $this->user_id !== Yii::$app->user->id;
    }

    /**
     * @return bool
     */
    public function toggleFavorite()
    {
        if ($this->isFavorite()) {
            return Favorite::remove($this->id);
        } else {
            return Favorite::add($this->id);
        }
    }

    /**
     * @return array a list of links that point to the post list filtered by every tag of this post
     */
    public function getTagsList()
    {
        return Tag::string2Array($this->tags);
    }

    /**
     * @return string
     */
    public function getUpdated()
    {
        return Module::getInstance()->getDate($this, 'updated_at');
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return Module::getInstance()->getDate($this, 'created_at');
    }

    /**
     * @return int|string
     */
    public function getUserName()
    {
        return $this->user ? Module::getInstance()->getUserName($this->user, 'id') : $this->user_id;
    }

    /**
     *
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->_oldTags = $this->tags;
    }

    /**
     * @param $insert
     * @param $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        Tag::updateFrequency($this->_oldTags, $this->tags);
    }

    /**
     * This is invoked after the record is deleted.
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Tag::updateFrequency($this->tags, '');
        Vote::removeRelation($this);
        Answer::removeRelation($this->id);
        Favorite::removeRelation($this->id);
    }
}
