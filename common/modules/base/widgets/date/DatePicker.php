<?php
namespace common\modules\base\widgets\date;

use yii\helpers\Json;

/**
 * Class DatePicker
 * @package common\modules\base\widgets
 */
class DatePicker extends DateWidget
{
    /**
     * @var array options for datepicker
     */
    public $clientOptions = [];
    
    /**
     * @var array events for datepicker
     */
    public $clientEvents = [];

    /**
     * @inheritdoc
     */
    protected function registerPlugin() {
        $asset = DatePickerAsset::register($this->view);
        if (isset($this->clientOptions['language'])) {
            $lang = $this->clientOptions['language'];
            $this->view->registerJsFile($asset->baseUrl . "/locales/bootstrap-datepicker.$lang.min.js", [
                'depends' => DatePickerAsset::class,
            ]);
        }

        $id = $this->options['id'];
        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
        $js = "jQuery('#$id').datepicker($options)";
        foreach ($this->clientEvents as $event => $handler) {
            $js .= ".on('$event', $handler)";
        }
        $this->view->registerJs($js . ';');
    }
}