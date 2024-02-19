<?php
namespace common\modules\content\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\content\models\query\ContentStatQuery;

/**
 * This is the model class for table "{{%content_stat}}".
 *
 * @property int $id
 * @property int $content_id
 * @property int $comments
 * @property int $likes
 * @property int $favorites
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Content $content
 */
class ContentStat extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content_id'], 'required'],
            [['content_id', 'comments', 'likes', 'favorites', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'content_id' => 'Content ID',
            'comments' => 'Comments',
            'likes' => 'Likes',
            'favorites' => 'Favorites',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContent() {
        return $this->hasOne(Content::class, ['id' => 'content_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\query\ContentStatQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentStatQuery(get_called_class());
    }
	
	/**
	 * Update links
	 * @param Content $content
	 */
    static public function updateLinks(Content $content) {
		$model = $content->stat;
		
		if (is_null($model)) {
			$model = new ContentStat();
			$model->content_id = $content->id;
		}
		
		$model->comments = $content->getComments()->count();
		$model->save();
	}
}
