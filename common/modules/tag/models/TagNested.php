<?php
namespace common\modules\tag\models;

use common\modules\base\components\Debug;
use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;

use common\modules\base\components\ActiveRecord;
use common\modules\base\behaviors\tree\nestedsets\NestedSetsBehavior;

use common\modules\user\models\User;

use common\modules\tag\models\query\TagNestedQuery;

/**
 * This is the model class for table "{{%tag_nested}}".
 *
 * @property integer $id
 * @property integer $module_type
 * @property integer $module_id
 * @property integer $parent_id
 * @property integer $tag_id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Tag $parent
 * @property Tag $tag
 * @property User $createdBy
 * @property User $updatedBy
 */
class TagNested extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%tag_nested}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => NestedSetsBehavior::class,
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function transactions() {
		return [
			self::SCENARIO_DEFAULT => self::OP_ALL,
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'module_id', 'parent_id', 'tag_id'], 'required'],
			[['module_type', 'module_id', 'parent_id', 'tag_id', 'root', 'lft', 'rgt', 'depth', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('tag-nested', 'field_id'),
			'parent_id' => Yii::t('tag-nested', 'field_parent_id'),
			'tag_id' => Yii::t('tag-nested', 'field_tag_id'),
			'root' => Yii::t('tag-nested', 'field_root'),
			'lft' => Yii::t('tag-nested', 'field_lft'),
			'rgt' => Yii::t('tag-nested', 'field_rgt'),
			'level' => Yii::t('tag-nested', 'field_level'),
			'created_by' => Yii::t('tag-nested', 'field_created_by'),
			'updated_by' => Yii::t('tag-nested', 'field_updated_by'),
			'created_at' => Yii::t('tag-nested', 'field_created_at'),
			'updated_at' => Yii::t('tag-nested', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\tag\models\query\TagNestedQuery the active query used by this AR class.
	 */
	public static function find() {
		return new TagNestedQuery(get_called_class());
	}
	
	/**
	 * Get tag
	 * @return \common\modules\tag\models\query\TagQuery
	 */
	public function getTag() {
		return $this->hasOne(Tag::class, ['id' => 'tag_id']);
	}
	
	/**
	 * Get created user model
	 * @return \common\modules\user\models\query\UserQuery
	 */
	public function getCreatedBy() {
		return $this->hasOne(User::class, ['id' => 'created_by']);
	}
	
	/**
	 * Get updated user model
	 * @return \common\modules\user\models\query\UserQuery
	 */
	public function getUpdatedBy() {
		return $this->hasOne(User::class, ['id' => 'updated_by']);
	}
	
	/**
	 * Find node
	 *
	 * @param integer $moduleType
	 * @param integer $moduleId
	 * @param integer $parentId
	 * @param integer $tagId
	 *
	 * @return ActiveRecord|TagNested
	 */
	static public function findNode($moduleType, $moduleId, $parentId, $tagId) {
		$model = self::find()->where('parent_id = :parent_id AND tag_id = :tag_id', [
			':parent_id' => $parentId,
			':tag_id' => $tagId,
		])->one();
		return $model;
	}
	
	/**
	 * @param integer $moduleType
	 * @param integer $moduleId
	 * @param bool $root
	 * @param bool $maxLevel
	 * @param bool $cache
	 *
	 * @return array
	 * @throws \Throwable
	 */
	static public function tree($moduleType, $moduleId, $root = false, $maxLevel = false, $cache = false) {
		
		// Create query
		$query = self::find()->where([])->joinWith([
			'tag',
		]);
		
		// Create cache depedency
		$dependency = new DbDependency();
		$dependency->sql = 'SELECT MAX(updated_at) FROM '.self::tableName();
		
		$tree = [];
		$items = [];
		
		if ($root === false) {
			$queryRoots = $query->roots();
			
			if ($cache) {
				$items = self::getDb()->cache(function ($db) use($queryRoots) {
					return $queryRoots->all();
				}, Yii::$app->params['cache.duration'], $dependency);
			}
			else {
				$items = $queryRoots->all();
			}
		}
		else {
			if (!$maxLevel || $root->level <= $maxLevel) {
				$queryChildren = $root->getChildren();
				if ($cache) {
					$items = self::getDb()->cache(function ($db) use($queryChildren) {
						return $queryChildren->all();
					}, Yii::$app->params['cache.duration'], $dependency);
				}
				else {
					$items = $queryChildren->all();
				}
			}
			else
				return $tree;
		}
		
		
		foreach ($items as $item) {
			
			/** @var TagNested $item */
			if ($item->tag) {
				$tree[$item->tag->id] = [
					'id' => $item->tag->id,
					'nested_id' => $item->id,
					'level' => $item->depth,
					'type' => $item->tag->type,
					'title' => $item->tag->title,
					'sequence' => $item->tag->sequence,
					'color' => $item->tag->color,
					'items' => (!$maxLevel || $item->level < $maxLevel) ? static::tree($moduleType, $moduleId, $item, $maxLevel) : null,
				];
			}
		}
		
		usort($tree, function ($item1, $item2) {
			if ($item1['sequence'] == $item2['sequence']) return 0;
			return $item1['sequence'] < $item2['sequence'] ? -1 : 1;
		});
		
		return $tree;
	}
}