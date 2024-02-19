<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('tag', 'title');

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="visible-xs">
    <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
</div>

<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<?= $this->render('_index', [
			'dataProvider' => $dataProvider,
		]) ?>
	</div>

	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
		<div class="hidden-xs">
            <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
		</div>
		
		<?= $this->render('//author/_top') ?>
	</div>

</div>

