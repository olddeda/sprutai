<?php
namespace common\modules\vote\models;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\catalog\models\CatalogItem;
use common\modules\comments\models\Comment;
use common\modules\content\helpers\enum\Type;
use common\modules\content\models\Article;
use common\modules\content\models\Content;
use common\modules\content\models\ContentAuthorStat;
use common\modules\user\models\User;
use common\modules\vote\Module;
use common\modules\vote\traits\ModuleTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * This is the model class for table "vote".
 *
 * @package common\modules\vote\models
 * @property integer $id
 * @property integer $module_type
 * @property integer $entity
 * @property integer $entity_id
 * @property integer $user_id
 * @property string $user_ip
 * @property integer $value
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property \common\modules\vote\models\VoteAggregate $aggregate
 */
class Vote extends ActiveRecord
{
	use ModuleTrait;
	
	const CONTENT_VOTE			= 'contentVote';
	const CONTENT_FAVORITE		= 'contentFavorite';
	const USER_FAVORITE			= 'userFavorite';
	const COMPANY_VOTE			= 'companyVote';
	const COMPANY_FAVORITE		= 'companyFavorite';
	
	const TAG_FAVORITE			= 'tagFavorite';

	const COMMENT_VOTE			= 'commentVote';
    const COMMENT_RATING		= 'commentRating';
	
	const CONTEST_VOTE			= 'contestVote';

	const CATALOG_ITEM_VOTE     = 'catalogItemVote';
	
	// To remove
	const ARTICLE_VOTE			= 'articleVote';
	const ARTICLE_FAVORITE		= 'articleFavorite';
	const NEWS_VOTE				= 'newsVote';
	const NEWS_FAVORITE			= 'newsFavorite';
	const PROJECT_VOTE			= 'projectVote';
	const PROJECT_FAVORITE		= 'projectFavorite';
	const BLOG_VOTE				= 'blogVote';
	const BLOG_FAVORITE			= 'blogFavorite';
	const PLUGIN_VOTE			= 'pluginVote';
	const PLUGIN_FAVORITE		= 'pluginFavorite';
	
    const VOTE_POSITIVE = 1;
    const VOTE_NEGATIVE = 0;

    /**
     * @return string
     */
    public static function tableName() {
        return '{{%vote}}';
    }

    /**
     * @return array
     */
    public function rules() {
        return [
            [['entity', 'entity_id', 'value'], 'required'],
            [['entity', 'entity_id', 'user_id', 'value', 'created_at', 'updated_at'], 'integer'],
            [['user_ip'], 'default', 'value' => function () {
                if (Yii::$app instanceof \yii\web\Application) {
                    return Yii::$app->request->userIP;
                }
                return null;
            }],
            [['user_id'], 'default', 'value' => function () {
                if (isset(Yii::$app->user) && !Yii::$app->user->isGuest) {
                    return Yii::$app->user->id;
                }
                return null;
            }],
            [['entity'], 'validateEntity'],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function validateEntity($attribute, $params) {
        $entities = $this->getModule()->entities;
        $value = $this->getAttribute($attribute);

        $found = false;
        foreach (array_keys($entities) as $entity) {
            if ($this->getModule()->encodeEntity($entity) == $value) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->addError($attribute, 'Wrong entity');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('vote', 'field_id'),
            'entity' => Yii::t('vote', 'field_entity'),
            'entity_id' => Yii::t('vote', 'field_entity_id'),
            'user_id' => Yii::t('vote', 'field_user_id'),
            'user_ip' => Yii::t('vote', 'field_user_ip'),
            'value' => Yii::t('vote', 'field_value'),
            'created_at' => Yii::t('vote', 'field_created_at'),
			'updated_at' => Yii::t('vote', 'field_updated_at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAggregate() {
        return $this->hasOne(VoteAggregate::class, [
            'vote.entity' => 'vote_aggregate.entity',
            'vote.entity_id' => 'vote_aggregate.entity_id'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent() {
        return $this->hasOne(Content::class, [
            'id' => 'entity_id'
        ])->where([]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment() {
        return $this->hasOne(Comment::class, [
            'id' => 'entity_id'
        ])->where([]);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete() {
        static::updateRating($this->attributes['entity'], $this->attributes['entity_id']);
        parent::afterDelete();
    }
    
	/**
	 * @param bool $insert
	 * @param array $changedAttributes
	 *
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\web\NotFoundHttpException
	 */
    public function afterSave($insert, $changedAttributes) {
        static::updateRating($this->attributes['entity'], $this->attributes['entity_id']);
        
        parent::afterSave($insert, $changedAttributes);
	
		$module = $this->getModule();
		
		if ($this->entity == $this->getModule()->encodeEntity(self::USER_FAVORITE) && !Yii::$app->user->isGuest) {
			$user = Yii::$app->user->identity;
			$model = User::findBy($this->entity_id);
			
			ContentAuthorStat::updateLinks($model);
			
			if ($model && $model->id != $user->id) {
				if ($this->value) {
					$subject = Yii::t('notification', 'author_subscribe_subject');
					$message = Yii::t('notification', 'author_subscribe', [
						'title' => $user->getAuthorName(),
						'url' => Url::to(['/user/profile/view', 'id' => $user->id], true),
					]);
					Yii::$app->notification->queue([$model->id], $subject, $message, 'system');
				}
			}
		}
		else if ($this->entity == $this->getModule()->encodeEntity(self::CONTENT_VOTE) && !Yii::$app->user->isGuest) {
        	$user = Yii::$app->user->identity;
			$settings = $module->getSettingsForEntity(self::CONTENT_VOTE);
			
			$targetModel = Yii::createObject($settings['modelName']);
			
			/** @var Content $model */
			$model = $targetModel->find()->where(['id' => $this->entity_id])->one();
        	if ($model && $model->author_id != $user->id) {
        		if ($this->value) {
					$type = $model->getTypeName();
        			$url = $model->getUriRoute('view', true);
                    if ($model->type == Type::VIDEO) {
                        $url = 'https://v2.sprut.ai/video/'.$model->seo->slugify;
                    }
        			
					$subject = Yii::t('notification', $type.'_liked_subject');
					$message = Yii::t('notification', $type.'_liked', [
						'fio' => $user->getAuthorName(),
						'url' => $url,
						'title' => $model->title,
					]);
					Yii::$app->notification->queue([$model->author->id], $subject, $message, 'vote');
				}
			}
		}
	    else if ($this->entity == $this->getModule()->encodeEntity(self::COMMENT_VOTE) && !Yii::$app->user->isGuest) {
		    
		    /** @var \common\modules\comments\models\Comment $comment */
		    $comment = Comment::findBy($this->entity_id);

		    switch ($comment->module_type) {
                case ModuleType::CONTENT:
                    $this->_sendNotificationContent($comment);
                    break;
            }
	    }
    }

    /**
     * @param Comment $comment
     */
    private function _sendNotificationContent($comment) {
        $user = Yii::$app->user->identity;

        /** @var \common\modules\content\models\Article $article */
        $article = Article::findById($comment->entity_id);

        if ($article && $comment && $comment->created_by != $user->id) {
            if ($this->value) {
                $subject = Yii::t('notification', 'article_comment_liked_subject');
                $message = Yii::t('notification', 'article_comment_liked', [
                    'fio' => $user->getAuthorName(),
                    'url' => Url::to(['/article/view', 'id' => $article->id], true),
                    'title' => $article->title,
                ]);
                Yii::$app->notification->queue([$comment->created_by], $subject, $message, 'vote');
            }
        }
    }

    /**
     * @param Comment $comment
     */
    private function _sendNotificationCatalogItem($comment) {
        $user = Yii::$app->user->identity;

        /** @var \common\modules\catalog\models\CatalogItem $model */
        $model = CatalogItem::findById($comment->entity_id);

        if ($model && $comment && $comment->created_by != $user->id) {
            if ($this->value) {
                $subject = Yii::t('notification', 'catalog_item_review_comment_liked_subject');
                $message = Yii::t('notification', 'catalog_item_review_comment_liked', [
                    'fio' => $user->getAuthorName(),
                    'url' => Url::to(['/article/view', 'id' => $article->id], true),
                    'title' => $article->title,
                ]);
                Yii::$app->notification->queue([$comment->created_by], $subject, $message, 'vote');
            }
        }
    }

    /**
     * @param $entity
     * @param $targetId
     */
    public static function updateRating($entity, $entityId) {
        $type = ArrayHelper::getValue(self::getModule()->getEntityForEncoded($entity), 'type', 'vote');

        $rating = 0;
        $positive = 0;
        $negative = 0;

        if ($type == Module::TYPE_RATING) {
            $rating = static::find()->select('value')->where(['entity' => $entity, 'entity_id' => $entityId])->scalar();
        }
        else {
            $positive = static::find()->where(['entity' => $entity, 'entity_id' => $entityId, 'value' => self::VOTE_POSITIVE])->count();
            $negative = static::find()->where(['entity' => $entity, 'entity_id' => $entityId, 'value' => self::VOTE_NEGATIVE])->count();

            if ($positive + $negative !== 0) {
                $rating = (($positive + 1.9208) / ($positive + $negative) - 1.96 * SQRT(($positive * $negative) / ($positive + $negative) + 0.9604) / ($positive + $negative)) / (1 + 3.8416 / ($positive + $negative));
            }
            else {
                $rating = 0;
            }
            $rating = round($rating * 10, 2);
        }
        
        $aggregateModel = VoteAggregate::findOne([
            'entity' => $entity,
            'entity_id' => $entityId,
        ]);
        if ($aggregateModel == null) {
            $aggregateModel = new VoteAggregate();
            $aggregateModel->entity = $entity;
            $aggregateModel->entity_id = $entityId;
        }
        $aggregateModel->positive = $positive;
        $aggregateModel->negative = $negative;
        $aggregateModel->rating = $rating;
        $aggregateModel->save();
    }
}
