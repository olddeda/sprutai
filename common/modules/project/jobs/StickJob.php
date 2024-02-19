<?php
namespace common\modules\project\jobs;

use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\payment\models\Payment;
use common\modules\payment\helpers\enum\Status;

/**
 * Class StickJob
 * @package common\modules\notification\jobs
 */
class StickJob extends BaseObject implements JobInterface
{
   
    /**
     * @inheritdoc
     */
    public function execute($queue) {
	
		$models = Payment::find()->where([
			'module_type' => ModuleType::CONTENT,
			'module_id' => 105,
			'status' => Status::DELIVERY,
		])->andWhere(['<>', 'comment', ''])->all();
	
		$count = 0;
	
		if ($models) {
			foreach ($models as $model) {
				if ($model->user->address) {
					$subject = Yii::t('notification', 'post_tracker_zigbee_subject');
					$message = Yii::t('notification', 'post_tracker_zigbee', [
						'fio' => $model->user->getFio(),
						'address' => $model->user->address->address,
						'tracker' => $model->comment,
					]);
				
					Yii::$app->notification->queue([$model->user_id], $subject, $message, 'system');
				
					$model->status = Status::COMPLETED;
					$model->save();
				
					$count++;
				}
			}
		}
  
		echo 'Found '.$count;
    }
}
