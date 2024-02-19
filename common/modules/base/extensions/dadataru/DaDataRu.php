<?php
namespace common\modules\base\extensions\dadataru;

use common\modules\base\components\Debug;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

use yii\widgets\InputWidget;

/**
 * Class DaDataRu
 * @package common\modules\base\extensions\dadataru
 */
class DaDataRu extends InputWidget
{
	const TYPE_ADDRESS  = 'ADDRESS';
	
	/**
	 * @var string
	 */
	public $token;
	
	/**
	 * @var string
	 */
	public $type;
	
	/**
	 * @var integer
	 */
	public $count = 5;
	
	/**
	 * @var array plugin options
	 */
	public $pluginOptions = [];
	
	/**
	 * @var array default plugin options
	 */
	public $defaultPluginOptions = [
		'constraints' => [
			'locations' => [
				'country' => '*',
			],
		],
	];
	
	/**
	 * Render widget
	 * @return string|void
	 * @throws InvalidConfigException
	 */
	public function run() {
		if (is_null($this->token))
			throw new InvalidConfigException('Need set token');
		
		if (is_null($this->type))
			throw new InvalidConfigException('Need set type');
		
		$this->renderInput();
		$this->registerAssets();
	}
	
	/**
	 * Register client assets
	 */
	protected function registerAssets() {
		$view = $this->getView();
		DaDataRuAsset::register($view);
		
		$js = '$("#'.$this->getInputId().'").suggestions('.$this->getPluginOptions().');';
		$view->registerJs($js, $view::POS_END);
	}
	
	/**
	 * Return plugin options in json format
	 * @return string
	 */
	public function getPluginOptions() {
		$pluginOptions = ArrayHelper::merge($this->defaultPluginOptions, $this->pluginOptions);
		$pluginOptions['token'] = $this->token;
		$pluginOptions['type'] = $this->type;
		$pluginOptions['count'] = $this->count;
		
		return Json::encode($pluginOptions);
	}
	
	/**
	 * Return input id
	 */
	public function getInputId() {
		return $this->options['id'];
	}
	
	/**
	 * Render text input
	 */
	public function renderInput() {
		if ($this->hasModel()) {
			echo Html::activeTextInput($this->model, $this->attribute, $this->options);
		}
		else {
			echo Html::textInput($this->name, $this->value, $this->options);
		}
	}
}