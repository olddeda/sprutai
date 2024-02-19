<?php
namespace common\modules\catalog;

use Yii;
use yii\base\Module as BaseModule;
use yii\db\Expression;
use yii\helpers\Url;

use common\modules\shortener\models\Shortener;

/**
 * Class Module
 * @package common\modules\catalog
 */
class Module extends BaseModule
{
    
    /**
     * @var string module name
     */
    public static $name = 'catalog';
    
    /**
     * @var string the namespace that controller classes are in.
     * This namespace will be used to load controller classes by prepending it to the controller
     * class name.
     */
    public $controllerNamespace = 'common\modules\catalog\controllers';
}
