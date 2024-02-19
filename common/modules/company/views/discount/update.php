<?php

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\company\models\CompanyDiscount */

$this->title = Yii::t('company-discount', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-discount', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="company-discount-update">
	
	<?= $this->render('_form', [
		'model' => $model,
		'company' => $company,
	]) ?>

</div>