<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\modules\shortener\models\Shortener */
/* @var $searchModel common\modules\shortener\models\search\ShortenerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('shortener', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('shortener', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<div class="shortener-view">
	
	<div class="row margin-top-20">
		<div class="col-md-12">
			
			<fieldset>
				<legend><?= Yii::t('shortener', 'header_hits') ?></legend>
                
                <?php Pjax::begin([
                    'timeout' => 10000,
                    'enablePushState' => false
                ]); ?>
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => [
                        'class' => 'table table-striped'
                    ],
                    'columns' => [
                        [
                           'attribute' => 'country'
                        ],
                        [
                            'attribute' => 'city',
                        ],
                        [
                            'attribute' => 'ip',
                        ],
                        [
                            'attribute' => 'os',
                        ],
                        [
                            'attribute' => 'os_version',
                        ],
                        [
                            'attribute' => 'browser',
                        ],
                        [
                            'attribute' => 'browser_version',
                        ],
                        
                        // Expiration date
                        [
                            'attribute' => 'created_at',
                            'value' => function ($data) {
                                return $data->getCreated_date();
                            },
                            'filter' => DateTimePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'created_at',
                                'template' => '{input}{button}{reset}',
                                'language' => 'ru',
                                'pickButtonIcon' => 'glyphicon glyphicon-calendar',
                                'clientOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-mm-yyyy H:i:s',
                                    'todayBtn' => true,
                                    'minView' => 2,
                                ],
                            ]),
                            'headerOptions' => ['width' => '170'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
			</fieldset>

			<fieldset>
				<legend><?= Yii::t('shortener', 'header_general') ?></legend>

				<div class="row">
					<div class="col-md-12">
						<?= DetailView::widget([
							'model' => $model,
							'attributes' => [
								[
									'attribute' => 'title'
								],
								[
									'attribute' => 'url',
                                    'value' => function($data) {
                                        return Html::a($data->url, $data->url, ['target' => '_blank', 'data-pjax' => 0]);
                                    },
                                    'format' => 'raw',
								],
								[
									'attribute' => 'short_url',
									'value' => function($data) {
                                        return Html::a($data->short_url, $data->short_url, ['target' => '_blank', 'data-pjax' => 0]);
									},
									'format' => 'raw',
								],
								[
									'attribute' => 'description',
								],
                                'expiration_at:datetime',
							],
						]) ?>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><?= Yii::t('shortener', 'header_other') ?></legend>
				
				<?= DetailView::widget([
					'model' => $model,
					'attributes' => [
						'id',
						[
							'attribute' => 'status',
							'value' => Status::getLabel($model->status),
						],
						[
							'attribute' => 'created_by',
							'value' => ($model->createdBy) ? Html::a($model->createdBy->fio, [
								'/user/profile/view',
								'id' => $model->created_by
							], [
								'title' => Yii::t('shortener', 'tooltip_user'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]) : '-',
							'format' => 'raw',
						],
						[
							'attribute' => 'updated_by',
							'value' => ($model->updatedBy) ? Html::a($model->updatedBy->fio, [
								'/user/profile/view',
								'id' => $model->updated_by
							], [
								'title' => Yii::t('shortener', 'tooltip_user'),
								'data-toggle' => 'tooltip',
								'data-pjax' => '0',
							]) : '-',
							'format' => 'raw',
						],
						'created_at:datetime',
						'updated_at:datetime',
					],
				]) ?>
			</fieldset>
		</div>
	</div>

	<div class="form-group margin-top-30">
		<div class="row">
			<div class="col-md-8">
				<?php if (Yii::$app->user->can('shortener.default.update')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-primary'
					]) ?>
				<?php } ?>
				<?php if (Yii::$app->user->can('shortener.default.index')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
						'class' => 'btn btn-default btn-lg'
					]) ?>
				<?php } ?>
			</div>
			<div class="col-md-4 align-right">
				<?php if (Yii::$app->user->can('shortener.default.delete')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'id' => $model->id], [
						'class' => 'btn btn-lg btn-danger',
						'data' => [
							'confirm' => Yii::t('shortener', 'confirm_delete'),
							'method' => 'post',
						],
					]) ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
