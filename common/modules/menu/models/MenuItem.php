<?php
namespace common\modules\menu\models;

use Yii;
use yii\helpers\ArrayHelper;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\base\components\Debug;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;

use common\modules\user\models\User;

use common\modules\menu\models\query\MenuItemQuery;

/**
 * This is the model class for table "{{%menu_item}}".
 *
 * @property integer $id
 * @property integer $menu_id
 * @property string $title
 * @property string $descr
 * @property string $url
 * @property integer $sequence
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property \common\modules\menu\models\Menu $menu
 * @property \common\modules\user\models\User $createdBy
 * @property \common\modules\user\models\User $updatedBy
 */
class MenuItem extends ActiveRecord
{
	/**
	 * Get module type
	 * @return int
	 */
	public static function moduleType() {
		return ModuleType::MENU_ITEM;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%menu_item}}';
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'image',
				'type' => MediaType::IMAGE,
			],
		]);
	}

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'sequence', 'status'], 'required'],
            [['sequence', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['title', 'url'], 'string', 'max' => 255],
			[['descr'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('menu-item', 'field_id'),
			'menu_id' => Yii::t('menu-item', 'field_menu_id'),
            'title' => Yii::t('menu-item', 'field_title'),
			'descr' => Yii::t('menu-item', 'field_descr'),
			'url' => Yii::t('menu-item', 'field_url'),
			'sequence' => Yii::t('menu-item', 'field_sequence'),
            'status' => Yii::t('menu-item', 'field_status'),
            'created_at' => Yii::t('menu-item', 'field_created_at'),
            'updated_at' => Yii::t('menu-item', 'field_updated_at'),
        ];
    }
	
	/**
	 * @inheritdoc
	 * @return \common\modules\menu\models\query\MenuItemQuery the active query used by this AR class.
	 */
	public static function find() {
		return new MenuItemQuery(get_called_class());
	}
	
	/**
	 * Get tag
	 * @return \common\modules\menu\models\query\MenuQuery
	 */
	public function getMenu() {
		return $this->hasOne(Menu::class, ['id' => 'tag_id']);
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
	 * @return null|string
	 * @throws \ReflectionException
	 */
	public function getUriModuleName() {
		return 'menu-items';
	}
	
	/**
	 * @param bool $useStatus
	 *
	 * @return false|int|void
	 */
	public function delete($useStatus = true) {
		parent::delete($useStatus);
		
		MenuNested::deleteAll([
			'menu_id' => $this->menu_id,
			'menu_item_id' => $this->id,
		]);
	}
}
