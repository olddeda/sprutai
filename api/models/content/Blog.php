<?php
namespace api\models\content;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\content\helpers\enum\Type;

/**
 * Class Blog
 * @package api\models\content
 */
class Blog extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::BLOG;
	}
}