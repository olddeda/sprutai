<?php
namespace common\modules\eav\controllers;

use Yii;

use common\modules\base\components\Controller;

/**
 * AttributeController implements the CRUD actions for EavAttribute model.
 */
class DefaultController extends Controller
{
	/**
     * Lists all EavAttribute models.
     * @return mixed
     */
    public function actionIndex() {
        return $this->render('index');
    }

}