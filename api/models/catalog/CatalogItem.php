<?php
namespace api\models\catalog;

use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

use api\models\comment\Comment;
use api\models\favorite\Favorite;
use api\models\seo\Seo;
use api\models\tag\Tag;
use api\models\user\User;

use api\traits\ImageTrait;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\catalog\models\CatalogItem as BaseModel;

use common\modules\shortener\models\Shortener;

use common\modules\tag\helpers\enum\Type as TagType;
use common\modules\tag\models\query\TagModuleQuery;
use common\modules\tag\models\query\TagQuery;
use common\modules\tag\models\TagModule;

use common\modules\vote\models\Vote;

/**
 * Class CatalogItem
 * @package api\models\catalog
 */

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="integer", description="ID"),
 *     @OA\Property(property="slug", type="string", description="Идентификатор"),
 *     @OA\Property(property="vendor_id", type="integer", description="ID производителя"),
 *     @OA\Property(property="yandex_id", type="integer", description="ID карточки товара в Yandex.Market"),
 *     @OA\Property(property="title", type="string", description="Название"),
 *     @OA\Property(property="model", type="string", description="Модель"),
 *     @OA\Property(property="url", type="string", description="Ссылка на товар на сайте производителя"),
 *     @OA\Property(property="documentation_url", type="string", description="Ссылка на документацию"),
 *     @OA\Property(property="comment", type="string", description="Комментарий"),
 *     @OA\Property(property="system_manufacturer", type="string", description="Системный: Производитель"),
 *     @OA\Property(property="system_model", type="string", description="Системный: Модель"),
 *     @OA\Property(property="status", type="integer", description="Статус"),
 *     @OA\Property(property="created_by", type="integer", description="ID пользователя который создал"),
 *     @OA\Property(property="updated_by", type="integer", description="ID пользователя который последний изменял"),
 *     @OA\Property(property="created_at", type="integer", description="Дата создания"),
 *     @OA\Property(property="updated_at", type="integer", description="Дата обновления"),
 *     @OA\Property(property="seo_id", type="integer", description="ID Seo"),
 *     @OA\Property(property="tags_ids", type="array", @OA\Items(type="integer"), description="ID тегов"),
 *     @OA\Property(property="image", ref="#/components/schemas/Image", description="Изображение"),
 *     @OA\Property(property="vendor", ref="#/components/schemas/Tag", description="Производитель"),
 *     @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/Tag"), description="Теги (отдается при запросе ?expand=tags)"),
 *     @OA\Property(property="seo", type="array", @OA\Items(ref="#/components/schemas/Seo"), description="SEO (отдается при запросе ?expand=text)"),
 *     @OA\Property(property="stats", type="object",
 *         @OA\Property(property="comments", type="integer", description="Колличество комментариев"),
 *         @OA\Property(property="contents", type="integer", description="Колличество материалов"),
 *         @OA\Property(property="videos", type="integer", description="Колличество видео обзоров"),
 *         @OA\Property(property="show", type="integer", description="Колличество просмотров"),
 *         @OA\Property(property="visit", type="integer", description="Колличество визитов"),
 *         @OA\Property(property="likes", type="integer", description="Количество лайков"),
 *         @OA\Property(property="dislikes", type="integer", description="Количество дизлайков"),
 *         @OA\Property(property="has_like", type="boolean", description="Пользователь ставил лайк"),
 *         @OA\Property(property="has_dislike", type="boolean", description="Пользователь ставил дизалайк"),
 *         @OA\Property(property="has_have", type="boolean", description="Есть устройство у пользователя")
 *     )
 * )
 */
class CatalogItem extends BaseModel
{
    use ImageTrait;

    /**
     * @var boolean
     */
    public $showVendor;

    /**
     * @var integer
     */
    public $count_favorite_have;

    /**
     * @var integer
     */
    public $count_compare;

    /**
     * @var boolean
     */
    public $has_spruthub_support;

    /**
     * @inheritdoc
     */
    public function fields() {
        return [
            'id',
            'slug',
            'vendor_id',
            'yandex_id',
            'title',
            'description',
            'model',
            'url',
            'documentation_url',
            'comment',
            'system_manufacturer',
            'system_model',
            'price',
            'in_stock',
            'sequence',
            'is_sprut',
            'is_sale',
            'sprut_type',
            'sprut_content_json',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'seo_id' => function ($data) {
                return $this->seoRelation ? $this->seoRelation->id : null;
            },
            'tags_ids',
            'fields',
            'image' => function($data) {
                return $data->mediaImageFor('image');
            },
            'image_mobile' => function($data) {
                return $data->mediaImageFor('image_mobile');
            },
            'image_desktop' => function($data) {
                return $data->mediaImageFor('image_desktop');
            },
            'comments' => function ($data) {
                return $data->getCommentsUsersFields();
            },
            'stats' => function($data) {
                $exclude = ArrayHelper::getValue(Yii::$app->request->get(), 'exclude');
                if ($exclude) {
                    if (in_array('stats', explode(',', $exclude))) {
                        return [];
                    }
                }
                $like = $data->getUserValue(Vote::CATALOG_ITEM_VOTE);
                $vote = $data->getVoteAggregate(Vote::CATALOG_ITEM_VOTE);
                $hasHave = Yii::$app->user->id ? !is_null($this->favoritesHaveOwn) : false;
                return [
                    'comments' => $data->stat ? $data->stat->comments : 0,
                    'contents' => $data->stat->contents ? $data->stat->contents : 0,
                    'videos' => $data->stat->videos ? $data->stat->videos : 0,
                    'rating' => $data->stat ? $data->stat->rating : 0,
                    'favorite_have' => $data->stat ? $data->stat->favorite_have : 0,
                    'likes' => $vote->positive ? $vote->positive : 0,
                    'dislikes' => $vote->negative ? $vote->negative : 0,
                    'has_like' => $like === 1 ? true : false,
                    'has_dislike' => $like === 0 ? true : false,
                    'has_have' => $hasHave,
                    'count_compare' => (int)$data->count_compare,
                    'has_compare' => (int)$data->count_compare > 0,
                    'has_spruthub_support' => (boolean)$data->has_spruthub_support,
                ];
            },
            'data',
            'content_ids' => function ($data) {
                $exclude = ArrayHelper::getValue(Yii::$app->request->get(), 'content_ids');
                if ($exclude) {
                    if (in_array('stats', explode(',', $exclude))) {
                        return [];
                    }
                }
                if ($data->isRelationPopulated('contentModule')) {
                    return ArrayHelper::getColumn($data->contentModule, 'content_id');
                }
                return [];
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        return [
            'vendor',
            'tags',
            'comment',
            'seo' => function ($data) {
                return $data->getSeoFields();
            },
            'available',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSeoRelation() {
        return $this->hasOne(Seo::class, ['module_id' => 'id'])->onCondition([
            Seo::tableName().'.module_type' => ModuleType::CATALOG_ITEM
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getVendor() {
        return $this->hasOne(Tag::class, ['id' => 'vendor_id'])->where([]);
    }

    /**
     * @return TagModuleQuery
     */
    public function getTagModuleTypes() {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->alias('tag_module_types')->andOnCondition([
            'tag_module_types.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return TagModuleQuery
     */
    public function getTagModulePlatforms() {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->alias('tag_module_platforms')->andOnCondition([
            'tag_module_platforms.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return TagModuleQuery
     */
    public function getTagModuleProtocols() {
        return $this->hasMany(TagModule::class, ['module_id' => 'id'])->alias('tag_module_protocols')->andOnCondition([
            'tag_module_protocols.module_type' => self::moduleType(),
        ])->where([]);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getTypes() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->alias('types')
            ->via('tagModuleTypes')
            ->where([])
            ->andOnCondition('types.type & '.TagType::TYPE);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getPlatforms() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->alias('platforms')
            ->via('tagModulePlatforms')
            ->where([])
            ->andOnCondition('platforms.type & '.TagType::PLATFORM);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getProtocols() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->alias('protocols')
            ->via('tagModuleProtocols')
            ->where([])
            ->andOnCondition('protocols.type & '.TagType::PROTOCOL);
    }

    /**
     * @return TagQuery|ActiveQuery
     */
    public function getTags() {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->via('tagModule')
            ->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getFields(): ActiveQuery
    {
        return $this->hasMany(CatalogItemField::class, ['catalog_item_id' => 'id'])->onCondition([
            CatalogItemField::tableName().'.status' => Status::ENABLED,
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getComments() {
        return $this->hasMany(Comment::class, ['entity_id' => 'id'])->alias('comments')->onCondition([
            'comments.module_type' => ModuleType::CATALOG_ITEM,
            'comments.status' => Status::ENABLED,
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getReviews() {
        return $this->hasMany(Comment::class, ['entity_id' => 'id'])->alias('reviews')->onCondition([
            'reviews.module_type' => ModuleType::CATALOG_ITEM,
            'reviews.status' => Status::ENABLED,
            'reviews.level' => 1,
        ])->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public function getCommentsUsers() {
        return $this->hasMany(User::class, ['id' => 'created_by'])->via('reviews')
            ->joinWith([
                'profile' => function($query) {},
                'mediaAvatar' => function($query) {
                    $query->alias('ma');
                },
                'contentsStat' => function($query) {
                    $query->alias('cs')->where([]);
                }
            ])
            ->where([])
            ->orderBy(['cs.subscribers' => SORT_DESC])
            ->groupBy(User::tableName().'.id')
        ;
    }

    /**
     * @return ActiveQuery
     */
    public function getFavorites() {
        return $this->hasMany(Favorite::class, ['module_id' => 'id'])->onCondition([
            Favorite::tableName().'.module_type' => ModuleType::CATALOG_ITEM,
            Favorite::tableName().'.user_id' => Yii::$app->user->id,
        ]);
    }

    /**
     * @return array
     */
    public function getCommentsUsersFields() {
        $tmp = [];

        $commentsUsers = $this->commentsUsers;
        if ($commentsUsers) {
            foreach ($commentsUsers as $user) {
                $imageInfo = $user->avatar->getImageInfo();
                $tmp[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->getAuthorName(),
                    'image' => [
                        'path' => $imageInfo['http'],
                        'file' => $imageInfo['file'],
                    ]
                ];
            }
        }

        return [
            'users' => $tmp
        ];
    }

    /**
     * @return array
     */
    public function getStats() {
        return [
            'comments' => $this->stat ? $this->stat->comments : 0,
            'contents' => $this->stat ? $this->stat->contents : 0,
        ];
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->seoRelation ? $this->seoRelation->slugify : null;
    }

    /**
     * @return Seo
     */
    public function getSeoFields() {
        $seo = $this->seoRelation;
        if (!$seo) {
            $seo = new Seo();
        }
        return $seo;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getTagsIdsByType($type) {
        $tmp = [];
        if ($this->tags) {
            foreach ($this->tags as $tag) {
                if ($tag->type & $type) {
                    $tmp[] = $tag->id;
                }
            }
        }
        return $tmp;
    }

    /**
     * @param $insert
     *
     * @return bool
     */
    public function beforeSave($insert) {
        if (is_array($this->data) && isset($this->data['shops']) && is_array($this->data['shops'])) {
            $data = $this->data;
            foreach ($data['shops'] as $key => $shop) {
                $url = $shop['url'];
                if ($url) {
                    $shortener = Shortener::find()->where('url = :url AND status = :status', [
                        ':url' => $url,
                        ':status' => Status::ENABLED
                    ])->one();
                    if (!$shortener) {
                        $title = '';
                        if ($this->vendor) {
                            $title .= $this->vendor->title.': ';
                        }
                        $title .= $this->title;

                        $shortener = new Shortener();
                        $shortener->url = $url;
                        $shortener->title = $title;
                        $shortener->description = '';
                        $shortener->status = Status::ENABLED;
                        $shortener->save();
                    }
                    $shop['short_url'] = $shortener->short_url;
                    $data['shops'][$key] = $shop;
                }
            }
            $this->data = $data;
        }
        return parent::beforeSave($insert);
    }
}