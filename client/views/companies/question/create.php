<?php

/* @var $this yii\web\View */
/* @var $company \common\modules\company\models\Company */
/* @var $model \common\modules\content\models\Question */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['companies/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['companies/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('company-question', 'title'), 'url' => ['index', 'company_id' => $company->id]];
$this->params['breadcrumbs'][] = Yii::t('company-question', 'title_create');

$this->title = Yii::t('company-question', 'title_create');

?>

<div class="companies-questions-create detail-view">
	
	<?= $this->render('../default/_header', ['model' => $company]) ?>
	
	<div class="content-index padding-20">
		
		<?= $this->render('_form', [
			'model' => $model,
			'company' => $company,
			'isCreate' => true,
		]) ?>
		
	</div>
	
</div>
