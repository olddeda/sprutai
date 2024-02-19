<?php
namespace common\modules\media\models\query;

use common\modules\media\helpers\enum\Type;

/**
 * This is the ActiveQuery class for [[\common\modules\media\models\MediaFile]].
 *
 * @see \common\modules\media\models\MediaFile
 */
class MediaFileQuery extends MediaQuery
{
	/**
	 * @inheritdoc
	 */
	public function init() {
		$modelClass = $this->modelClass;
		$tableName = $modelClass::tableName();
		$this->andWhere([$tableName.'.type' => Type::FILE]);
		parent::init();
	}
}