<?php
namespace common\modules\seo\models;

use Yii;
use yii\db\Query;

use common\modules\base\components\ActiveRecord;
use common\modules\base\components\Debug;

use common\modules\seo\models\query\SeoModuleQuery;

/**
 * This is the model class for table "{{%seo_module}}".
 *
 * @property int $id
 * @property int $module_type
 * @property string $module_class
 * @property string $slugify
 * @property int $status
 * @property int $created_by
 * @property int $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class SeoModule extends ActiveRecord
{
	private $_slugify_old;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return '{{%seo_module}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['module_type', 'module_class'], 'required'],
			[['module_type', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['module_class', 'slugify'], 'string', 'max' => 255],
			[['slugify'], 'filter', 'filter'=>'strtolower'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('seo-module', 'field_id'),
			'module_type' => Yii::t('seo-module', 'field_module_type'),
			'module_class' => Yii::t('seo-module', 'field_module_class'),
			'slugify' => Yii::t('seo-module', 'field_slugify'),
			'status' => Yii::t('seo-module', 'field_status'),
			'created_by' => Yii::t('seo-module', 'field_created_by'),
			'updated_by' => Yii::t('seo-module', 'field_updated_by'),
			'created_at' => Yii::t('seo-module', 'field_created_at'),
			'updated_at' => Yii::t('seo-module', 'field_updated_at'),
		];
	}
	
	/**
	 * @inheritdoc
	 * @return \common\modules\seo\models\query\SeoModuleQuery the active query used by this AR class.
	 */
	public static function find() {
		return new SeoModuleQuery(get_called_class());
	}
	
	public function afterFind() {
		parent::afterFind();
		
		$this->_slugify_old = $this->slugify;
	}
	
	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert) {
		
		// Format slugify
		$this->slugify = str_replace('/', '', $this->slugify);
		
		return parent::beforeSave($insert);
	}
	
	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		// Set seo uri
		$moduleClass = $this->module_class;
		$moduleClassShort = (new \ReflectionClass($moduleClass))->getShortName();
		$moduleRoute = strtolower($moduleClassShort).'/index';
		SeoUri::saveUri($this->module_type, 0, $moduleRoute, $moduleClass::rootUriParams(), $this->slugify);
	}
}