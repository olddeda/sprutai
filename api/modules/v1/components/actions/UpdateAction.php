<?php
namespace api\modules\v1\components\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class UpdateAction
 * @package api\modules\v1\components\actions
 */
class UpdateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @param $id
     *
     * @return ActiveRecord
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function run($id) {

        /* @var $model ActiveRecord */
        $model = $this->findModel($id, true);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            $this->uploadMedia($model);
        }
        else if (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        return $model;
    }
}
