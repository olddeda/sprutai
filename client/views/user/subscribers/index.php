<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model common\modules\user\models\User */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('user-profile', 'title_subscribers');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ($model->id == Yii::$app->user->id ? ['/user/profile/index'] : ['/user/profile/view', 'id' => $model->id])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-payment-index detail-view">
	
	<?= $this->render('../profile/_header', ['model' => $model]) ?>

	<div class="content-index padding-20">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '_view',
			'emptyText' => Yii::t('user-profile', 'error_empty_subscribers'),
			'layout' => "{items}\n{pager}"
		]); ?>
	</div>

</div>
