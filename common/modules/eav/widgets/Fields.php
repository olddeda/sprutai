<?php
namespace common\modules\eav\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

use common\modules\eav\models\EavAttribute;

/**
 * Class Fields
 * @package common\modules\eav\widgets
 */
class Fields extends Widget
{
	/**
	 * @var array
	 */
    public $url = ['/eav/ajax/index'];
	
	/**
	 * @var array
	 */
    public $urlSave = ['/eav/ajax/save'];
	
	/**
	 * @var
	 */
    public $model;
	
	/**
	 * @var int
	 */
    public $categoryId = 0;
	
	/**
	 * @var
	 */
    public $entityModel;
	
	/**
	 * @var string
	 */
    public $entityName = 'Untitled';
	
	/**
	 * @var array
	 */
    public $options = [];
	
	/**
	 * @var array
	 */
    private $bootstrapData = [];
	
	/**
	 * @var array
	 */
    private $rules = [];
	
	/**
	 * @inheritdoc
	 */
    public function init() {
        parent::init();

        $this->url = Url::toRoute($this->url);

        $this->urlSave = Url::toRoute($this->urlSave);

        $this->entityModel = str_replace('\\', '\\\\', $this->entityModel);

        /** @var EavAttribute $attribute */
        foreach ($this->model->getEavAttributes()->all() as $attribute) {

            $options = ArrayHelper::merge([
                'description' => $attribute->description,
                'required' => (bool)$attribute->required,
            ], is_null($attribute->eavAttributeRule->rules)? []: json_decode($attribute->eavAttributeRule->rules));

            foreach ($attribute->eavOptions as $option) {
                $options['options'][] = [
                    'label' => $option->value,
                    'id' => $option->id,
                    'checked' => (bool)$option->defaultOptionId,
                ];
            }

            $this->bootstrapData[] = [
                'group_name' => $attribute->type,
                'label' => $attribute->label,
                'field_type' => $attribute->eavType->name,
                'field_options' => $options,
                'cid' => $attribute->name,
            ];

        }

        $this->bootstrapData = Json::encode($this->bootstrapData);
    }
	
	/**
	 * @return string
	 */
    public function run() {
        return $this->render('fields', [
            'url' => $this->url,
            'urlSave' => $this->urlSave,
            'categoryId' => isset($this->categoryId) ? $this->categoryId : 0,
            'entityModel' => $this->entityModel,
            'entityName' => $this->entityName,
            'bootstrapData' => $this->bootstrapData,
        ]);
    }
}
