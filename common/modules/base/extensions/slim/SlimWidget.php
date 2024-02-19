<?php
namespace common\modules\base\extensions\slim;

use common\modules\base\components\Debug;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 * Class SlimWidget
 * @package common\modules\base\extensions\slim
 */
class SlimWidget extends InputWidget
{
	/** @var array */
	public $settings = [];
	
	/** @var array */
	public $clientOptions = [];
	
	/** @var array */
	public $pluginOptions = [];
	
	public function init() {
		parent::init();
		
		$this->clientOptions = ArrayHelper::merge($this->clientOptions, ['id' => $this->getId()]);
	}
	
	/**
	 * @return string
	 */
	public function run() {
		$this->registerAsset();
		$this->registerClientScript();
		
		$content = null;
		if ($this->hasModel()) {
			if ($this->model->{$this->attribute}) {
				$content[] = Html::img($this->model->{$this->attribute}, ['class' => 'slim-img']);
			}
			else if ($this->value) {
				$content[] = Html::img($this->value, ['class' => 'slim-img']);
			}
			
			$content[] = Html::fileInput('slim[]', $this->model->{$this->attribute}, $this->options);
		}
		else {
			if ($this->value) {
				$content[] = Html::img($this->value, ['class' => 'slim-img']);
			}
			$content[] = Html::fileInput('slim[]', $this->value, $this->options);
		}
		
		return Html::tag('div', implode(PHP_EOL, $content), $this->clientOptions);
	}
	
	/**
	 * @inheritdoc
	 */
	protected function registerAsset() {
		SlimAsset::register($this->getView());
	}
	
	/**
	 * @inheritdoc
	 */
	protected function registerClientScript() {
		$settings = Json::encode($this->settings);
		$this->getView()->registerJs(new JsExpression('jQuery("#' . $this->getId() . '").slim('.$settings.');'));
	}
}
