<?php

/* @var $this yii\web\View */
/* @var $company common\modules\company\models\Company */
/* @var $model common\modules\company\models\CompanyAddress */

$this->title = Yii::t('company-address', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/company/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/company/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-address', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="company-address-create">
	
	<?= $this->render('_form', [
		'model' => $model,
		'company' => $company,
	]) ?>

</div>
