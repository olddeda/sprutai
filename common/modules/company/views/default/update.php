<?php

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('plugin', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

?>

<div class="plugin-default-update">
	
	<?= $this->render('_header', ['model' => $model]) ?>

	<div class="margin-top-20">
		<?= $this->render('_form', ['model' => $model]) ?>
	</div>

</div>
