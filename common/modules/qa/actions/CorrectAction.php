<?php

namespace common\modules\qa\actions;

use common\modules\qa\models\Answer;
use common\modules\qa\models\Question;
use Yii;
use yii\web\Response;

class CorrectAction extends Action
{
    public $questionClass = 'common\modules\qa\models\QuestionInterface';

    public $viewRoute = 'view';

    public $partialViewFile = 'parts/answer-correct';

    public function run($id, $questionId)
    {
        /** @var Answer $answer */
        $answer = $this->findModel($this->modelClass, $id);
        /** @var Question $question */
        $question = $answer->question;

        $response = [
            'data' => ['status' => false],
            'format' => 'json'
        ];

        if ($question && $question->isAuthor()) {
            $response['data']['status'] = $answer->toggleCorrect();
            $response['data']['html'] = $this->controller->renderPartial($this->partialViewFile, compact('answer', 'question'));
        }

        if (Yii::$app->request->isAjax) {
            return new Response($response);
        }

        return $this->controller->redirect([$this->viewRoute, 'id' => $questionId]);
    }
}
