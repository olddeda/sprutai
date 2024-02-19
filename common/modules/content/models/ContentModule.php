<?php
namespace common\modules\content\models;

use common\modules\content\models\query\ContentTagQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Exception;

use common\modules\base\components\ActiveRecord;

use common\modules\content\models\query\ContentModuleQuery;

/**
 * This is the model class for table "{{%content_module}}".
 *
 * @property int $id
 * @property int $content_id
 * @property int $module_type
 * @property int $module_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Content $content
 */
class ContentModule extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_module}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content_id', 'module_type', 'module_id'], 'required'],
            [['content_id', 'module_type', 'module_id', 'created_at', 'updated_at'], 'integer'],
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
            'module_type' => 'Module Type',
            'module_id' => 'Module ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
	
	/**
	 * @return ActiveQuery
	 */
	public function getContent() {
		return $this->hasOne(Content::class, ['id' => 'content_id']);
	}

    /**
     * {@inheritdoc}
     * @return ContentTagQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentModuleQuery(get_called_class());
    }

    /**
     * Update links
     *
     * @param int $contentId
     * @param array $modules
     *
     * @throws Exception
     */
    static public function updateLinks(int $contentId, array $modules) {
		self::getDb()->createCommand('DELETE FROM '.self::tableName().' WHERE content_id = :content_id', [
			':content_id' => $contentId,
		])->execute();
	
		$inserts = [];
		if (count($modules)) {
		    foreach ($modules as $m)
			    $inserts[] = [$contentId, $m['module_type'], $m['module_id'], time(), time()];
		    self::getDb()->createCommand()->batchInsert(self::tableName(), ['content_id', 'module_type', 'module_id', 'created_at', 'updated_at'], $inserts)->execute();
		}
	}
}
