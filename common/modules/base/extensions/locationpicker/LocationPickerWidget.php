<?php
namespace common\modules\base\extensions\locationpicker;

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;
use yii\base\Widget;

/**
 * LocationPickerWidget
 */
class LocationPickerWidget extends Widget
{
    /** @var string google map api key */
    public $key;
    public $options = [];
    public $clientOptions = [];
    public $clientEvents = [];
	
	/**
	 * @inheritdoc
	 */
    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        $url = "//maps.googleapis.com/maps/api/js?" .http_build_query([
            'key'       => $this->key ,
            'libraries' => 'places'
        ]);
        
        $this->view->registerJsFile($url, [
            'position' => View::POS_END
        ]);
        
        LocationPickerAsset::register($this->view);
    }

    /**
     * Copy from jui widget
     * @param string $name
     * @param string $id
     */
    protected function registerClientOptions($name, $id) {
        if ($this->clientOptions !== false) {
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('#$id').$name($options);";
            $this->getView()->registerJs($js);

			$field = $this->clientOptions['inputBinding']['locationNameInput']->expression;

$js = <<<JS
	var input = $field;
	var button = input.parent().find('button');
	var div = jQuery('#$id');
	button.on('click', function() {
	    var isOpen = $(this).hasClass('btn-primary');
	    if (isOpen) {
	        $(this).removeClass('btn-primary');
	        div.slideUp();
	    }
	    else {
	        $(this).addClass('btn-primary');
	        div.slideDown(function() {
	          	div.locationpicker('autosize');
	        });
	    }
	});
JS;
			$this->getView()->registerJs($js);

        }
    }

    /**
     * Copy from jui widget
     * @param string $name
     * @param string $id
     */
    protected function registerClientEvents($name, $id) {
        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                if (isset($this->clientEventMap[$event])) {
                    $eventName = $this->clientEventMap[$event];
                } else {
                    $eventName = strtolower($name . $event);
                }
                $js[] = "jQuery('#$id').on('$eventName', $handler);";
            }
            $this->getView()->registerJs(implode("\n", $js));
        }
    }
	
	/**
	 * @inheritdoc
	 */
    public function run() {
        echo Html::tag('div','', $this->options);
        $this->registerClientOptions('locationpicker', $this->getId());
        $this->registerClientEvents('locationpicker', $this->getId());
    }
}
