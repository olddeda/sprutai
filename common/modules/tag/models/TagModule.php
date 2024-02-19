<?php
namespace common\modules\tag\models;

use Yii;
use yii\db\Exception;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;
use common\modules\user\models\query\UserQuery;

use common\modules\tag\models\query\TagQuery;
use common\modules\tag\models\query\TagModuleQuery;
use common\modules\tag\helpers\enum\Type;


/**
 * This is the model class for table "am_tag_module".
 *
 * @property integer $id
 * @property integer $tag_id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Tag $tag
 * @property User $createdBy
 * @property User $updatedBy
 */
class TagModule extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%tag_module}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['tag_id', 'module_type', 'module_id', 'status'], 'required'],
			[['tag_id', 'module_type', 'module_id', 'root', 'lft', 'rgt', 'level', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', ], 'integer'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('tag-module', 'field_id'),
			'tag_id' => Yii::t('tag-module', 'field_tag_id'),
			'module_type' => Yii::t('tag-module', 'field_module_type'),
			'module_id' => Yii::t('tag-module', 'field_module_id'),
			'root' => Yii::t('tag-module', 'field_root'),
			'lft' => Yii::t('tag-module', 'field_lft'),
			'rgt' => Yii::t('tag-module', 'field_rgt'),
			'level' => Yii::t('tag-module', 'field_level'),
			'status' => Yii::t('tag-module', 'field_status'),
			'created_at' => Yii::t('tag-module', 'field_created_at'),
			'updated_at' => Yii::t('tag-module', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\tag\models\query\TagModuleQuery the active query used by this AR class.
	 */
	public static function find() {
		return new TagModuleQuery(get_called_class());
	}
	
	/**
	 * Get tag
	 * @return TagQuery
	 */
	public function getTag() {
		return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
	}
	
	/**
	 * Get created user model
	 * @return UserQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}
	
	/**
	 * Get updated user model
	 * @return UserQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}

    /**
     * Update links
     *
     * @param $tagsOld
     * @param $tagsNew
     * @param ModuleType $moduleType
     * @param integer $moduleId
     *
     * @throws Exception
     */
	public static function updateLinks($tagsOld, $tagsNew, $moduleType, $moduleId) {
		if (!is_array($tagsOld))
			$tagsOld = [];
		if (!is_array($tagsNew))
			$tagsNew = [];
		
		$tagsToAdd = array_values(array_diff($tagsNew, $tagsOld));
		$tagsToRemove = array_values(array_diff($tagsOld, $tagsNew));
		
		// Remove links
		if (count($tagsToRemove)) {
			self::removeLinks($tagsToRemove, $moduleType, $moduleId);
		}
		
		// Add links
		if (count($tagsToAdd)) {
			self::addLinks($tagsToAdd, $moduleType, $moduleId);
		}
	
	}

    /**
     * @param array $tagsIds
     * @param $moduleType
     * @param $moduleId
     *
     * @throws Exception
     */
	public static function addLinks(array $tagsIds, $moduleType, $moduleId) {
		$time = time();
		$rows = [];
		
		foreach ($tagsIds as $tagId)
			$rows[] = [$tagId, $moduleType, $moduleId, Yii::$app->user->id, Yii::$app->user->id, $time, $time];
		
		self::getDb()->createCommand()->batchInsert(self::tableName(), [
			'tag_id', 'module_type', 'module_id', 'created_by', 'updated_by', 'created_at', 'updated_at',
		], $rows)->execute();
		
		if ($moduleType == ModuleType::TAG) {
			foreach ($tagsIds as $tagId) {
				
				/** @var TagNested $modelParentNested */
				$modelParentNested = TagNested::find()->where(['tag_id' => $moduleId])->one();
				if ($modelParentNested === null) {
					$modelParentNested = new TagNested();
					$modelParentNested->parent_id = 0;
					$modelParentNested->tag_id = $moduleId;
					$modelParentNested->makeRoot()->save();
				}
				
				/** @var TagNested $modelChildNested */
				$modelChildNested = TagNested::find()->where(['tag_id' => $tagId])->one();
				if ($modelChildNested === null) {
					$modelChildNested = new TagNested();
					$modelChildNested->parent_id = $moduleId;
					$modelChildNested->tag_id = $tagId;
					$modelChildNested->appendTo($modelParentNested)->save();
				}
			}
		}
	}

    /**
     * @param array $tagsIds
     * @param $moduleType
     * @param $moduleId
     *
     * @throws Exception
     */
	public static function removeLinks(array $tagsIds, $moduleType, $moduleId) {
		$time = time();
		
		self::getDb()->createCommand()->delete(self::tableName(), [
			'module_type' => $moduleType,
			'module_id' => $moduleId,
			'tag_id' => $tagsIds,
		])->execute();
		
		if ($moduleType == ModuleType::TAG) {
			
			/** @var TagNested $modelParentNested */
			$modelParentNested = TagNested::find()->where(['tag_id' => $moduleId])->one();
			if ($modelParentNested) {
				foreach ($tagsIds as $tagId) {
					
					/** @var TagNested $modelChildNested */
					$modelChildNested = TagNested::find()->where(['tag_id' => $tagId])->one();
					if ($modelChildNested) {
						$modelChildNested->delete();
					}
				}
			}
		}
	}
}