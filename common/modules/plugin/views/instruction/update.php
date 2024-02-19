<?php

/* @var $this yii\web\View */
/* @var $model common\modules\plugin\models\Plugin */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('plugin-instruction', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->context->pluginModel->title, 'url' => ['/plugin/default/view', 'id' => $this->context->pluginModel->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin-instruction', 'title'), 'url' => ['index', 'plugin_id' => $this->context->pluginModel->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="plugin-instruction-update">
	
	<?= $this->render('_form', ['model' => $model]) ?>
	
</div>
