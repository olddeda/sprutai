<?php
namespace common\modules\seo\widgets;

use yii\base\Widget;
use yii\helpers\Html;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\seo\models\Seo;

class SeoFormWidget extends Widget
{
    public $model = null;
	public $module_class;
    public $module_name;
	public $module_type;
	public $module_id;
	
    public $form = null;
	
	/**
	 * @inheritdoc
	 */
    public function init() {
    	
    	// Assign model name
	    $this->module_class = $this->model->className();
	    $this->module_type = $this->model->moduleType;
	    $this->module_id = $this->model->id;
	    $this->module_name = ModuleType::getItem($this->module_type);
        
        parent::init();
    }
	
	/**
	 * @inheritdoc
	 */
    public function run() {
    	
    	// Find or create seo model
	    $seo = Seo::findOne(['module_type' => $this->module_type, 'module_id' => $this->module_id]);
	    if (is_null($seo))
		    $seo = new Seo();

        // Prepare content
        $content = [];
	    $content[] = $this->form->field($seo, 'module_class')->hiddenInput(['value' => $this->module_class])->label(false);
        $content[] = $this->form->field($seo, 'module_type')->hiddenInput(['value' => $this->module_type])->label(false);
	    $content[] = $this->form->field($seo, 'module_id')->hiddenInput(['value' => $this->module_id])->label(false);
	    $content[] = $this->form->field($seo, 'module_name')->hiddenInput(['value' => $this->module_name])->label(false);
	    $content[] = $this->form->field($seo, 'slugify')->textInput(['maxlength' => true]);
        $content[] = $this->form->field($seo, 'title')->textInput(['maxlength' => true]);
        $content[] = $this->form->field($seo, 'description')->textInput(['maxlength' => true]);
        $content[] = $this->form->field($seo, 'keywords')->textInput(['maxlength' => true]);
        //$content[] = $this->form->field($seo, 'h1')->textInput(['maxlength' => true]);
        $content[] = $this->form->field($seo, 'text')->textarea(['rows' => 6]);
        
        // Create view
        $body = Html::tag('div', implode('', $content), ['class' => '', 'id' => 'seo-body']);
        $view = Html::tag('div', $body, ['class' => 'module-seo']);
        
        return $view;
    }
}
