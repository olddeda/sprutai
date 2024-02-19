<?php
namespace api\modules\v1\components\actions;

use Yii;

/**
 * Class ViewAction
 * @package yii\rest
 */
class ViewAction extends Action
{
    /**
     * @param $id
     *
     * @return \yii\db\ActiveRecordInterface
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id) {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        return $model;
    }
}
