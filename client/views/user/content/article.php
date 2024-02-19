<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model common\modules\user\models\User */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('user-content', 'title_article', ['title' => $model->getAuthorName()]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ($model->id == Yii::$app->user->id ? ['/user/profile/index'] : ['/user/profile/view', 'id' => $model->id])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-payment-index detail-view">
	
	<?= $this->render('../profile/_header', ['model' => $model]) ?>

	<div class="content-index padding-20">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '//article/_view',
			'viewParams' => [
				'hideAuthorName' => true,
				'urlTarget' => '_blank',
			],
			'emptyText' => Yii::t('user-content', 'error_empty_article'),
			'layout' => "{items}\n{pager}"
		]); ?>
	</div>

</div>
