<?php

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="companies-view">
	
	<?= $this->render('_header', [
		'model' => $model,
		'question' => null,
	]) ?>
	
	<div class="margin-20">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
				<?= $this->render('_view_text', ['model' => $model]) ?>
			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('_view_contacts', ['model' => $model]) ?>
				<?= $this->render('_view_discount', ['model' => $model]) ?>
				<?= $this->render('_view_questions', ['model' => $model]) ?>
			</div>
		</div>
	</div>
	
</div>
