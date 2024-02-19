<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\user\models\UserSearch;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

$this->title = Yii::t('user', 'title');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
	
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
						'attribute' => 'id',
						'headerOptions' => ['width' => '70'],
					],
					[
						'attribute' => 'fio',
						/*'value' => function($data) {
							return Html::a($data->fio, [
								'/user/profile/show',
								'id' => $data->id
							], [
								'title' => Yii::t('user', 'tooltip_fio'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]);
						},*/
						'format' => 'raw',
					],
					[
						'attribute' => 'username',
						/*'value' => function($data) {
							return Html::a($data->username, [
								'/user/profile/show',
								'id' => $data->id
							], [
								'title' => Yii::t('user', 'tooltip_fio'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]);
						},*/
						'format' => 'raw',
					],
					[
						'attribute' => 'email',
						'value' => function($data) {
							return Html::a($data->email, 'mailto:'.$data->email, [
								'title' => Yii::t('user', 'tooltip_email'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]);
						},
						'format' => 'raw',
					],
					[
						'attribute' => 'telegram',
						'value' => function($data) {
							return ($data->telegram && strlen($data->telegram->username)) ? $data->telegram->username : '-';
						},
						'format' => 'raw',
					],
					
					[
						'attribute' => 'phone',
						'value' => function($data) {
							return $data->profile->phone ? $data->profile->phone : '-';
							return Html::a(($data->profile->phone ? $data->profile->phone : '-'), [
								'/user/profile/show',
								'id' => $data->id
							], [
								'title' => Yii::t('user', 'tooltip_fio'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]);
						},
						'format' => 'raw',
						'headerOptions' => ['width' => '150'],
					],
					[
						'attribute' => 'created_at',
						'value' => function ($model) {
							return (extension_loaded('intl')) ? Yii::t('user', 'format_created_at', [$model->created_at]) : date('d-m-Y G:i', $model->created_at);
						},
						'filter' => DateTimePicker::widget([
							'model' => $searchModel,
							'attribute' => 'created_at',
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
						'header' => Yii::t('user', 'header_confirmation'),
						'value' => function ($model) {
							if ($model->isConfirmed) {
								return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'status_confirmed') . '</span></div>';
							}
							else {
								return Html::a(Yii::t('user', 'button_confirm_activation'), ['confirm', 'id' => $model->id], [
									'class' => 'btn btn-xs btn-success btn-block',
									'data-method' => 'post',
									'data-confirm' => Yii::t('user', 'confirm_activate'),
								]);
							}
						},
						'format' => 'raw',
						'visible' => Yii::$app->getModule('user')->enableConfirmation,
						'headerOptions' => ['width' => '100'],
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{signin} {update} {delete}',
						'headerOptions' => [
							'width' => '75',
							'style' => 'text-align:center;',
						],
						'contentOptions' => [
							'style'	=> 'text-align:center;',
						],
						'buttons' => [
							'signin' => function ($url, $model) {
								return Yii::$app->user->can('user.admin.signin') && Yii::$app->user->canAccess($model) ? Html::a('<span class="glyphicon glyphicon-log-in"></span>&nbsp;', $url, [
									'title' => Yii::t('user-admin', 'button_signin'),
									'data-toggle' => 'tooltip',
									'data-pjax' => '0',
								]) : '';
							},
							'update' => function ($url, $model) {
								return Yii::$app->user->can('user.admin.update') && Yii::$app->user->canAccess($model, true) ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
									'title' => Yii::t('base', 'button_update'),
									'data-toggle' => 'tooltip',
									'data-pjax' => '0',
								]) : '';
							},
							'delete' => function ($url, $model) {
								return Yii::$app->user->can('user.admin.delete') && Yii::$app->user->canAccess($model) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
									'title' => Yii::t('base', 'button_delete'),
									'data-toggle' => 'tooltip',
									'data-confirm' => Yii::t('user', 'confirm_delete'),
									'data-method' => 'post',
									'data-pjax' => '1',
								]) : '';
							},
						],
					],
				],
			]); ?>
			<?php Pjax::end(); ?>
		</div>
	</div>

	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
</div>
