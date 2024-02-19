<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model common\modules\plugin\models\Plugin */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = $model->title.' - '.Yii::t('plugin', 'title_version');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="plugin-view-version detail-view">
	
	<?= $this->render('_header', [
		'model' => $model,
	]) ?>
	
	<div class="margin-20">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '_view_version',
			'layout' => '<div class="streamline b-l b-info m-l-lg m-b">{items}</div>{pager}',
			'emptyText' => Yii::t('plugin', 'empty_versions'),
		]); ?>
	</div>
</div>
