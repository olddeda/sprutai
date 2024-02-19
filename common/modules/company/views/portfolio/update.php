<?php

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\content\models\Portfolio */

$this->title = Yii::t('content-portfolio', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('content-portfolio', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'company_id' => $company->id, 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';
?>

<div class="company-portfolio-update">
	
    <?= $this->render('_form', [
        'model' => $model,
		'company' => $company,
    ]) ?>

</div>
