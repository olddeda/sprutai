<?php
namespace common\modules\base\widgets\date;

use yii\helpers\Json;

/**
 * Class DateTimePicker
 * @package common\modules\base\widgets
 */
class DateTimePicker extends DateWidget
{
    /**
     * @var array options for datetimepicker
     */
    public $clientOptions = [];
    
    /**
     * @var array events for datetimepicker
     */
    public $clientEvents = [];
    
    /**
     * @inheritdoc
     */
    protected function registerPlugin() {
        $asset = DateTimePickerAsset::register($this->view);
        if (isset($this->clientOptions['language'])) {
            $lang = $this->clientOptions['language'];
            $this->view->registerJsFile($asset->baseUrl . "/js/locales/bootstrap-datetimepicker.$lang.js", [
                'depends' => DateTimePickerAsset::class,
            ]);
        }

        $id = $this->options['id'];
        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $js = "jQuery('#$id').datetimepicker($options)";
        foreach ($this->clientEvents as $event => $handler) {
            $js .= ".on('$event', $handler)";
        }
        $this->view->registerJs($js . ';');
    }
}