<?php
namespace common\modules\content\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;
use common\modules\company\models\Company;
use common\modules\tag\models\Tag;

use common\modules\content\models\query\ContentTagQuery;

/**
 * This is the model class for table "{{%content_tag}}".
 *
 * @property int $id
 * @property int $content_id
 * @property int $tag_id
 * @property int $author_id
 * @property int $company_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Tag $tag
 * @property User $author
 * @property Content $content
 */
class ContentTag extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_tag}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content_id', 'tag_id'], 'required'],
            [['content_id', 'tag_id', 'author_id', 'company_id', 'created_at', 'updated_at'], 'integer'],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::class, 'targetAttribute' => ['tag_id' => 'id']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
            [['content_id'], 'exist', 'skipOnError' => true, 'targetClass' => Content::class, 'targetAttribute' => ['content_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'content_id' => 'Content ID',
            'tag_id' => 'Tag ID',
            'author_id' => 'Author ID',
			'company_id' => 'Company ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getTag() {
        return $this->hasOne(Tag::class, ['id' => 'tag_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor() {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getCompany() {
		return $this->hasOne(Company::class, ['id' => 'company_id']);
	}
    

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\query\ContentTagQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentTagQuery(get_called_class());
    }
	
	/**
	 * Update links
	 * @param int $contentId
	 * @param int $authorId
	 * @param int $companyId
	 * @param array $tagsIds
	 *
	 * @throws \yii\db\Exception
	 */
    static public function updateLinks(int $contentId, int $authorId, int $companyId, array $tagsIds) {
		self::getDb()->createCommand('DELETE FROM '.self::tableName().' WHERE content_id = :content_id AND company_id = :company_id', [
			':content_id' => $contentId,
			':company_id' => $companyId,
		])->execute();
	
		$inserts = [];
		foreach ($tagsIds as $tagId)
			$inserts[] = [$contentId, $authorId, $companyId, $tagId, time(), time()];
		self::getDb()->createCommand()->batchInsert(self::tableName(), ['content_id', 'author_id', 'company_id', 'tag_id', 'created_at', 'updated_at'], $inserts)->execute();
	
	}
}
