<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\components\Debug;

use common\modules\media\widgets\show\ImageShowWidget;
use common\modules\media\helpers\enum\Mode;

use common\modules\telegram\helpers\enum\Role;
use common\modules\telegram\helpers\enum\StatusUser as Status;
use common\modules\telegram\helpers\enum\StatusUser;
use common\modules\telegram\models\TelegramRegion;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\telegram\models\search\TelegramUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('telegram-user', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="telegram-user-index">
	
	<div class="row">
		<div class="col-md-12">
			
			<?php Pjax::begin(); ?>
			
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'filterModel' => $searchModel,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'columns' => [
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '150'],
					],
					
					[
						'format' => 'raw',
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'role',
							'items' => Role::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'attribute' => 'role',
						'value' => function($data) {
							return $data->getRoles();
						},
						'headerOptions' => ['width' => '150'],
					],
					
					'username',
					
					[
						'attribute' => 'fullname',
						'value' => function ($data) {
							return $data->getFullname();
						}
					],
					
					[
						'attribute' => 'phone',
						'value' => function ($data) {
							return $data->getPhoneFormatted();
						}
					],
					
					'email',
					
					[
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'region_id',
							'items' => TelegramRegion::listData('id', 'title', 'sequence'),
							'clientOptions' => [
								'allowClear' => true,
								'placeholder' => '',
							],
						]),
						'attribute' => 'region_id',
						'value' => function ($data) {
							return $data->region ? $data->region->title : null;
						}
					],
					
					[
						'filter' => Select2::widget([
							'model' => $searchModel,
							'attribute' => 'status',
							'items' => Status::listData(),
							'clientOptions' => [
								'allowClear' => true,
								'hideSearch' => true,
								'placeholder' => '',
							],
						]),
						'attribute' => 'status',
						'value' => function($data) {
							return Status::getLabel($data->status);
						},
						'headerOptions' => ['width' => '170'],
					],
					
					[
						'filter' => false,
						'attribute' => 'lastvisit_at',
						'format' => 'datetime',
						'headerOptions' => ['width' => '150'],
					],
					
					[
						'filter' => false,
						'attribute' => 'created_at',
						'format' => 'datetime',
						'headerOptions' => ['width' => '150'],
					],
					
					[
						'header' => '',
						'value' => function ($data) {
							if ($data->status == StatusUser::BANNED) {
								return Yii::$app->user->can('telegram.user.unblock') ? Html::a(Yii::t('telegram-user', 'button_unblock'), ['unblock', 'id' => $data->id], [
									'class' => 'btn btn-sm btn-success ',
									'data-method' => 'post',
									'data-confirm' => Yii::t('telegram-user', 'confirm_unblock', ['fullname' => $data->getFullname()]),
								]) : '';
							}
							else {
								return Yii::$app->user->can('telegram.user.block') ? Html::a(Yii::t('telegram-user', 'button_block'), ['block', 'id' => $data->id], [
									'class' => 'btn btn-sm btn-danger',
									'data-method' => 'post',
									'data-confirm' => Yii::t('telegram-user', 'confirm_block', ['fullname' => $data->getFullname()]),
								]) : '';
							}
						},
						'format' => 'raw',
						'visible' => Yii::$app->user->can('telegram.user.block') || Yii::$app->user->can('telegram.user.unblock'),
						'headerOptions' => ['width' => '100'],
					],
					
					[
						'class' => 'yii\grid\ActionColumn',
						'template' => '{view}'
					],
				],
			]); ?>
			
			<?php Pjax::end(); ?>
			
		</div>
	</div>
	
</div>
