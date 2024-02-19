<?php
namespace common\modules\media\models;

use Yii;

use common\modules\media\models\query\MediaFileQuery;

class MediaFile extends Media
{
	/**
	 * @inheritdoc
	 * @return \common\modules\media\models\query\MediaFileQuery the active query used by this AR class.
	 */
	public static function find() {
		return new MediaFileQuery(get_called_class());
	}
}