<?php
namespace common\modules\comments\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use yii\helpers\Url;

use common\modules\base\behaviors\PurifyBehavior;
use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemStat;

use common\modules\comments\models\query\CommentQuery;

use common\modules\company\models\Company;

use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\content\models\Content;
use common\modules\content\models\ContentStat;

use common\modules\favorite\models\Favorite;

use common\modules\media\helpers\enum\Mode;

use common\modules\tag\models\query\TagQuery;
use common\modules\tag\models\Tag;
use common\modules\tag\models\TagModule;

use common\modules\telegram\models\TelegramChat;

use common\modules\user\models\User;

use common\modules\vote\behaviors\VoteBehavior;

/**
 * Class Comment
 *
 * @property integer $id
 * @property integer $module_type
 * @property string $entity
 * @property integer $entity_id
 * @property integer $parent_id
 * @property integer $company_id
 * @property string $content
 * @property string $related_to
 * @property integer $level
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Comment $parent
 * @property Comment $child
 * @property TagModule[] $tagModule
 * @property Tag[] $tags
 * @property CommentOwner $owner
 * @property User $author
 * @property Company $company
 *
 */
class Comment extends ActiveRecord
{
	/**
	 * @var null|array|ActiveRecord[] Comment parent
	 */
	protected $_parent;
	
    /**
     * @var null|array|ActiveRecord[] Comment children
     */
    protected $_children;

    /**
     * @var integer
     */
    public $rating;

    /**
     * @var array
     */
    public $tags_ids;

    /**
     * @var array
     */
    public $_tags_ids;

    /**
     * @var array
     */
    public $_tags_ids_old;

    /**
     * Declares the name of the database table associated with this AR class.
     * @return string the table name
     */
    public static function tableName() {
        return '{{%comment}}';
    }

    /**
     * Returns the validation rules for attributes.
     * @return array validation rules
     */
    public function rules() {
        return [
			[['module_type', 'entity', 'entity_id', 'content'], 'required'],
            [['content', 'entity', 'related_to'], 'string'],
            [['parent_id'], 'validateParentID'],
            [['module_type', 'entity_id', 'parent_id', 'company_id', 'level', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['module_id', 'tags_ids'], 'safe'],
            [['level'], 'default', 'value' => 1],
            [['rating'], 'integer', 'min' => 1, 'max' => 5],
        ];
    }

    /**
     * Validate parentId attribute
     * @param $attribute
     */
    public function validateParentID($attribute) {
        if ($this->{$attribute}) {
            $comment = self::find()->where([
				'id' => $this->{$attribute},
				'entity' => $this->entity,
				'entity_id' => $this->entity_id
			])->exists();
            if ($comment === false) {
                $this->addError('content', Yii::t('comments', 'Oops, something went wrong. Please try again later.'));
            }
        }
    }

    /**
     * @param $attribute
     */
    public function validationSpam($insert = false, $andWhere = []) {
        if (!$insert || $this->parent_id) {
            return false;
        }

        $query = self::find()->where([
            'AND',
            [
                'module_type' => ModuleType::CATALOG_ITEM,
                'entity_id' => $this->entity_id,
                'created_by' => Yii::$app->user->id,
            ],
            $andWhere,
            'created_at + :time > UNIX_TIMESTAMP()',
            '(parent_id = 0 OR parent_id IS NULL)'
        ], [
            ':time' => 60 * 60 * 24
        ]);
        if ($query->exists()) {
            return true;
        }
        return false;
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     * @return array
     */
    public function behaviors() {
        return ArrayHelper::merge(parent::behaviors(), [
            'purify' => [
                'class' => PurifyBehavior::class,
                'attributes' => ['content']
            ],
	        [
		        'class' => VoteBehavior::class,
	        ],
        ]);
    }

    /**
     * Returns the attribute labels.
     * @return array attribute labels (name => label)
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('comments', 'field_id'),
			'module_type' => Yii::t('comments', 'field_module_type'),
			'company_id' => Yii::t('comments', 'field_company_id'),
            'content' => Yii::t('comments', 'field_content'),
            'entity' => Yii::t('comments', 'field_entity'),
			'level' => Yii::t('comments', 'field_level'),
            'status' => Yii::t('comments', 'field_status'),
			'related_to' => Yii::t('comments', 'field_related_to'),
			'created_by' => Yii::t('comments', 'field_created_by'),
			'updated_by' => Yii::t('comments', 'field_updated_by'),
			'created_at' => Yii::t('comments', 'field_created_at'),
			'updated_at' => Yii::t('comments', 'field_updated_at'),
        ];
    }

    /**
     * @inheritdoc
     * @return CommentQuery
     */
    public static function find() {
        return new CommentQuery(get_called_class());
    }

    /**
     * Author relation
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor() {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany() {
		return $this->hasOne(Company::class, ['id' => 'company_id'])->where([])->votes();
	}

    /**
     * @return ActiveQuery
     */
    public function getTagModule(): ActiveQuery
    {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->andOnCondition([
            TagModule::tableName().'.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
    }

    /**
	 * @return CommentOwner
	 */
	public function getOwner() {
		return new CommentOwner($this);
	}
	
	/**
	 * @return bool
	 */
	public function getIsCompany() {
		return ($this->company_id) ? true : false;
	}

    /**
     * Get comments tree.
     *
     * @param $entity string model class id
     * @param $entity_id integer model id
     * @param null $maxLevel
	 *
     * @return array|\yii\db\ActiveRecord[] Comments tree
     */
    public static function getTree($entity, $entity_id, $maxLevel = null) {
        $query = self::find()->joinWith([
        	'author' => function($query) {
        		$query->joinWith(['profile', 'mediaAvatar']);
			},
		])->where([
            self::tableName().'.entity_id' => $entity_id,
            self::tableName().'.entity' => $entity,
        ])->andWhere(['!=', self::tableName().'.status', Status::TEMP]);
	
	    foreach ([\common\modules\vote\models\Vote::COMMENT_VOTE] as $entity) {
		    $query->withVoteAggregate($entity);
		    $query->withUserVote($entity);
	    }

        if ($maxLevel > 0) {
            $query->andWhere(['<=', self::tableName().'.level', $maxLevel]);
        }

        $models = $query->orderBy([
			self::tableName().'.parent_id' => SORT_ASC,
			self::tableName().'.created_at' => SORT_ASC
		])->all();
        if (!empty($models)) {
            $models = self::buildTree($models);
        }

        return $models;
    }

    /**
     * Build comments tree.
     *
     * @param array $data Records array
     * @param int $rootID parentId Root ID
	 * @param object|null $parent parent node
	 *
     * @return array|ActiveRecord[] Comments tree
     */
    protected static function buildTree(&$data, $rootID = 0, $parent = null) {
        $tree = [];
        foreach ($data as $id => $node) {
        	if ($parent)
        		$node->parent = $parent;
            if ($node->parent_id == $rootID) {
                unset($data[$id]);
                $node->children = self::buildTree($data, $node->id, $node);
                $tree[] = $node;
            }
        }
        return $tree;
    }

    /**
     * Delete comment.
     *
     * @return boolean Whether comment was deleted or not
     */
    public function deleteComment() {
        $this->status = Status::DELETED;

        return $this->save(false, ['status', 'updated_by', 'updated_at']);
    }
	
	/**
	 * $_parent getter.
	 *
	 * @return null|array|ActiveRecord[] Comment parent
	 */
	public function getParent() {
		return $this->_parent;
	}
	
	/**
	 * $_parent setter.
	 *
	 * @param array|ActiveRecord[] $value Comment parent
	 */
	public function setParent($value) {
		$this->_parent = $value;
	}

    /**
     * $_children getter.
     *
     * @return null|array|ActiveRecord[] Comment children
     */
    public function getChildren() {
        return $this->_children;
    }

    /**
     * $_children setter.
     *
     * @param array|ActiveRecord[] $value Comment children
     */
    public function setChildren($value) {
        $this->_children = $value;
    }

    /**
     * Check if comment has children comment
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->_children) ? true : false;
    }

    /**
     * @return mixed|null
     */
    public function getModule() {
        switch ($this->module_type) {
            case ModuleType::CONTENT:
                return Content::find()->where('id = :id', [
                    ':id' => $this->getModule_id(),
                ])->one();
                break;
            case ModuleType::CATALOG_ITEM:
                return CatalogItem::findById($this->getModule_id());
                break;
        }
        return null;
    }

    /**
     * @return int
     */
    public function getModule_id() {
        return $this->entity_id;
    }

    /**
     * @param int $val
     */
    public function setModule_id($val) {
        $this->entity_id = $val;
    }

    /**
     * @return boolean Whether comment is active or not
     */
    public function getIsActive() {
        return $this->status === Status::ENABLED;
    }

    /**
     * @return boolean Whether comment is deleted or not
     */
    public function getIsDeleted() {
        return $this->status === Status::DELETED;
    }

    /**
     * Get comment posted date as relative time
     * @return string
     */
    public function getPostedDate() {
        return Yii::$app->formatter->asRelativeTime($this->created_at);
    }
	
	/**
	 * Get comment updated date as relative time
	 * @return string
	 */
	public function getUpdatedDate() {
		return Yii::$app->formatter->asRelativeTime($this->updated_at);
	}

    /**
     * Get author name
     * @return mixed
     */
    public function getAuthorName() {
        return $this->author->getAuthorName();
    }

	/**
	 * Get avatar user
	 * @param array $imgOptions
	 * @return string
	 */
	public function getAvatar($imgOptions = [], $width = 50, $height = 50) {
		return ($this->getIsCompany()) ? $this->company->logo->getImageSrc($width, $height, Mode::CROP_CENTER) : $this->author->avatar->getImageSrc($width, $height, Mode::CROP_CENTER);
	}

    /**
     * Get comment content
     * @param string $text
	 * 
     * @return string
     */
    public function getContent($text = null) {
		if (is_null($text))
			$text = Yii::t('comments', 'message_comment_was_deleted');
        return $this->isDeleted ? $text : $this->content;
    }
	
	/**
	 * Check is own comment
	 * @return bool
	 */
    public function getIsOwn() {
    	return $this->created_by == Yii::$app->user->id;
	}

    /**
     * This function used for filter in gridView, for attribute `created_by`.
     * @return array
     */
    public static function getListAuthorsNames() {
        return ArrayHelper::map(self::find()->joinWith('author')->all(), 'created_by', 'author.profile.fullname');
    }

    /**
     * @return string|string[]|null
     */
    public function getContent_with_links() {
        return $this->content;
        $url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
        return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $this->content);
    }

    /**
     * @return array
     */
    public function getTags_ids(): array
    {
        if (is_null($this->_tags_ids)) {
            $this->_tags_ids = [];
            $tags = $this->tags;
            if ($tags) {
                foreach ($tags as $item)
                    $this->_tags_ids[] = $item->id;
            }
        }
        return $this->_tags_ids;
    }

    /**
     * @return bool
     */
    public function beforeValidate() {
        if ($this->parent_id === 0) {
            $this->parent_id = null;
        }
        if (!$this->entity && $this->module_type && $module = $this->getModule()) {
            $this->entity = hash('crc32', get_class($module));
        }
        return parent::beforeValidate();
    }

    /**
	 * This method is called at the beginning of inserting or updating a record.
	 * @param bool $insert
	 * @return bool
	 */
	public function beforeSave($insert) {
		if (parent::beforeSave($insert)) {
            if ($this->validationSpam($insert, [
                'status' => Status::ENABLED,
            ])) {
                $this->addError('id', Yii::t('comments', 'message_spam'));
                return false;
            }

			if ($this->parent_id > 0) {
				$parentNodeLevel = (int)self::find()->select('level')->where(['id' => $this->parent_id])->scalar();
				$this->level = $parentNodeLevel + 1;
			}
			return true;
		}

		return false;
	}

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);

        if (in_array($this->module_type, [
            ModuleType::CATALOG_ITEM,
        ])) {
            $model = CatalogItem::find()->where(['id' => $this->entity_id])->one();
            if ($model) {
                CatalogItemStat::updateLinks($model);
            }
        }
		
		if (in_array($this->module_type, [
		    ModuleType::CONTENT,
			ModuleType::CONTENT_ARTICLE,
			ModuleType::CONTENT_NEWS,
			ModuleType::CONTENT_BLOG,
			ModuleType::CONTENT_PROJECT,
			ModuleType::CONTENT_PLUGIN,
            ModuleType::CONTENT_QUESTION,
		])) {
			$model = Content::find()->where(['id' => $this->entity_id])->one();
			if ($model) {
				ContentStat::updateLinks($model);
			}
		}

		if ($this->status == Status::ENABLED) {
			if (in_array($this->module_type, [
			    ModuleType::CONTENT,
				ModuleType::CONTENT_ARTICLE,
				ModuleType::CONTENT_NEWS,
				ModuleType::CONTENT_BLOG,
				ModuleType::CONTENT_PROJECT,
				ModuleType::CONTENT_PLUGIN,
				ModuleType::CONTENT_QUESTION,
			])) {
				$model = Content::find()->where(['id' => $this->entity_id])->one();
				if ($model) {
					$typeName = str_replace('type_', '', ContentType::getItem($model->type));
					$user = Yii::$app->user->identity;
					$messageCategory = $this->getIsCompany() ? 'notification-company' : 'notification';
					
					$url = Url::to(['/'.$model->getUriModuleName().'/view', 'id' => $model->id], true);
					if ($this->module_type == ModuleType::CONTENT_QUESTION && $model->getIsCompany()) {
						$url = Url::to(['/companies/question/view', 'company_id' => $model->company_id, 'id' => $model->id], true);
					}
                    if ($model->type == ContentType::VIDEO) {
                        $url = 'https://v2.sprut.ai/video/'.$model->seo->slugify;
                    }
					
					if ($this->level == 1) {
						if ($model->author_id != Yii::$app->user->id) {
							$subject = Yii::t($messageCategory, $typeName.'_comment_new_subject');
							$message = Yii::t($messageCategory, $typeName.'_comment_new', [
								'user' => $this->owner->title,
								'url' => $url,
								'title' => $model->title,
								'comment' => strip_tags($this->content),
							]);
							
							Yii::$app->notification->queue([$model->author->id], $subject, $message, 'comment');
						}
					}
					else {
						$parent = self::findBy($this->parent_id);
						if ($parent && $parent->created_by !== $user->id) {
							$subject = Yii::t($messageCategory, $typeName.'_comment_reply_subject');
							$message = Yii::t($messageCategory, $typeName.'_comment_reply', [
								'user' => $this->owner->title,
								'url' => $url,
								'title' => $model->title,
								'comment' => strip_tags($this->content),
							]);
							
							Yii::$app->notification->queue([$parent->created_by], $subject, $message, 'comment');
						}
					}
				}
			}

			if ($insert && $this->module_type == ModuleType::CATALOG_ITEM) {


                /** @var CatalogItem $catalogItem */
                $catalogItem = CatalogItem::find()->where(['id' => $this->entity_id])->one();
                if ($catalogItem) {
                    $validationSpam = $this->validationSpam(true, ['<>', 'id', $this->id]);
                    if (!$this->parent_id && !$validationSpam) {
                        $chatIds = ArrayHelper::merge([-1001082506583], TelegramChat::getIdentifiersContent($catalogItem->tags_ids));
                        $chatIds = array_unique($chatIds);
                        $chatIds = array_filter($chatIds, function ($id) {
                            return $id != -1001437904573;
                        });

                        $tag = null;
                        $chat = null;
                        if (is_array($this->getTags_ids())) {
                            $tag = Tag::find()->where(['in', 'id', $this->getTags_ids()])->one();
                        }
                        if ($tag) {
                            $chat = TelegramChat::find()->joinWith(['tags'])
                                ->where([
                                    TelegramChat::tableName().'.status' => Status::ENABLED,
                                ])
                                ->andWhere([
                                    Tag::tableName().'.id' => $tag->id,
                                ])->one();
                        }

                        $platform = '';
                        if ($tag) {
                            $data = ($chat) ? '<a href="tg://resolve?domain='.$chat->username.'">'.$tag->title.'</a>' : $tag->title;
                            $platform = Yii::t('notification', 'catalog_item_review_platform', ['data' => $data]).PHP_EOL.PHP_EOL;
                        }

                        $message = Yii::t('notification', 'catalog_item_review', [
                            'catalog_item_url' => 'https://v2.sprut.ai/catalog/item/'.$catalogItem->seo->slugify,
                            'catalog_item_title' => $catalogItem->title,
                            'vendor_title' => $catalogItem->vendor->title,
                            'rating' => str_repeat('⭐', $this->rating),
                            'user_name' => $this->author->getAuthorName(true),
                            'content' => $this->content,
                            'platform' => $platform,
                        ]);

                        $image = $catalogItem->image->mediaImage->getFilePath(true).$catalogItem->image->mediaImage->getFile();
                        Yii::$app->notification->queueTelegramIds($chatIds, $message, [
                            'image' => $image,
                        ]);

                        if (!Favorite::find()->where([
                            'group_id' => Favorite::GROUP_ID,
                            'module_type' => ModuleType::CATALOG_ITEM,
                            'module_id' => $this->entity_id,
                            'user_id' => $this->created_by,
                        ])->exists()) {
                            $favorite = new Favorite();
                            $favorite->group_id = Favorite::GROUP_ID;
                            $favorite->module_type = ModuleType::CATALOG_ITEM;
                            $favorite->module_id = $this->entity_id;
                            $favorite->user_id = $this->created_by;
                            $favorite->save();
                        }
                    }
                    else if ($this->parent_id && $this->level > 1) {
                        $parent = self::findBy($this->parent_id);
                        if ($parent && $parent->created_by !== $user->id) {
                            $subject = Yii::t('notification', 'catalog_item_'.($this->level > 2 ? 'comment' : 'review').'_reply_subject');
                            $message = Yii::t('notification', 'catalog_item_'.($this->level > 2 ? 'comment' : 'review').'_reply', [
                                'user' => $this->author->getAuthorName(true),
                                'url' => 'https://v2.sprut.ai/catalog/item/'.$catalogItem->seo->slugify,
                                'title' => $model->title,
                                'comment' => strip_tags($this->content),
                            ]);

                            Yii::$app->notification->queue([$parent->created_by], $subject, $message, 'comment');
                        }
                    }
                }
            }
		}
	}
}
