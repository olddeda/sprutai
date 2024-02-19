<?php

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\company\models\CompanyAddress */

$this->title = Yii::t('company-address', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-address', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => $model->address, 'url' => ['view', 'company_id' => $company->id, 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="company-address-update">
	
	<?= $this->render('_form', [
		'model' => $model,
		'company' => $company,
	]) ?>

</div>
