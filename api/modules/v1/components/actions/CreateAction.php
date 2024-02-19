<?php
namespace api\modules\v1\components\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

use common\modules\base\components\ArrayHelper;

/**
 * Class CreateAction
 * @package api\modules\v1\components\actions
 */
class CreateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * @var string the name of the view action. This property is needed to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';

    /**
     * @return ActiveRecord
     * @throws ServerErrorHttpException
     * @throws InvalidConfigException
     */
    public function run() {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }


        /* @var $model ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $attributes = array_keys($model->getAttributes());

        if (ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'status') === 0 && in_array('status', $attributes)) {
            $modelClass = $this->modelClass;

            /** @var ActiveQuery $query */
            $query = $modelClass::find()
                ->select(['*'])
                ->where([
                    'status' => 0
                ])
            ;

            $userField = null;
            if (in_array('user_id', $attributes)) {
                $userField = 'user_id';
            }
            if (in_array('author_id', $attributes)) {
                $userField = 'author_id';
            }
            if (!is_null($userField)) {
                $query->andWhere([
                    $userField => Yii::$app->user->id
                ]);
            }

            $type = ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'type');
            if ($type && in_array('type', $attributes)) {
                $query->andWhere([
                    'type' => $type,
                ]);
            }

            $moduleType = ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'module_type');
            $moduleId = ArrayHelper::getValue(Yii::$app->getRequest()->getBodyParams(), 'module_id');
            if ($moduleType && $moduleId && in_array('module_type', $attributes) && in_array('module_id', $attributes)) {
                $query->andWhere([
                    'module_type' => (int)$moduleType,
                    'module_id' => (int)$moduleId,
                ]);
            }

            if ($query->exists()) {
                return $query->one();
            }

            $model->setScenario('temp');
        }

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            $response = Yii::$app->getResponse();

            if ($error = $this->uploadMedia($model)) {
                $response->setStatusCode(422);
                return [
                    'errors' => $error
                ];
            }

            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([
                $this->viewAction,
                'id' => $id
            ], true));
        }
        else if (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}
