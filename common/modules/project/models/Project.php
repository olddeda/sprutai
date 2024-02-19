<?php
namespace common\modules\project\models;

use yii\helpers\ArrayHelper;

use common\modules\content\helpers\enum\Type;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;

use common\modules\content\models\Content;

class Project extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::PROJECT;
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'background',
				'type' => MediaType::IMAGE,
			],
			[
				'class' => MediaBehavior::class,
				'attribute' => 'logo',
				'type' => MediaType::IMAGE,
			],
		]);
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'projects';
	}
}