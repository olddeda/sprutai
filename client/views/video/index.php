<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('content-video', 'title');
?>

<div class="visible-xs">
    <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
</div>

<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<div class="content-index">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view',
				'layout' => "{items}\n{pager}",
			]); ?>
		</div>
	</div>
	
	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
		<div class="hidden-xs">
            <?= $this->render('//banner/view', ['showLeaders' => true]) ?>
		</div>
		
		<?= $this->render('//author/_top') ?>
	</div>
</div>