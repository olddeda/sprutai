<?php
namespace common\modules\media\commands;

use Yii;
use yii\console\Controller;

use common\modules\media\models\Media;

class CliController extends Controller {
	
	public function actionPurge() {
		Media::deleteAll('status = :status AND created_at < :created_at', [
			':status' => \common\modules\base\helpers\enum\Status::TEMP,
			':created_at' => strtotime("yesterday 00:00")
		]);
	}
}