<?php
namespace common\modules\base\widgets\date;

use yii\helpers\Json;

/**
 * Class DateRangePicker
 * @package common\modules\base\widgets
 */
class DateRangePicker extends DateWidget
{
    /**
     * @var array options for js plugin
     */
    public $clientOptions = [];
    
    /**
     * @var array events for js plugin
     */
    public $clientEvents = [];

    /**
     * @inheritdoc
     */
    protected function registerPlugin() {
        DateRangePickerAsset::register($this->view);

        $id = $this->options['id'];
        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $js = "jQuery('#$id').daterangepicker($options)";
        foreach ($this->clientEvents as $event => $handler) {
            $js .= ".on('$event', $handler)";
        }
        $this->view->registerJs($js . ';');
    }
}