<?php
namespace client\controllers;

use Yii;
use yii\web\UploadedFile;

use common\modules\base\components\Debug;

use client\components\Controller;
use client\forms\ItunesReportForm;

/**
 * Payment controller
 */
class ItunesController extends Controller
{
    /**
     * @return array
     */
	public function behaviors(): array
    {
		return [];
	}

    /**
     * @return string
     */
	public function actionReport(): string
    {
        $model = new ItunesReportForm();
        if (Yii::$app->request->isPost) {

            $model->rates = UploadedFile::getInstance($model, 'rates');
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->upload();
        }

        return $this->render('report', [
            'model' => $model
        ]);
	}
}
