<?php

use yii\widgets\DetailView;
use yii\bootstrap\Html;

/**
 * @var \yii\web\View $this
 * @var \common\modules\user\models\User $model
 * @var \common\modules\user\models\UserProfile $profile
 */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('user-profile', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-profile-view detail-view">
	
	<?= $this->render('_header', ['model' => $model]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-heading"><?= Yii::t('user-profile', 'header_view_general_contacts') ?></div>
		<div class="panel-body">
			<?= DetailView::widget([
				'model' => $model,
				'options' => [
					'class' => 'table table-striped detail-view detail-view-general',
				],
				'attributes' => [
					[
						'format' => 'raw',
						'attribute' => 'phone',
						'value' => $model->profile->phone,
                        'visible' => strlen($model->profile->phone) && Yii::$app->user->getIsAdmin(),
					],
					[
						'format' => 'raw',
						'attribute' => 'email',
						'value' => $model->email,
						'visible' => strlen($model->email) && Yii::$app->user->getIsAdmin(),
					],
					[
						'format' => 'raw',
						'attribute' => 'telegram',
						'value' => ($model->telegram ? Html::a('@'.$model->telegram->username, 'https://t.me/'.$model->telegram->username, ['target' => '_blank']) : null),
						'visible' => ($model->telegram && strlen($model->telegram->username))
					],
					[
						'format' => 'raw',
						'attribute' => 'address',
						'label' => Yii::t('user-address', 'field_address'),
						'value' => ($model->address) ? $model->address->address : null,
						'visible' => ($model->address && strlen($model->address->address)) && Yii::$app->user->getIsAdmin(),
					],
				],
			]) ?>
		</div>
	</div>
	
</div>
