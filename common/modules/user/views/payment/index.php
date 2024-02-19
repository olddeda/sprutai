<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\payment\helpers\enum\Status;
use common\modules\payment\models\Payment;

use common\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model common\modules\user\models\User */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('user-payment', 'title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ($model->id == Yii::$app->user->id ? ['/user/profile/index'] : ['/user/profile/view', 'id' => $model->id])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-payment-index detail-view">
	
	<?= $this->render('../profile/_header', ['model' => $model]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">

            <?php Pjax::begin([
                'timeout' => 10000,
                'enablePushState' => true
            ]); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'tableOptions' => [
                    'class' => 'table table-striped'
                ],
                'emptyText' => Yii::t('user-payment', ($model->isOwn ? 'error_empty_list' : 'error_him_empty_list')),
                'columns' => [

                    // Id
                    [
                        'attribute' => 'id',
                        'headerOptions' => ['width' => '50'],
                    ],

                    // Date
                    [
                        'attribute' => 'date_at',
                        'value' => function ($data) {
                            return $data->getDatetime();
                        },
                        'filter' => DateTimePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_at',
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
                        'headerOptions' => ['width' => '170'],
                    ],
    
    
                    // Price
                    [
                        'attribute' => 'price',
                        'headerOptions' => ['width' => '160'],
                        'format' => 'currency',
                    ],
	
	                // Title
	                [
		                'attribute' => 'title',
	                ],
	
					// Comment
					[
						'attribute' => 'comment',
					],

                    // Status
                    [
                        'attribute' => 'status',
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
                        'value' => function($data) {
                            return Status::getLabel($data->status);
                        },
                        'headerOptions' => ['width' => '160'],
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>

		</div>
	</div>

</div>
