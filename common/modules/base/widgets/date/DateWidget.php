<?php
namespace common\modules\base\widgets\date;

use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class DateWidget
 * @package common\modules\base\widgets
 */
abstract class DateWidget extends InputWidget
{
    /**
     * @var string the template to render the input
     */
    public $template = '{input}';
    
    /**
     * @var array the HTML attributes for the input tag.
     */
    public $options = [
        'class' => 'form-control',
        'autocomplete' => 'off',
    ];
    
    /**
     * @var string|null
     */
    public $icon = 'calendar';

    /**
     * Renders field
     */
    public function run() {
        $this->registerPlugin();

        if (is_string($this->icon)) {
            Html::addCssClass($this->field->options, 'has-feedback');
            $this->template = strtr($this->template, [
                '{input}' => '{input}' . Html::tag('span', '', [
                    'class' => 'form-control-feedback glyphicon glyphicon-' . $this->icon,
                ]),
            ]);
        }

        return strtr($this->template, [
            '{input}' => $this->hasModel()
                ? Html::activeTextInput($this->model, $this->attribute, $this->options)
                : Html::textInput($this->name, $this->value, $this->options),
        ]);
    }

    /**
     * Registers plugin
     */
    abstract protected function registerPlugin();
}