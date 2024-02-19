<?php
namespace common\modules\dashboard\components;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

use appmake\yii2\bootstrap\Panel;

use common\modules\base\helpers\enum\Status;

use common\modules\dashboard\models\Dashboard;

class DashboardWidget extends Widget
{
	/**
	 * @var \fedemotta\gridstack\Gridster;
	 */
	public $gridstack;
	
	/**
	 * @var \common\modules\dashboard\models\Dashboard;
	 */
	private $_model;
	
	/**
	 * DashboardWidget constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config = []) {
		
		// Check and set gridster
		if (!isset($config['gridstack']))
			throw new InvalidConfigException('The "gridstack" propery must be set.');
		
		parent::__construct($config);
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		throw new InvalidConfigException('The "getName()" method must be set.');
	}
	
	/**
	 * @return string
	 */
	public function getTitle() {
		throw new InvalidConfigException('The "getTitle()" property must be set.');
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
	
	public function run() {
		$this->_loadOrCreateModel();
		
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
		
		Panel::begin([
			'type' => Panel::TYPE_DEFAULT,
			'header' => $this->getTitle(),
		]);
		
		echo $this->render($this->getName());
		
		Panel::end();
		
		echo Html::endTag('div');
		
		echo $this->gridstack->endWidget();
	}
	
	/**
	 * @param string $name
	 * @param integer $default
	 */
	private function _getParam($name, $default) {
		if ($this->_model)
			return $this->_model->{$name};
		die;
		
		$methodName = 'getDefault'.ucfirst($name);
		$class = get_called_class();
		if (method_exists($class, $methodName))
			return call_user_func(array($class, $methodName));
		
		return $default;
	}
	
	private function _loadOrCreateModel() {
		$this->_model = Dashboard::find()->andWhere(['name' => $this->getName()])->one();
		if (is_null($this->_model)) {
			$this->_model = new Dashboard();
			$this->_model->user_id = Yii::$app->user->id;
			$this->_model->name = $this->getName();
			$this->_model->width = $this->getWidth();
			$this->_model->height = $this->getHeight();
			$this->_model->x = $this->getX();
			$this->_model->y = $this->getY();
			$this->_model->status = Status::ENABLED;
			$this->_model->save();
		}
	}
	
	public function render($view, $params = []) {
		return parent::render($view, $params);
	}
}