<?php

namespace common\modules\qa\actions;

use common\modules\qa\models\QuestionSearch;
use Yii;

class IndexAction extends Action
{

    public $viewFile = 'index';

    /**
     * @return string
     */
    public function run()
    {
        /** @var QuestionSearch $searchModel */
        $searchModel = $this->getModel();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $models = $dataProvider->getModels();

        return $this->render(compact('searchModel', 'models', 'dataProvider'));
    }
}
