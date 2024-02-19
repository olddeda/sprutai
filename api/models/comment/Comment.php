<?php
namespace api\models\comment;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

use common\modules\base\helpers\enum\Status;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\catalog\models\CatalogItem;
use common\modules\catalog\models\CatalogItemCorrect;

use common\modules\comments\models\Comment as BaseComment;

use common\modules\catalog\helpers\enum\CorrectAction;
use common\modules\catalog\helpers\enum\CorrectType;
use common\modules\tag\models\TagModule;

use common\modules\vote\models\Vote;

use api\models\user\User;
use api\models\company\Company;
use api\models\tag\Tag;

/**
 * Class Comment
 * @package api\models\comment
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="module_type", type="integer", description="Тип модуля", enum={40, 81}),
 *     @OA\Property(property="module_id", type="integer", description="ID модуля"),
 *     @OA\Property(property="parent_id", type="integer", description="ID родительской записи"),
 *     @OA\Property(property="level", type="integer", description="Уровень в дереве"),
 *     @OA\Property(property="content", type="string", description="Комментарий"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_at", type="integer", description="Дата и время создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата и время изменения"),
 *     @OA\Property(property="created_by", type="integer", description="Создан пользователем"),
 *     @OA\Property(property="updated_by", type="integer", description="Изменен пользователем"),
 *     @OA\Property(property="owner", type="object",
 *         @OA\Property(property="id", type="integer", description="ID"),
 *         @OA\Property(property="type", type="string", description="Тип", enum={"user", "company"}),
 *         @OA\Property(property="name", type="string", description="Имя"),
 *         @OA\Property(property="username", type="string", description="Юзернейм"),
 *         @OA\Property(property="is_online", type="boolean", description="Признак онлайна"),
 *         @OA\Property(property="is_company", type="boolean", description="Признак компании"),
 *         @OA\Property(property="image", type="object",
 *             @OA\Property(property="path", type="string", description="Путь к изображению"),
 *             @OA\Property(property="file", type="string", description="Название файла изображения")
 *         )
 *     ),
 *     @OA\Property(property="stats", type="object",
 *         @OA\Property(property="likes", type="integer", description="Количество лайков"),
 *         @OA\Property(property="dislikes", type="integer", description="Количество дизлайков"),
 *         @OA\Property(property="has_like", type="boolean", description="Признак лайкал ли пользователь"),
 *         @OA\Property(property="has_dislike", type="boolean", description="Признак дизлайкал ли пользователь")
 *     ),
 * )
 */
class Comment extends BaseComment
{
    /**
     * @return array
     */
    public function rules() {
        return ArrayHelper::merge(parent::rules(), [
            ['rating', 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     *
     * The default implementation returns the names of the columns whose values have been populated into this record.
     */
    public function fields() {
        return [
            'id',
            'module_type',
            'module_id',
            'parent_id' => function($data) {
                return (int)$data->parent_id;
            },
            'level',
            'content' => function($data) {
                return $data->getContent_parsed();
            },
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'status',
            'stats' => function($data) {
                $like = $data->getUserValue(Vote::COMMENT_VOTE);
                $vote = $data->getVoteAggregate(Vote::COMMENT_VOTE);
                $ratingAggregate = $data->getVoteAggregate(Vote::COMMENT_RATING);

                $rating = 0;
                if ($data->rating) {
                    $rating = $data->rating;
                }
                else if ($ratingAggregate && $ratingAggregate->rating) {
                    $rating = $ratingAggregate->rating;
                }

                return [
                    'likes' => $vote->positive ? $vote->positive : 0,
                    'dislikes' => $vote->negative ? $vote->negative : 0,
                    'has_like' => $like === 1 ? true : false,
                    'has_dislike' => $like === 0 ? true : false,
                    'rating' => $rating,
                ];
            },
            'owner' => function($data) {
                return $this->getOwner();
            },
            'tags_ids' => function($data) {
                return ArrayHelper::getColumn($data->tags, 'id');
            },
            'tags',
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getAuthor() {
        return $this->hasOne(User::class, ['id' => 'created_by'])->where([]);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::class, ['id' => 'company_id'])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagModule')->where([]);
    }
    
    public function getOwner() {
        $isCompany = $this->getIsCompany();
    
        $id = $isCompany ? $this->company_id : $this->created_by;
        $type = $isCompany ? 'company' : 'user';
        $title = $isCompany ? $this->company->title : $this->author->getAuthorName();
        $username = $isCompany ? '' : $this->author->username;
        $image = $isCompany ? $this->company->getMediaImage() : $this->author->getMediaImage();
        $isOnline = $isCompany ? false : $this->author->getIsOnline();
        
        return [
            'id' => $id,
            'type' => $type,
            'title' => $title,
            'username' => $username,
            'image' => $image,
            'is_online' => $isOnline,
            'is_company' => $isCompany
        ];
    }

    /**
     * @return string|string[]|null
     */
    public function getContent_parsed() {
        $tmp = $this->content;

        // Remove images
        $tmp = preg_replace("/<img[^>]+\>/i", "", $tmp);

        // Remove tags
        $tmp = strip_tags($tmp);

        return $tmp;
    }

    public function beforeSave($insert)
    {
        $this->_tags_ids_old = ($insert) ? [] : self::findById($this->id, true)->getTags_ids();
        return parent::beforeSave($insert);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes) {
        if (is_null($this->status)) {
            $this->status = Status::ENABLED;
        }

        if ($this->rating) {
            $entity = Yii::$app->getModule('vote')->encodeEntity(Vote::COMMENT_RATING);
            $model = Vote::find()->where([
                'entity' => $entity,
                'entity_id' => $this->id,
            ])->one();
            if (is_null($model)) {
                $model = new Vote();
                $model->entity = $entity;
                $model->entity_id = $this->id;
            }
            $model->value = $this->rating;
            $model->save();
        }

        $this->_saveTagsIds();

        parent::afterSave($insert, $changedAttributes);
    }

    private function _saveTagsIds()
    {
        if ($this->module_type == ModuleType::CATALOG_ITEM) {

            if (is_null($this->tags_ids)) {
                $this->tags_ids = [];
            }

            /** @var CatalogItem $catalogItem */
            $catalogItem = CatalogItem::find()->where(['id' => $this->entity_id])->one();
            if (is_null($catalogItem)) {
                return;
            }

            $platformIds = $catalogItem->platforms ? ArrayHelper::getColumn($catalogItem->platforms, 'id') : [];

            $diffIds = array_diff($this->tags_ids, $platformIds);

            CatalogItemCorrect::deleteAll([
                'catalog_item_id' => $catalogItem->id,
                'type' => CorrectType::TAG,
                'action' => CorrectAction::ADD,
                'created_by' => Yii::$app->user->id,
            ]);

            foreach ($diffIds as $diffId) {
                self::getDb()->createCommand()->insert(CatalogItemCorrect::tableName(), [
                    'catalog_item_id' => $catalogItem->id,
                    'type' => CorrectType::TAG,
                    'action' => CorrectAction::ADD,
                    'value' => (int)$diffId,
                    'created_at' => time(),
                    'updated_at' => time(),
                    'created_by' => Yii::$app->user->id,
                    'updated_by' => Yii::$app->user->id,
                ])->execute();
            }

            TagModule::updateLinks($this->_tags_ids_old, $this->tags_ids, self::moduleType(), $this->id);
        }
    }


}
