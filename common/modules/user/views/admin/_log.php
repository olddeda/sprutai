<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

use common\modules\base\extensions\datetimepicker\DateTimePicker;

/**
 * @var yii\web\View
 * @var common\modules\user\models\User
 */
?>

<?php $this->beginContent('@common/modules/user/views/admin/update.php', ['user' => $user]) ?>

<div class="admin-log">

	<h1><?= Html::encode($this->title) ?></h1>

	<div class="row margin-top-20">
		<div class="col-md-12">
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => false
			]); ?>
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'layout' => "{items}\n{pager}",
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'columns' => [
					[
						'attribute' => 'visit',
						'value' => function ($model) {
							return (extension_loaded('intl')) ? Yii::t('user', 'format_created_at', [$model->visit]) : date('d-m-Y G:i', $model->visit);
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'visit',
							'template' => '{input}{button}{reset}',
							'language' => 'ru',
							'pickButtonIcon' => 'glyphicon glyphicon-calendar',
							'clientOptions' => [
								'autoclose' => true,
								'format' => 'dd-mm-yyyy',
								'todayBtn' => true,
								'minView' => 2,
							],
						]),
						'headerOptions' => ['width' => '160'],
					],
					[
						'attribute' => 'ip',
						'headerOptions' => ['width' => '140'],
					],
					[
						'attribute' => 'user_agent',
					],
				],
			]); ?>
			<?php Pjax::end(); ?>
		</div>
	</div>
</div>

<?php $this->endContent() ?>
