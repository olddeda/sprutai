<?php
namespace api\modules\v1\components;

use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class ActiveController
 * @package api\modules\v1\components
 */
class ActiveController extends Controller
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass;

    /**
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = Model::SCENARIO_DEFAULT;

    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = Model::SCENARIO_DEFAULT;

    /**
     * {@inheritdoc}
     */
    public function init() {
        parent::init();
        if ($this->modelClass === null) {
            throw new InvalidConfigException('The "modelClass" property must be set.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function actions() {
        return [
            'index' => [
                'class' => 'api\modules\v1\components\actions\IndexAction',
                'modelClass' => $this->modelClass,
            ],
            'view' => [
                'class' => 'api\modules\v1\components\actions\ViewAction',
                'modelClass' => $this->modelClass,
            ],
            'create' => [
                'class' => 'api\modules\v1\components\actions\CreateAction',
                'modelClass' => $this->modelClass,
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'api\modules\v1\components\actions\UpdateAction',
                'modelClass' => $this->modelClass,
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'api\modules\v1\components\actions\DeleteAction',
                'modelClass' => $this->modelClass,
            ],
            'options' => [
                'class' => 'api\modules\v1\components\actions\OptionsAction',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs() {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }
}
