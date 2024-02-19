<?php
namespace common\modules\content\models;

use common\modules\base\helpers\enum\ModuleType;
use yii\helpers\ArrayHelper;

use common\modules\base\components\Debug;

use common\modules\content\helpers\enum\Type;
use common\modules\content\helpers\enum\PageType;

use common\modules\seo\models\Seo;

class Page extends Content
{
	/**
	 * Get model type
	 * @return int
	 */
	static public function type() {
		return Type::PAGE;
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return ArrayHelper::merge(parent::rules(), [
			[['page_type'], 'required'],
			['page_path', 'required', 'when' => function ($model) {
				return $model->page_type == PageType::PATH;
			}, 'whenClient' => "function (attribute, value) {
    			return $('#page-page_type').val() == '".PageType::PATH."';
			}"],
		]);
	}
	
	/**
	 * Get page by slug
	 * @param $slug
	 *
	 * @return mixed|null
	 */
	static public function findBySlug($slug) {
		$seo = Seo::find()->where([
			'module_type' => ModuleType::CONTENT,
			'slugify' => $slug,
		])->one();
		return $seo ? $seo->module : null;
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent() {
		return $this->hasOne(self::class, ['id' => 'content_id']);
	}
	
	/**
	 * @return string
	 */
	public function getUriModuleName() {
		return 'page';
	}
}