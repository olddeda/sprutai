<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $items array */


use yii\helpers\Html;
use yii\widgets\ListView;

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('favorite', 'title');

$this->params['breadcrumbs'][] = Html::encode($this->title);

?>

<div class="favorite-index detail-view">
	
	<?= $this->render('_header', [
		'items' => $items
	]) ?>
	
	<div class="content-index padding-20">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '/author/_view',
			'layout' => "{items}\n{pager}",
			'emptyText' => Yii::t('favorite', 'error_empty_author'),
		]); ?>
	</div>
	
</div>

