<?php
namespace api\modules\v1\components\actions;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Class DeleteAction
 * @package api\modules\v1\components\actions
 */
class DeleteAction extends Action
{
    /**
     * @param $id
     *
     * @return mixed|\yii\db\ActiveRecordInterface
     * @throws ServerErrorHttpException
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function run($id) {
        $model = $this->findModel($id, true);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->delete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(200);
        return $model;
    }
}
