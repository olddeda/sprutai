<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type string */

use yii\helpers\Html;

$this->context->layoutContent = 'content_no_panel';


if ($type == 'newest') {
	$this->title = Yii::t('news', 'title');
}
else {
	$this->title = Yii::t('news', 'title_'.$type);
	$this->params['breadcrumbs'][] = ['label' => Yii::t('news', 'title'), 'url' => ['index']];
}

$this->params['breadcrumbs'][] = Html::encode($this->title);

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
            <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
		</div>
		
		<?= $this->render('//author/_top') ?>
	</div>

</div>