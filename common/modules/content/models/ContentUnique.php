<?php
namespace common\modules\content\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\content\models\query\ContentUniqueQuery;

/**
 * This is the model class for table "{{%content_unique}}".
 *
 * @property int $id
 * @property int $content_id
 * @property string $text
 * @property string $uid
 * @property array $urls
 * @property array $spellcheck
 * @property string $unique
 * @property int $count_chars_with_space
 * @property int $count_chars_without_space
 * @property int $count_words
 * @property int $water_percent
 * @property int $spam_percent
 * @property int $queue
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class ContentUnique extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_unique}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content_id', 'uid', 'text'], 'required'],
            [['content_id', 'count_chars_with_space', 'count_chars_without_space', 'count_words', 'water_percent', 'spam_percent', 'queue', 'status', 'created_at', 'updated_at'], 'integer'],
            [['text'], 'string'],
            [['urls', 'spellcheck'], 'safe'],
            [['unique'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('content-unique', 'field_id'),
            'content_id' => Yii::t('content-unique', 'field_content_id'),
            'text' => Yii::t('content-unique', 'field_text'),
			'uid' => Yii::t('content-unique', 'field_uid'),
            'urls' => Yii::t('content-unique', 'field_urls'),
			'spellcheck' => Yii::t('content-unique', 'field_spellcheck'),
            'unique' => Yii::t('content-unique', 'field_unique'),
            'count_chars_with_space' => Yii::t('content-unique', 'field_count_chars_with_space'),
            'count_chars_without_space' => Yii::t('content-unique', 'field_count_chars_without_space'),
            'count_words' => Yii::t('content-unique', 'field_count_words'),
            'water_percent' => Yii::t('content-unique', 'field_water_percent'),
            'spam_percent' => Yii::t('content-unique', 'field_spam_percent'),
            'queue' => Yii::t('content-unique', 'field_queue'),
            'status' => Yii::t('content-unique', 'field_status'),
            'created_at' => Yii::t('content-unique', 'field_created_at'),
            'updated_at' => Yii::t('content-unique', 'field_updated_at'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\query\ContentUniqueQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentUniqueQuery(get_called_class());
    }
}
