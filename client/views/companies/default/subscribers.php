<?php

use yii\widgets\ListView;

use common\modules\company\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('company', 'title_subscribers_title', ['title' => $model->title]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('company', 'title_subscribers');
?>

<div class="user-payment-index">
	
	<?= $this->render('_header', [
		'model' => $model,
		'question' => null,
	]) ?>

	<div class="content-index padding-20">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '//author/_view',
					'emptyText' => Yii::t('company', 'error_empty_subscribers'),
					'layout' => "{items}\n{pager}"
				]); ?>
			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('_view_contacts', ['model' => $model]) ?>
				<?= $this->render('_view_discount', ['model' => $model]) ?>
				<?= $this->render('_view_questions', ['model' => $model]) ?>
			</div>
		</div>
	</div>

</div>
