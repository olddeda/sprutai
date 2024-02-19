<?php

namespace common\modules\qa\actions;

use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use common\modules\qa\models\Answer;

/**
 * Class AnswerAction
 * @package common\modules\qa\actions
 */
class AnswerAction extends Action
{
    const EVENT_SUBMITTED = 'answerSubmitted';

    /**
     * @var string
     */
    public $viewRoute = 'view';

    /**
     * @var string
     */
    public $viewFile = 'answer';

    /**
     * @param $id
     * @return string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        /** @var Answer $model */
        $model = $this->getModel(['question_id' => $id]);

        $question = $model->question;

        if (!$question) {
            $this->notFoundException();
        }

        if ($model->load($_POST) && $model->save()) {
            $this->trigger(self::EVENT_SUBMITTED);

            return $this->controller->redirect([$this->viewRoute, 'id' => $question->id, 'alias' => $question->alias]);
        } else {
            return $this->render(compact('model', 'question'));
        }
    }
}
