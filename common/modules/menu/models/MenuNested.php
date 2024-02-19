<?php
namespace common\modules\menu\models;

use common\modules\base\components\Debug;
use Yii;
use yii\helpers\ArrayHelper;
use yii\caching\DbDependency;

use common\modules\base\components\ActiveRecord;
use common\modules\base\behaviors\tree\nestedsets\NestedSetsBehavior;

use common\modules\user\models\User;

use common\modules\menu\models\query\MenuNestedQuery;

/**
 * This is the model class for table "{{%menu_nested}}".
 *
 * @property integer $id
 * @property integer $menu_id
 * @property integer $menu_item_id
 * @property integer $parent_id
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
 * @property \common\modules\menu\models\MenuItem $parent
 * @property \common\modules\menu\models\MenuItem $item
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class MenuNested extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%menu_nested}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => NestedSetsBehavior::class,
				'conditionAttributes' => ['menu_id'],
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
			[['parent_id', 'menu_id', 'menu_item_id'], 'required'],
			[['parent_id', 'menu_id', 'menu_item_id', 'root', 'lft', 'rgt', 'depth', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('menu-nested', 'field_id'),
			'parent_id' => Yii::t('menu-nested', 'field_parent_id'),
			'menu_id' => Yii::t('menu-nested', 'field_menu_id'),
			'menu_item_id' => Yii::t('menu-nested', 'field_menu_item_id'),
			'root' => Yii::t('menu-nested', 'field_root'),
			'lft' => Yii::t('menu-nested', 'field_lft'),
			'rgt' => Yii::t('menu-nested', 'field_rgt'),
			'level' => Yii::t('menu-nested', 'field_level'),
			'created_by' => Yii::t('menu-nested', 'field_created_by'),
			'updated_by' => Yii::t('menu-nested', 'field_updated_by'),
			'created_at' => Yii::t('menu-nested', 'field_created_at'),
			'updated_at' => Yii::t('menu-nested', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\menu\models\query\MenuNestedQuery the active query used by this AR class.
	 */
	public static function find() {
		return new MenuNestedQuery(get_called_class());
	}
	
	/**
	 * Get menu
	 * @return \common\modules\menu\models\query\MenuQuery
	 */
	public function getMenu() {
		return $this->hasOne(Menu::class, ['id' => 'menu_id']);
	}
	
	/**
	 * Get item
	 * @return \common\modules\menu\models\query\MenuItemQuery
	 */
	public function getItem() {
		return $this->hasOne(MenuItem::class, ['id' => 'menu_item_id']);
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
	 * @param integer $menuId
	 * @param integer $parentId
	 * @param integer $menuItemId
	 *
	 * @return ActiveRecord|MenuNested
	 */
	static public function findNode($menuId, $parentId, $menuItemId) {
		return self::find()->where('menu_id = :menu_id AND parent_id = :parent_id AND menu_item_id = :menu_item_id', [
			':menu_id' => $menuId,
			':parent_id' => $parentId,
			':menu_item_id' => $menuItemId,
		])->one();
	}
	
	/**
	 * @param integer $menuId
	 * @param bool $root
	 * @param bool $maxLevel
	 * @param bool $cache
	 *
	 * @return array
	 * @throws \Throwable
	 */
	static public function tree($menuId, $root = false, $maxLevel = false, $cache = false) {
		
		// Create query
		$query = self::find()->where(self::tableName().'.menu_id = :menu_id', [
			':menu_id' => $menuId,
		])->joinWith([
			'item',
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
			
			/** @var MenuNested $item */
			if ($item->item) {
				$tree[$item->item->id] = [
					'id' => $item->item->id,
					'nested_id' => $item->id,
					'level' => $item->depth,
					'title' => $item->item->title,
					'descr' => $item->item->descr,
					'url' => $item->item->url,
					'image' => $item->item->image->getImageSrc(50, 50),
					'sequence' => $item->item->sequence,
					'items' => (!$maxLevel || $item->level < $maxLevel) ? static::tree($menuId, $item, $maxLevel) : null,
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