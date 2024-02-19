<?php

/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\Payment */
/* @var $company common\modules\company\models\Company */

$this->title = Yii::t('project-payer', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('project', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('project-payer', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="project-payer-update">

    <?= $this->render('_form', [
        'model' => $model,
        'company' => $company,
    ]) ?>

</div>

