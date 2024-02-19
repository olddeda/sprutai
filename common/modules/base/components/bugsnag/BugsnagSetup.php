<?php
namespace common\modules\base\components\bugsnag;

use Exception;

use yii\base\Component;

class BugsnagSetup extends Component
{
	/**
	 * @var string
	 */
    public $apiKey;
	
	/**
	 * @var string|null
	 */
	public $endpoint;
	
	/**
	 * @var string|null
	 */
    public $stage;
    
	/**
	 * @var bool
	 */
    public $sendWarnings = false;
	
	/**
	 * @var bool
	 */
    public $autoCaptureSessions = true;
	
	/**
	 * @var array
	 */
    public $except = [
		'yii\web\HttpException:404',
	];
	
	/**
	 * @var \common\modules\user\components\User
	 */
    public $identityClass;

    public function init() {
        if (empty($this->apiKey)) {
            throw new Exception('API Key required!');
        }

        if (empty($this->identityClass)) {
            throw new Exception('Identity class required!');
        }
    }
	
	/**
	 * @param array $yiiConfig
	 */
    private function attachComponent(&$yiiConfig) {
        $config = [
            'class' => BugsnagComponent::class,
            'apiKey' => $this->apiKey,
            'stage' => $this->stage,
            'endpoint' => $this->endpoint,
            'sendWarnings' => $this->sendWarnings,
			'autoCaptureSessions' => $this->autoCaptureSessions,
            'identityClass' => $this->identityClass,
        ];

        $yiiConfig['components']['bugsnag'] = $config;
    }
	
	/**
	 * @param array $yiiConfig
	 */
    private function attachWebErrorHandler(&$yiiConfig) {
        $yiiConfig['components']['errorHandler']['class'] = BugsnagWebErrorHandler::class;
    }
	
	/**
	 * @param array $yiiConfig
	 */
    private function attachConsoleErrorHandler(&$yiiConfig) {
        $yiiConfig['components']['errorHandler']['class'] = BugsnagConsoleErrorHandler::class;
    }
	
	/**
	 * @param array $yiiConfig
	 */
    private function attachLogTarget(&$yiiConfig) {
        $target = [
            'class' => BugsnagLogTarget::class,
            'levels' => ['error', 'warning', 'info', 'trace'],
            'logVars' => [],
            'except' => $this->except
        ];

        if (!isset($yiiConfig['components']['log']['targets'])) {
            $yiiConfig['components']['log']['targets'] = [];
        }

        $yiiConfig['components']['log']['targets'][] = $target;
    }
	
	/**
	 * @param array $yiiConfig
	 */
    public function attachForWeb(&$yiiConfig) {
        $this->attachComponent($yiiConfig);
        $this->attachWebErrorHandler($yiiConfig);
        $this->attachLogTarget($yiiConfig);
    }
	
	/**
	 * @param array $yiiConfig
	 */
    public function attachForConsole(&$yiiConfig) {
        $this->attachComponent($yiiConfig);
        $this->attachConsoleErrorHandler($yiiConfig);
        $this->attachLogTarget($yiiConfig);
    }

}
