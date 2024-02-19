<?php
namespace common\modules\catalog\models;

use Yii;

use common\modules\base\components\ActiveRecord;
use common\modules\catalog\models\query\CatalogItemStatQuery;
use common\modules\content\helpers\enum\Type as ContentType;
use common\modules\vote\models\Vote;

/**
 * This is the model class for table "{{%catalog_item_stat}}".
 *
 * @property int $id
 * @property int $catalog_item_id
 * @property int $comments
 * @property double $rating
 * @property int $likes
 * @property int $favorites
 * @property int $favorite_have
 * @property int $contents
 * @property int $videos
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CatalogItem $catalogItem
 */
class CatalogItemStat extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%catalog_item_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['catalog_item_id'], 'required'],
            [['catalog_item_id', 'comments', 'likes', 'favorites', 'contents', 'videos', 'created_at', 'updated_at'], 'integer'],
            [['rating'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'catalog_item_id' => 'CatalogItem ID',
            'comments' => 'Comments',
            'likes' => 'Likes',
            'favorites' => 'Favorites',
            'contents' => 'Contents',
            'videos' => 'Videos',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogItem() {
        return $this->hasOne(CatalogItem::class, ['id' => 'catalog_item_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\catalog_item\models\query\CatalogItemStatQuery the active query used by this AR class.
     */
    public static function find() {
        return new CatalogItemStatQuery(get_called_class());
    }
	
	/**
	 * Update links
	 * @param CatalogItem $catalogItem
	 */
    static public function updateLinks(CatalogItem $catalogItem) {
		$model = $catalogItem->stat;

		if (is_null($model)) {
			$model = new CatalogItemStat();
			$model->catalog_item_id = $catalogItem->id;
		}

		$ratingQuery = $catalogItem->getComments()->where(['level' => 1]);

		$rating = 0;
		$ratingIds = $ratingQuery->select('id')->column();
		if (count($ratingIds)) {
		    $voteModule = Yii::$app->getModule('vote');
            $rating = Vote::find()->where([
                'AND',
                ['entity' => $voteModule->encodeEntity(Vote::COMMENT_RATING)],
                ['in', 'entity_id', $ratingIds]
            ])->average('value');
        }

		$model->comments = $ratingQuery->count();
		$model->rating = $rating;

		$model->contents = $catalogItem->getContents()->andWhere([
		    '<>', 'type', ContentType::VIDEO,
        ])->count();

        $model->videos = $catalogItem->getContents()->andWhere([
            'type' => ContentType::VIDEO,
        ])->count();
		$model->favorite_have = $catalogItem->getFavoritesHave()->count();
		$model->save();
	}
}
