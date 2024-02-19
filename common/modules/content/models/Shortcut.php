<?php
namespace common\modules\content\models;

use Yii;

use yii\helpers\ArrayHelper;

use common\modules\media\behaviors\MediaBehavior;
use common\modules\media\helpers\enum\Type as MediaType;

use common\modules\user\models\User;

use common\modules\content\helpers\enum\Type;

class Shortcut extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::SHORTCUT;
	}
	
	/**
	 * Returns a list of behaviors that this component should behave as.
	 *
	 * @return array the behavior configurations.
	 */
	public function behaviors() {
		return ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => MediaBehavior::class,
				'attribute' => 'video',
				'type' => MediaType::VIDEO,
			],
		]);
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['content_id', 'title', 'descr', 'text', 'status'], 'required'],
			[['content_id', 'author_id', 'type', 'status', 'date_at', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
			[['text'], 'string', 'max' => 100000],
			[['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
			[['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
			[['payment_types_ids', 'date'], 'safe'],
		
		];
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'shortcut';
	}
}