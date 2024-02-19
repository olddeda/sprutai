<?php

namespace common\modules\qa\actions;

use common\modules\qa\models\Question;
use common\modules\qa\models\QuestionInterface;
use common\modules\qa\Module;
use yii\db\Exception;

class AskAction extends Action
{
    const EVENT_SUBMITTED = 'questionSubmitted';

    /**
     * @var string
     */
    public $viewRoute = 'view';

    /**
     * @var string
     */
    public $viewFile = 'ask';

    /**
     * @return string
     * @throws Exception
     */
    public function run()
    {
        /** @var Question $model */
        $model = $this->getModel();

        if ($model->load($_POST)) {
            if ($model->haveDraft($_POST)) {
                $model->status = QuestionInterface::STATUS_DRAFT;
            }

            if (!$model->save()) {
                throw new Exception(Module::t('main', 'Error create question'));
            }

            $this->trigger(self::EVENT_SUBMITTED);
            return $this->controller->redirect([$this->viewRoute, 'id' => $model->id]);
        } else {
            return $this->render(compact('model'));
        }
    }
}
