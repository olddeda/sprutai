<?php
namespace api\modules\v1\controllers\media\actions;

use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

use common\modules\base\helpers\enum\Status;
use common\modules\base\components\flysystem\AwsS3Filesystem;
use common\modules\base\components\flysystem\LocalFilesystem;
use common\modules\base\helpers\enum\ModuleType;

use common\modules\media\models\MediaImage;
use common\modules\media\helpers\enum\Type;

use api\models\content\Content;

/**
 * Class DeleteAction
 * @package api\modules\v1\controllers\media\actions
 */
class DeleteAction extends Action
{

    /**
     * @param $module_type
     * @param $module_id
     * @param $id
     *
     * @return array|ActiveRecord|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function run($module_type, $module_id, $id) {
        $model = $this->_getModel($module_type, $module_id);
        if (is_null($model)) {
            throw new NotFoundHttpException('Not found');
        }

        $model = MediaImage::find()->where('module_type = :module_type AND module_id = :module_id AND id = :id AND status = :status', [
            'module_type' => $module_type,
            'module_id' => $module_id,
            'id' => $id,
            'status' => Status::ENABLED,
        ])->one();

        if (is_null($model)) {
            throw new NotFoundHttpException(Yii::t('media', 'error_not_found'));
        }

        $model->delete();

        Yii::$app->getResponse()->setStatusCode(200);

        return $model;
    }

    /**
     * @param $module_type
     * @param $module_id
     *
     * @return array|ActiveRecord|null
     */
    private function _getModel($module_type, $module_id) {
        $model = null;

        switch ($module_type) {
            case ModuleType::CONTENT:
                return Content::findOwn($module_id, true, 'content');
        }

        return $model;
    }
}