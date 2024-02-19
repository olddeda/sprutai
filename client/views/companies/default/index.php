<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type string */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('company', ($type ? 'title_'.$type : 'title'));

if (is_null($type)) {
	$this->params['breadcrumbs'][] = Yii::t('company', 'title');
}
else {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
	$this->params['breadcrumbs'][] =  $this->title;
}
?>

<div class="form-group margin-bottom-20">
	<?= $this->render('_tabs') ?>
</div>

<hr />

<div class="visible-xs">
    <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
</div>

<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<?= $this->render('_index', [
			'dataProvider' => $dataProvider,
			'type' => $type,
		]) ?>
	</div>

	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
		<div class="hidden-xs">
            <?= $this->render('//banner/view', ['showLeaders' => false]) ?>
		</div>
		
		<?= $this->render('../discount/_block_all') ?>
		
		<?= $this->render('../question/_block_last') ?>
	</div>

</div>