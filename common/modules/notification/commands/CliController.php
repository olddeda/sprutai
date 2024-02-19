<?php
namespace common\modules\notification\commands;

use Yii;
use yii\console\Controller;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\payment\models\Payment;
use common\modules\payment\helpers\enum\Status;

use common\modules\project\jobs\StickJob;

/**
 * Class CriController
 * @package common\modules\telegram\commands
 *
 * @property \common\modules\telegram\Module $module
 */

class CliController extends Controller
{
	public function actionIndex() {
	
	}
	
	public function actionTest() {
		Yii::$app->queue->push(new StickJob());
	}
	
	public function actionZigbee() {
		
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
		
		echo "Sent - ".$count.PHP_EOL;
	}
}