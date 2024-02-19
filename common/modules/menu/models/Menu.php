<?php
namespace common\modules\menu\models;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\seo\behaviors\SeoFields;

use common\modules\user\models\User;

use common\modules\menu\models\query\MenuQuery;
use common\modules\menu\helpers\enum\Type;

use common\modules\tag\models\Tag;
use common\modules\tag\models\TagNested;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $tag_id
 * @property string $title
 * @property integer $sequence
 * @property bool $visible
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 * @property array $tree
 *
 * Defined relations:
 * @property \common\modules\tag\models\tag $tag
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class Menu extends ActiveRecord
{
	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::MENU;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%menu}}';
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => SeoFields::class,
			],
		]);
	}

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['type', 'title', 'tag_id', 'status'], 'required'],
            [['type', 'tag_id', 'sequence', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['visible'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('menu', 'field_id'),
			'type' => Yii::t('menu', 'field_type'),
			'tag_id' => Yii::t('menu', 'field_tag_id'),
            'title' => Yii::t('menu', 'field_title'),
            'sequence' => Yii::t('menu', 'field_sequence'),
			'visible' => Yii::t('menu', 'field_visible'),
            'status' => Yii::t('menu', 'field_status'),
            'created_at' => Yii::t('menu', 'field_created_at'),
            'updated_at' => Yii::t('menu', 'field_updated_at'),
        ];
    }
	
	/**
	 * @inheritdoc
	 * @return \common\modules\menu\models\query\MenuQuery the active query used by this AR class.
	 */
	public static function find() {
		return new MenuQuery(get_called_class());
	}
	
	/**
	 * Get tag
	 * @return \common\modules\tag\models\query\TagQuery
	 */
	public function getTag() {
		return $this->hasOne(Tag::class, ['id' => 'tag_id']);
	}
	
	/**
	 * Get nested tag
	 * @return \common\modules\tag\models\query\TagNestedQuery
	 */
	public function getNested() {
		return $this->type == Type::TAG ? $this->getNestedTag() : $this->getNestedItem();
	}
	
	/**
	 * Get nested tag
	 * @return \common\modules\tag\models\query\TagNestedQuery
	 */
	public function getNestedTag() {
		return $this->hasOne(TagNested::class, ['tag_id' => 'tag_id'])->andOnCondition([
			'module_type' => $this->getModuleType(),
			'module_id' => $this->id,
			'depth' => 0,
		]);
	}
	
	/**
	 * Get nested tag
	 * @return \common\modules\tag\models\query\TagNestedQuery
	 */
	public function getNestedItem() {
		return $this->hasOne(MenuNested::class, ['menu_id' => 'id'])->andOnCondition([
			'depth' => 0,
		]);
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
	 * Get tree
	 * @return array
	 * @throws \Throwable
	 */
	public function getTree() {
		return ($this->type == Type::TAG) ? TagNested::tree(Menu::moduleType(), $this->id) : MenuNested::tree($this->id);
	}
	
	/**
	 * @return null|string
	 * @throws \ReflectionException
	 */
	public function getUriModuleName() {
		return 'menus';
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeValidate() {
		if ($this->type == Type::TAG) {
			$this->title = ($this->tag) ? $this->tag->title : null;
		}
		
		return parent::beforeValidate();
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		
		// Set sequence
		if (!$this->sequence) {
			$this->sequence = self::lastSequence();
		}
		
		return parent::beforeSave($insert);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		if ($this->status != Status::DELETED) {
			if ($this->type == Type::TAG) {
				
				$model = TagNested::find()->where([
					'module_type' => $this->getModuleType(),
					'module_id' => $this->id,
					'parent_id' => 0,
				])->one();
				
				if (is_null($model)) {
					$model = new TagNested();
					$model->module_type = $this->getModuleType();
					$model->module_id = $this->id;
					$model->parent_id = 0;
					$model->makeRoot();
				}
				
				$model->tag_id = $this->tag_id;
				$model->save();
				
			}
			else {
				$modelItem = MenuItem::find()->where([
					'title' => $this->title
				])->one();
				
				if (is_null($modelItem)) {
					$modelItem = new MenuItem();
					$modelItem->menu_id = $this->id;
					$modelItem->title = $this->title;
					$modelItem->status = Status::ENABLED;
					$modelItem->save(false);
				}
				
				$modelNested = MenuNested::find()->where([
					'menu_id' => $this->id,
					'parent_id' => 0,
				])->one();
				
				if (is_null($modelNested)) {
					$modelNested = new MenuNested();
					$modelNested->menu_id = $this->id;
					$modelNested->parent_id = 0;
					$modelNested->makeRoot();
				}
				
				$modelNested->menu_item_id = $modelItem->id;
				$modelNested->save();
			}
		}
	}
}
