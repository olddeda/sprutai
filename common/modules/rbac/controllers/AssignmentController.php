<?php

namespace common\modules\rbac\controllers;

use Yii;
use yii\web\Controller;

use common\modules\rbac\models\Assignment;

class AssignmentController extends Controller
{
    /**
     * Show form with auth items for user.
     * 
     * @param int $id
     */
    public function actionAssign($id) {
        $model = Yii::createObject([
            'class' => Assignment::className(),
            'user_id' => $id,
        ]);
        
        if ($model->load(\Yii::$app->request->post()) && $model->updateAssignments()) {
        }

        return \common\modules\rbac\widgets\Assignments::widget([
            'model' => $model,
        ]);
    }
}