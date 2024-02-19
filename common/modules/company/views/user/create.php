<?php

/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\Payment */
/* @var $company common\modules\company\models\Company */

$this->title = Yii::t('company-user', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-user', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="company-user-create">

    <?= $this->render('_form', [
        'model' => $model,
        'company' => $company,
    ]) ?>

</div>

