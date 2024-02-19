<?php
namespace common\modules\base\extensions\gtm\widget;

use Yii;

use yii\base\Widget;

class GTMWidget extends Widget
{
	const PARAM_ENV = 'gtm_env';
	const PARAM_ID = 'gtm_id';
	
	const TYPE_SCRIPT = 'script';
	const TYPE_NOSCRIPT = 'noscript';
	const TYPE_PUSH = 'dataLayerPush';
	
	const SESSION_KEY = 'gtm-data-layer-push';
	
	public $type = '';
	
	public function init() {
		parent::init();
		if ($this->type != self::TYPE_SCRIPT && $this->type != self::TYPE_NOSCRIPT && $this->type != self::TYPE_PUSH) {
			$this->type = self::TYPE_SCRIPT;
		}
	}
	
	public function run() {
		if ($this->type == self::TYPE_PUSH)
			return $this->runPush();
		
		$params = $this->getParams();
		if ($this->paramMissing($params))
			return '';
		
		return $this->render($this->type, $params);
	}
	
	private function runPush(): String {
		$session = Yii::$app->getSession();
		$dataLayerPushItems = $session->get(self::SESSION_KEY) ?? [];
		
		if (empty($dataLayerPushItems)) return '';
		
		$session->remove(self::SESSION_KEY);
		return $this->render($this->type, ['dataLayerPushItems' => $dataLayerPushItems]);
	}
	
	private function paramMissing(array $params): bool {
		return empty($params[self::PARAM_ID]);
	}
	
	private function getParams(): array {
		return [
			self::PARAM_ENV => Yii::$app->params[self::PARAM_ENV] ?? '',
			self::PARAM_ID => Yii::$app->params[self::PARAM_ID] ?? ''
		];
	}
}
