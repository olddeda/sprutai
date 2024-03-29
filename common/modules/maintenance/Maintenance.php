<?php
namespace common\modules\maintenance;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Component;
use yii\helpers\FileHelper;

/**
 * Class Maintenance
 * @package common\modules\maintenance
 */
class Maintenance extends Component
{
    /**
     * Value of "OK" status code.
     */
    const STATUS_CODE_OK = 200;

    /**
     * Mode status.
	 *
     * @var bool
     */
    public $enabled = false;
    
    /**
     * Route to action.
	 *
     * @var string
     */
    public $route = 'maintenance/index';
    
    /**
     * Show title.
	 *
     * @var string
     */
    public $title = 'We’ll be back soon!';
    
    /**
     * Show message.
	 *
     * @var string
     */
    public $message = 'Sorry, perform technical works.';
    
    /**
     * Allowed user name(s).
	 *
     * @var array|string
     */
    public $users;
    
    /**
     * Allowed roles.
	 *
     * @var array
     */
    public $roles;
    
    /**
     * Allowed IP addresses.
	 *
     * @var string|array
     */
    public $ips;
    
    /**
     * Allowed urls.
	 *
     * @var array
     */
    public $urls;
    
    /**
     * Path to layout file.
	 *
     * @var string
     */
    public $layoutPath = '@common/modules/maintenance/views/layouts/main';
    
    /**
     * Path to view file
	 *
     * @var string
     */
    public $viewPath = '@common/modules/maintenance/views/maintenance/index';
    
    /**
     * Path to command file
	 *
     * @var string
     */
    public $commandPath = '@common/modules/maintenance';
    
    /**
     * Username attribute name
     * @since 0.2.0
     * @var string
     */
    public $usernameAttribute = 'username';
    
    /**
     * Default status code to send on maintenance
     * 503 = Service Unavailable
	 *
     * @var integer
     */
    public $statusCode = 503;
    
    /**
     * Retry-After header
	 *
     * @var bool|string
     */
    public $retryAfter = false;
	
	/**
	 * Web controller class name.
	 *
	 * @var string
	 */
	public $webController = 'common\modules\maintenance\controllers\MaintenanceController';
    
    /**
     * Console controller class name.
	 *
     * @var string
     */
    public $consoleController = 'common\modules\maintenance\commands\MaintenanceController';

    /**
     * Disable items.
	 *
     * @var boolean
     */
    protected $disable;

    /**
     * Initial component method.
     */
    public function init() {
        Yii::setAlias('@maintenance', $this->commandPath);
        
        if (!file_exists(Yii::getAlias('@maintenance'))) {
            FileHelper::createDirectory(Yii::getAlias('@maintenance'));
        }
        
        if (Yii::$app instanceof \yii\console\Application) {
            Yii::$app->controllerMap['maintenance'] = $this->consoleController;
        }
        else {
            if ($this->getIsEnabled()) {
                $this->filtering();
            }
        }
    }

    /**
     * Checks if mode is on.
     *
     * @param bool $onlyConsole
     * @return bool
     */
    public function getIsEnabled($onlyConsole = false) {
        $exists = file_exists($this->getStatusFilePath());
        return $onlyConsole ? $exists : $this->enabled || $exists;
    }

    /**
     * Return status file path.
     *
     * @return bool|string
     */
    protected function getStatusFilePath() {
        return Yii::getAlias('@maintenance/.enable');
    }

    /**
     * Turn off mode.
     *
     * @return bool
     */
    public function disable() {
        $path = $this->getStatusFilePath();
        if ($path && file_exists($path)) {
            return (bool) unlink($path);
        }
        return false;
    }

    /**
     * Turn on mode.
     *
     * @return bool
     */
    public function enable() {
        $path = $this->getStatusFilePath();
        return (bool) file_put_contents($path, ' ');
    }

    /**
     * Check IP (mask supported).
     *
     * @param $filter
     * @return bool
     */
    protected function checkIp($filter) {
        $ip = Yii::$app->getRequest()->getUserIP();
        return $filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos));
    }

    /**
     * Filtering by configuration.
	 *
	 * @throws InvalidConfigException
	 * @throws \Throwable
	 */
    protected function filtering() {
        $app = Yii::$app;
        if ($this->statusCode) {
            if (is_integer($this->statusCode)) {
                if ($app->getRequest()->getIsAjax()) {
                    $app->getResponse()->setStatusCode(self::STATUS_CODE_OK);
                }
                else {
                    $app->getResponse()->setStatusCode($this->statusCode);
                    if ($this->retryAfter){
                        $app->getResponse()->getHeaders()->set('Retry-After', $this->retryAfter);
                    }
                }
            }
            else {
                throw new InvalidConfigException('Parameter "statusCode" should be an integer.');
            }
        }
        
        // Check users
        if ($this->users) {
            if (is_array($this->users)) {
                $this->disable = $app->getUser()->getIdentity()
                    ? in_array($app->getUser()->getIdentity()->{$this->usernameAttribute}, $this->users)
                    : false;
            }
            elseif (is_string($this->users)) {
                $this->disable = $app->getUser()->getIdentity()->{$this->usernameAttribute} === $this->users;
            }
            else {
                throw new InvalidConfigException('Parameter "users" should be an array or string.');
            }
        }
        
        // Check roles
        if ($this->roles) {
            if (is_array($this->roles)) {
                foreach ($this->roles as $role) {
                    $this->disable = $this->disable || $app->getUser()->can($role);
                }
            }
            else {
                throw new InvalidConfigException('Parameter "roles" should be an array.');
            }
        }
        
        // Check URL's
        if ($this->urls) {
            if (is_array($this->urls)) {
                $this->disable = $this->disable || in_array($app->getRequest()->getPathInfo(), $this->urls);
            }
            else {
                throw new InvalidConfigException('Parameter "urls" should be an array.');
            }
        }
        
        // Check IP's
        if ($this->ips) {
            if (is_array($this->ips)) {
                foreach ($this->ips as $filter) {
                    $this->disable = $this->disable || $this->checkIp($filter);
                }
            } elseif (is_string($this->ips)){
                $this->disable = $this->disable || $this->checkIp($this->ips);
            } else {
                throw new InvalidConfigException('Parameter "ips" should be an array.');
            }
        }
        
        if (!$this->disable) {
            if ($this->route === 'maintenance/index') {
                $app->controllerMap['maintenance'] = $this->webController;
            }
            $app->catchAll = [$this->route];
        }
        else {
            $app->getResponse()->setStatusCode(self::STATUS_CODE_OK);
        }
    }
}