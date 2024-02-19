<?php
namespace common\modules\seo\behaviors;

use common\modules\base\components\ActiveRecord;
use common\modules\base\helpers\enum\ModuleType;
use common\modules\base\helpers\enum\Status;
use common\modules\seo\models\Seo;
use Yii;
use yii\base\Behavior;

class SeoFields extends Behavior
{
	/**
	 * @inheritdoc
	 */
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'updateFields',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateFields',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteFields',
        ];
    }
	
	/**
	 * Update event
	 */
	public function updateFields($event) {
		if (method_exists(Yii::$app->request, 'post')) {
			$post = Yii::$app->request->post();

			$model = $this->getSeo();
			$model->load($post);
			$model->save();
		}
	}
	
	/**
	 * Delete event
	 *
	 * @return bool
	 */
	public function deleteFields($event) {
		if ($this->owner->seo)
			$this->owner->seo->delete();
		return true;
	}
	
	/**
	 * @return array|Seo|null|ActiveRecord
	 */
    public function getSeo() {
    	$model = Seo::find()->where('module_type = :module_type AND module_id = :module_id', [
    		':module_type' => $this->owner->moduleType,
		    ':module_id' => $this->owner->id
	    ])->one();
        if (!$model) {
	        $model = new Seo;
			$model->module_class = get_class($this->owner);
			$model->status = Status::ENABLED;
			$model->module_type = $this->owner->moduleType;
			$model->module_id = $this->owner->id;
			$model->module_name = ModuleType::getItem($model->module_type);
        }
	    return $model;
    }
}
