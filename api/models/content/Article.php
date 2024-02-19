<?php
namespace api\models\content;

use common\modules\content\helpers\enum\Type;

/**
 * Class Article
 * @package api\models\content
 */
class Article extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::ARTICLE;
	}
}