<?php
namespace common\modules\dashboard\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\base\InvalidConfigException;


use common\modules\base\components\Debug;
use common\modules\base\extensions\bootstrap\Panel;
use common\modules\base\extensions\gridstack\Gridstack;

use common\modules\base\helpers\enum\Status;

use common\modules\dashboard\models\Dashboard;

class DashboardWidget extends Widget
{
	/**
	 * @var Gridstack
	 */
	public $gridstack;
	
	/**
	 * @var \common\modules\dashboard\models\Dashboard
	 */
	private $_model;
	
	public function init() {
		parent::init();
		
		$this->_loadOrCreateModel();
	}
	
	/**
	 * DashboardWidget constructor.
	 *
	 * @param array $config
	 *
	 * @throws InvalidConfigException
	 */
	public function __construct(array $config = []) {
		
		if (!isset($config['gridstack']))
			throw new InvalidConfigException('The "gridstack" propery must be set.');
		
		parent::__construct($config);
	}
	
	/**
	 * @throws InvalidConfigException
	 */
	public function getName() {
		throw new InvalidConfigException('The "getName()" method must be set.');
	}
	
	/**
	 * @throws InvalidConfigException
	 */
	public function getTitle() {
		return null;
	}
	
	/**
	 * @return int
	 */
	public function getX() {
		return $this->_getParam('x', 0);
	}
	
	/**
	 * @return int
	 */
	public function getY() {
		return $this->_getParam('y', 0);
	}
	
	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->_getParam('width', 4);
	}
	
	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->_getParam('height', 4);
	}
	
	/**
	 * @return null|int
	 */
	public function getMinWidth() {
		return null;
	}
	
	/**
	 * @return null|int
	 */
	public function getMinHeight() {
		return null;
	}
	
	/**
	 * @return null|int
	 */
	public function getMaxWidth() {
		return null;
	}
	
	/**
	 * @return null|int
	 */
	public function getMaxHeight() {
		return null;
	}
	
	/**
	 * @return null|mixed
	 */
	public function getPanelHeader() {
		return null;
	}
	
	/**
	 * @return bool
	 */
	public function getHasBody() {
		return true;
	}
	
	public function run() {
		$options = [
			'class '=> 'grid-stack-item',
			'data-gs-width' => $this->getWidth(),
			'data-gs-height'=> $this->getHeight(),
			'data-gs-x' => $this->getX(),
			'data-gs-y' => $this->getY(),
		];
		
		if (!is_null($this->getMinWidth()))
			$options['data-gs-min-width'] = $this->getMinWidth();
		if (!is_null($this->getMinHeight()))
			$options['data-gs-min-height'] = $this->getMinHeight();
		if (!is_null($this->getMaxWidth()))
			$options['data-gs-max-width'] = $this->getMaxWidth();
		if (!is_null($this->getMaxHeight()))
			$options['data-gs-max-height'] = $this->getMaxHeight();
		
		echo $this->gridstack->beginWidget($options);
		
		echo Html::beginTag('div', ['class' => 'grid-stack-item-content', 'data-name' => $this->getName()]);
		
		$header = $this->getPanelHeader();
		$panelHeader = null;
		if ($header)
			$panelHeader = $header;
		else if ($this->getTitle())
			$panelHeader = Html::tag('div', $this->getTitle(), ['class' => 'panel-title']);
		Panel::begin([
			'type' => Panel::TYPE_DEFAULT,
			'header' => $panelHeader,
			'hasBody' => $this->getHasBody(),
		]);
		
		echo $this->render($this->getName());
		
		Panel::end();
		
		echo Html::endTag('div');
		
		echo $this->gridstack->endWidget();
	}
	
	/**
	 * @param $name
	 * @param $default
	 *
	 * @return mixed
	 */
	private function _getParam($name, $default) {
		if ($this->_model)
			return $this->_model->{$name};
		
		$methodName = 'getDefault'.ucfirst($name);
		$class = get_called_class();
		if (method_exists($class, $methodName))
			return call_user_func([$this, $methodName]);
		
		return $default;
	}
	
	private function _loadOrCreateModel() {
		$this->_model = Dashboard::find()->andWhere([
			'name' => $this->getName(),
			'user_id' => Yii::$app->user->id,
		])->one();
		if (is_null($this->_model)) {
			$model = new Dashboard();
			$model->user_id = Yii::$app->user->id;
			$model->name = $this->getName();
			$model->width = $this->getWidth();
			$model->height = $this->getHeight();
			$model->x = $this->getX();
			$model->y = $this->getY();
			$model->status = Status::ENABLED;
			$model->save();
			$this->_model = $model;
		}
	}
	
	/**
	 * Get param
	 * @param $name
	 * @param null $default
	 *
	 * @return mixed|null
	 */
	public function getParam($name, $default = null) {
		$value = $this->_model->getParamsValue($name);
		return !is_null($value) ? $value : $default;
	}
	
	/**
	 * @param $name
	 * @param $value
	 */
	public function setParam($name, $value) {
		$this->_model->setParamsValue($name, $value);
		$this->_model->save();
	}
}