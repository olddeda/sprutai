<?php
namespace api\models\content;

use common\modules\content\helpers\enum\Type;

/**
 * Class Video
 * @package api\models\content
 */
class Video extends Content
{
    /**
     * Get model type
     * @return int
     */
    static public function type() {
        return Type::VIDEO;
    }
}