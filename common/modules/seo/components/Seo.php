<?php
namespace common\modules\seo\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

use common\modules\base\components\Debug;

use common\modules\seo\models\Seo as SeoModel;

class Seo extends Component {
	
	private $_route;
	private $_route_params;
	
	private $_metaTags = [];
	
	private $_model;
	private $_owner;
	
	/**
	 * @inheritdoc
	 */
	public function init() {
		parent::init();
	}
	
	/**
	 * @inheritdoc
	 */
	public function run() {
		$this->setRoute(Yii::$app->controller->route);
		$this->setRouteParams(Yii::$app->request->queryParams);
		$this->generateData();
		$this->setMeta();
	}
	
	/**
	 * Set route
	 *
	 * @param $route
	 */
	public function setRoute($route) {
		$this->_route = $route;
	}
	
	/**
	 * Set route params
	 *
	 * @param array $params
	 */
	public function setRouteParams($params = []) {
		$tmp = $params;
		if (is_array($tmp)) {
			foreach ($tmp as $key => $value) {
				if (!in_array($key, ['id', 'slug']) || is_null($value) || $value == '') {
					unset($tmp[$key]);
				}
			}
		}
		$this->_route_params = (is_array($tmp)) ? $tmp : [];
	}
	
	/**
	 * Generate data
	 */
	public function generateData() {
		$condition = [
			'route' => $this->_route,
			'route_params' => Json::encode($this->_route_params),
		];
		
		$this->_model = SeoModel::find()->where($condition)->one();
		if (is_null($this->_model)) {
			$this->_model = new SeoModel();
			$this->_model->route = $this->_route;
			$this->_model->route_params = Json::encode($this->_route_params);
			//$this->_model->save();
		}
		
		// Find owner
		if ($this->_model->model_name && $this->_model->model_id) {
			$obj = Yii::createObject($this->_model->model_name);
			$this->_owner = $obj->findById($this->_model->model_id);
		}
	}
	
	public function setMeta() {
		if ($this->_model) {
			if ($this->_owner)
				Yii::$app->view->title = $this->_owner->title;
			
			Yii::$app->view->seo_title = $this->_model->title;
			Yii::$app->view->seo_h1 = $this->_model->h1;
			Yii::$app->view->seo_keywords = $this->_model->keywords;
			Yii::$app->view->seo_description = $this->_model->description;
			Yii::$app->view->seo_text = $this->_model->text;
			
			Yii::$app->view->registerMetaTag(['name' => 'keywords', 'content' => $this->_model->keywords]);
			Yii::$app->view->registerMetaTag(['name' => 'description', 'content' => $this->_model->description]);
		}
	}
}