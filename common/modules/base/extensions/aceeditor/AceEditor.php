<?php
namespace common\modules\base\extensions\aceeditor;

use common\modules\base\components\Debug;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\widgets\InputWidget;

/**
 * Class AceEditor
 * @package common\modules\base\extensions\aceeditor
 */
class AceEditor extends InputWidget
{   
     /**
     * @var boolean Read-only mode on/off (false=off - default)
     */
    public $readOnly = false;
    
    /**
     * @var string Programming Language Mode
     */
    public $mode = 'html';

    /**
     * @var string Editor theme
     * $see Themes List
     * @link https://github.com/ajaxorg/ace/tree/master/lib/ace/theme
     */
    public $theme = 'chrome';

    /**
     * @var array Div options
     */
    public $containerOptions = [
        'style' => 'width: 100%;',
    ];
    
    /**
	 * @var array
	 */
    public $pluginOptions = [];
	
	/**
	 * @var array
	 */
    private $defaultPluginOptions = [
		'wrap' => true,
		'enableBasicAutocompletion' => true,
		'enableSnippets' =>  true,
		'enableLiveAutocompletion' => true,
		'minLines' => 1,
		'maxLines' => 100,
	];

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        
        AceEditorAsset::register($this->getView());
        
        $editor_id = $this->getId();
        $editor_var = 'aceeditor_' . $editor_id;
        
		$pluginOptionsJson = Json::encode(ArrayHelper::merge($this->defaultPluginOptions, $this->pluginOptions));
        
        $this->getView()->registerJs("var {$editor_var} = ace.edit(\"{$editor_id}\")");
        $this->getView()->registerJs("{$editor_var}.setTheme(\"ace/theme/{$this->theme}\")");
        $this->getView()->registerJs("{$editor_var}.getSession().setMode({path:\"ace/mode/{$this->mode}\", inline:true})");
        $this->getView()->registerJs("{$editor_var}.setReadOnly({$this->readOnly})");
        $this->getView()->registerJs("{$editor_var}.setOptions({$pluginOptionsJson})");

        $textarea_var = 'acetextarea_' . $editor_id;
        
        $this->getView()->registerJs("
            var {$textarea_var} = $('#{$this->options['id']}').hide();
            {$editor_var}.getSession().setValue({$textarea_var}.val());
            {$editor_var}.getSession().on('change', function(){
                {$textarea_var}.val({$editor_var}.getSession().getValue());
            });
        ");
        
        Html::addCssStyle($this->options, 'display: none');
        
        $this->containerOptions['id'] = $editor_id;
        
        $this->getView()->registerCss("#{$editor_id}{position:relative}");
    }

    /**
     * @inheritdoc
     */
    public function run() {
        $content = Html::tag('div', '', $this->containerOptions);
        if ($this->hasModel()) {
            $content .= Html::activeTextarea($this->model, $this->attribute, $this->options);
        }
        else {
            $content .= Html::textarea($this->name, $this->value, $this->options);
        }
        
        $content .= Html::tag('div', '', ['class' => 'ace_scroll']);
        
        return $content;
    }
}
