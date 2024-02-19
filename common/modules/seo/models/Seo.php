<?php
namespace common\modules\seo\models;

use common\modules\base\components\ActiveRecord;
use common\modules\seo\models\query\SeoQuery;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%seo}}".
 *
 * @property integer $id
 * @property string $module_name
 * @property string $module_class
 * @property integer $module_type
 * @property integer $module_id
 * @property string $slugify
 * @property string $h1
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $text
 * @property int status
 * @property integer $created_at
 * @property integer $updated_at
 */
class Seo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%seo}}';
    }
	
	/**
	 * @return array
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			'slug' => [
				'class' => 'common\modules\base\behaviors\SlugifyBehavior',
				'slugAttribute' => 'slugify',
				'attribute' => 'slugify_title',
				'uniqueValidator' => ['targetAttribute' => ['module_type', 'slugify']],
			]
		]);
	}
	
	/**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['module_type', 'module_id', 'created_at', 'updated_at'], 'integer'],
			[['module_name'], 'string', 'max' => 255],
            [['slugify', 'h1', 'title', 'keywords'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 522],
			[['slugify'], 'filter', 'filter' => 'strtolower'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('seo', 'field_'),
	        'module_class' => Yii::t('seo', 'field_module_class'),
	        'module_name' => Yii::t('seo', 'field_module_name'),
	        'module_type' => Yii::t('seo', 'field_module_type'),
            'module_id' => Yii::t('seo', 'field_module_id'),
	        'slugify' => Yii::t('seo', 'field_slugify'),
            'h1' => Yii::t('seo', 'field_h1'),
            'title' => Yii::t('seo', 'field_title'),
            'keywords' => Yii::t('seo', 'field_keywords'),
            'description' => Yii::t('seo', 'field_description'),
            'text' => Yii::t('seo', 'field_text'),
			'created_at' => Yii::t('seo', 'field_created_at'),
			'updated_at' => Yii::t('seo', 'field_updated_at'),
        ];
    }
	
	/**
	 * @inheritdoc
	 * @return \common\modules\seo\models\query\SeoQuery the active query used by this AR class.
	 */
	public static function find() {
		return new SeoQuery(get_called_class());
	}
	
	/**
	 * Get module
	 * @return \yii\db\ActiveQuery
	 */
	public function getModule() {
		return $this->hasOne($this->module_class, ['id' => 'module_id'])->where([]);
	}
	
	/**
	 * Get root model by class name
	 * @param string $className
	 *
	 * @return $this|Seo
	 */
	static public function getRoot($className) {
		$model = self::find()->where('module_class = :module_class AND module_id = :module_id', [
			':module_class' => $className,
			':module_id' => 0,
		])->one();
		
		if ($model === null)
			$model = new Seo;
		if (!strlen($model->title))
			$model->title = null;
		if (!strlen($model->description))
			$model->description = null;
		if (!strlen($model->keywords))
			$model->keywords = null;
		if (!strlen($model->text))
			$model->text = null;
		if (!strlen($model->h1))
			$model->h1 = null;
		return $model;
	}

    /**
     * @return mixed
     */
	public function getSlugify_title() {
	    if ($this->module->hasMethod('getSlugify_title')) {
            return $this->module->getSlugify_title();
        }
	    if ($this->module->getAttribute('title')) {
            return $this->module->getAttribute('title');
        }
	    return $this->title;
    }

    /**
	 * @param bool $insert
	 * @param array $changedAttributes
	 */
	public function afterSave($insert, $changedAttributes) {
		parent::afterSave($insert, $changedAttributes);
		
		// Set seo uri
		if ($this->slugify && strlen($this->slugify)) {
			$moduleName = lcfirst($this->module->moduleClass);
			
			if (method_exists($this->module, 'getUriModuleName'))
				$moduleName = $this->module->getUriModuleName();
			
			$moduleRoute = $moduleName.'/'.($this->module_id ? 'view' : 'index');
			$moduleParams = $this->module->getUriParams();
			
			$uri = $this->module->getUri();
			
			SeoUri::saveUri($this->module_type, $this->module_id, $moduleRoute, $moduleParams, $uri);
		}
	}
}