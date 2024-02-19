<?php
namespace common\modules\content\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use common\modules\content\models\Content;
use common\modules\content\helpers\enum\Status;
use common\modules\content\models\ContentStat;

/**
 * Class CronController
 * @package common\modules\content\commands
 */
class CronController extends Controller
{
    
    public function actionIndex() {
    
    }
    
    public function actionStatComments() {
        $models = Content::find()
            ->joinWith(['comments'])
            ->votes()
            ->where(['not in', Content::tableName().'.status', [Status::TEMP, Status::DELETED]])
            ->all();
    
        foreach ($models as $model) {
            ContentStat::updateLinks($model);
        }
    }
    
}