<?php

use yii\helpers\Html;

use yii\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\modules\user\models\UserAddress $model
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-address', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ['/user/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-md-3">
		<?= $this->render('../settings/_menu') ?>
	</div>
	<div class="col-md-9">
		<div class="panel panel-default">
			<div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
						<?php Pjax::begin([
							'timeout' => 10000,
							'enablePushState' => false
						]); ?>
						
						<?= GridView::widget([
							'dataProvider' => $dataProvider,
							'showHeader' => $dataProvider->count,
							'showFooter' => false,
							'emptyText' => false,
							'summary' => false,
							'tableOptions' => [
								'class' => 'table table-striped'
							],
							'columns' => [
							    
							    [
							        'attribute' => 'address',
                                ],
								
								[
									'attribute' => 'is_primary',
                                    'format' => 'boolean',
									'headerOptions' => ['width' => '150'],
								],
								
								// Actions
								[
									'class' => 'yii\grid\ActionColumn',
									'headerOptions' => [
										'width' => '70',
										'style' => 'text-align: center;',
									],
									'template' => '{primary} {update} {delete}',
									'buttons' => [
										'primary' => function ($url, $model) {
											return ($model->is_primary) ? '<span class="glyphicon glyphicon-heart"></span>' : Html::a('<span class="glyphicon glyphicon-heart-empty"></span>', $url, [
												'title' => Yii::t('user-address', 'button_primary'),
												'data-method' => 'POST',
												'data-confirm' => Yii::t('user-address', 'confirm_primary_name', ['title' => $model->address]),
												'data-pjax' => '1',
												'data-toggle' => 'tooltip',
											]);
										},
										'update' => function ($url, $model) {
											return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
												'title' => Yii::t('base', 'button_update'),
												'data-pjax' => '0',
												'data-toggle' => 'tooltip',
											]);
										},
										'delete' => function ($url, $model) {
											return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
												'title' => Yii::t('base', 'button_delete'),
												'data-method' => 'POST',
												'data-confirm' => Yii::t('user-address', 'confirm_delete_name', ['title' => $model->address]),
												'data-pjax' => '1',
												'data-toggle' => 'tooltip',
											]);
										},
									],
								],
							],
						]); ?>
						<?php Pjax::end(); ?>
                    </div>
                </div>
                
                <div class="form-group margin-top-20">
                    <?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('user-address', 'button_add'), ['create'], [
                        'class' => 'btn btn-primary btn-lg'
					]) ?>
                </div>
			</div>
		</div>
	</div>
</div>