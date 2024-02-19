<?php
namespace common\modules\shortener;

use Yii;
use yii\base\Module as BaseModule;
use yii\db\Expression;
use yii\helpers\Url;

use common\modules\shortener\models\Shortener;

class Module extends BaseModule
{
    /**
     * @var string host name
     */
    public $hostName = 'sprut.ai';

    /**
     * @var string host scheme
     */
    public $hostScheme = 'https';

    /**
     * @var string module name
     */
    public static $name = 'shortener';
    
    /**
     * @var string the namespace that controller classes are in.
     * This namespace will be used to load controller classes by prepending it to the controller
     * class name.
     */
    public $controllerNamespace = 'common\modules\shortener\controllers';
}
