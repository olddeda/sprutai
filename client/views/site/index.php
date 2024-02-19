<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = 'Главная';
?>

<div class="visible-xs">
	<div class="form-group margin-top-0">
        <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('article', 'button_add'), ['/content/article/create'], [
            'class' => 'btn btn-primary btn-lg'
        ]) ?>
	</div>

    <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
</div>

<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">

		<div class="form-group margin-top-0 hidden-xs">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('article', 'button_add'), ['/content/article/create'], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
		
		<div class="content-index">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view',
				'layout' => "{items}\n{pager}",
			]); ?>
		</div>

		<div class="form-group margin-top-20">
			<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('article', 'button_add'), ['/content/article/create'], [
				'class' => 'btn btn-primary btn-lg'
			]) ?>
		</div>
	</div>
	
	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3" style="margin-top: 60px">
		<div class="hidden-xs">
            <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
		</div>
		
		<?= $this->render('//author/_top') ?>
	</div>
</div>