<?php

use yii\widgets\DetailView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\Company */

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="margin-0 text-primary"><?= Yii::t('company', 'header_view_contacts') ?></h4>
	</div>
	<div class="panel-body">
		<?= DetailView::widget([
			'model' => $model,
			'options' => [
				'class' => 'table table-striped detail-view detail-view-general',
			],
			'attributes' => [
				[
					'format' => 'raw',
					'attribute' => 'site',
					'value' => Html::a($model->site, $model->site, ['target' => '_blank']),
					'visible' => strlen($model->site),
				],
				[
					'format' => 'raw',
					'attribute' => 'email',
					'value' => Html::mailto($model->email, $model->email),
					'visible' => strlen($model->email),
				],
				[
					'format' => 'raw',
					'attribute' => 'phone',
					'value' => $model->phone,
					'visible' => strlen($model->phone),
				],
				[
					'format' => 'raw',
					'attribute' => 'telegram',
					'value' => ($model->telegram) ? Html::a('t.me/'.$model->telegram, 'tg://resolve?domain='.$model->telegram, ['target' => '_blank']) : null,
					'visible'=> strlen($model->telegram),
				],
				[
					'format' => 'raw',
					'attribute' => 'instagram',
					'value' => ($model->instagram) ? Html::a('instagram.com/'.$model->instagram, 'https://instagram.com/'.$model->instagram, ['target' => '_blank']) : null,
					'visible'=> strlen($model->instagram),
				],
				[
					'format' => 'raw',
					'attribute' => 'facebook',
					'value' => ($model->facebook) ? Html::a('facebook.com/'.$model->facebook, 'https://facebook.com/'.$model->facebook, ['target' => '_blank']) : null,
					'visible'=> strlen($model->facebook),
				],
				[
					'format' => 'raw',
					'attribute' => 'vk',
					'value' => ($model->vk) ? Html::a('vk.com/'.$model->vk, 'https://vk.com/'.$model->vk, ['target' => '_blank']) : null,
					'visible'=> strlen($model->vk),
				],
				[
					'format' => 'raw',
					'attribute' => 'ok',
					'value' => ($model->ok) ? Html::a('ok.ru/group/'.$model->ok, 'https://ok.ru/group/'.$model->ok, ['target' => '_blank']) : null,
					'visible'=> strlen($model->ok),
				],
				[
					'format' => 'raw',
					'attribute' => 'address',
					'value' => ($model->address) ? $model->address->address : null,
					'visible' => $model->address && $model->address->address,
				],
				[
					'format' => 'raw',
					'attribute' => 'promo',
					'label' => 'Промокод',
					'value' => '<b>'.Yii::$app->settings->get('promocode', 'value').'</b><br><em>'.Yii::$app->settings->get('promocode', 'description').'</em>',
					'visible' => $model->is_integrator && Yii::$app->settings->get('promocode', 'value') && Yii::$app->settings->get('promocode', 'description'),
				],
			],
		]) ?>
	</div>
</div>
