<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

use common\modules\telegram\helpers\enum\StatusUser;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramUser */

$this->title = Yii::t('telegram-user', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('telegram-user', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->getFullname();
?>
<div class="telegram-user-view detail-view">
	
	<?= $this->render('_header', ['model' => $model]) ?>

	<?php if ($model->username || $model->phone || $model->email) { ?>
	<div class="panel panel-default margin-20">
		<div class="panel-heading"><?= Yii::t('telegram-user', 'header_view_general_contacts') ?></div>
		<div class="panel-body">
			<?= DetailView::widget([
				'model' => $model,
				'options' => [
					'class' => 'table table-striped detail-view detail-view-general',
				],
				'attributes' => [
					[
						'format' => 'raw',
						'attribute' => 'username',
						'value' => Html::a('https://t.me/'.$model->username, 'https://t.me/'.$model->username, ['target' => '_blank']),
						'visible' => $model->username,
					],
					[
						'format' => 'raw',
						'attribute' => 'phone',
						'value' => Html::a($model->getPhoneFormatted(), 'tel://'.$model->phone),
						
					],
					[
						'format' => 'raw',
						'attribute' => 'email',
						'value' => Html::mailto($model->email),
						
					],
				],
			]) ?>
		</div>
	</div>
	<?php } ?>

	<div class="panel panel-default margin-20">
		<div class="panel-heading"><?= Yii::t('telegram-user', 'header_view_general_data') ?></div>
		<div class="panel-body">
			<?= DetailView::widget([
				'model' => $model,
				'options' => [
					'class' => 'table table-striped detail-view detail-view-general',
				],
				'attributes' => [
					[
						'format' => 'raw',
						'attribute' => 'role',
						'value' => $model->getRoles(),
					],
					[
						'attribute' => 'status',
						'value' => StatusUser::getLabel($model->status),
					],
					[
						'format' => 'datetime',
						'attribute' => 'lastvisit_at',
					
					],
					[
						'format' => 'datetime',
						'attribute' => 'created_at',
					
					],
				],
			]) ?>
		</div>
	</div>
	
</div>
