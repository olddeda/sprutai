<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type string */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('company-discount', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] =  $this->title;

use yii\widgets\ListView; ?>


<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<div class="content-index">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view',
				'layout' => "{items}\n{pager}",
				'emptyText' => Yii::t('company-discount', 'list_is_empty'),
			]); ?>
		</div>
	</div>
	
	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
		<?= $this->render('//banner/view', ['showLeaders' => true]) ?>
		
		<?= $this->render('//author/_top') ?>
	</div>

</div>