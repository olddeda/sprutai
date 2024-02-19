<?php
namespace api\models\content;

use common\modules\content\helpers\enum\Type;

/**
 * Class Qa
 * @package api\models\content
 */
class Qa extends Content
{
    /**
     * Get model type
     * @return int
     */
    static public function type() {
        return Type::QA;
    }
}