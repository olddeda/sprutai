<?php
namespace api\models\content;

use common\modules\base\helpers\enum\ModuleType;
use common\modules\content\helpers\enum\Type;

/**
 * Class News
 * @package api\models\content
 */
class News extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::NEWS;
	}
}