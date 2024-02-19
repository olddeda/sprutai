<?php
namespace api\models\content;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\content\helpers\enum\Type;

/**
 * Class Page
 * @package api\models\content
 */
class Page extends Content
{
    /**
     * Get model type
     * @return int
     */
    static public function type() {
        return Type::PAGE;
    }
}