<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\datetimepicker\DateTimePicker;
use common\modules\base\extensions\editable\EditableColumn;

use common\modules\payment\helpers\enum\StatusWithdrawal;
use common\modules\payment\models\Payment;

use common\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $months array */
/* @var $currentMonth string */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('payment-withdrawal', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<?php

$js = <<<JS
	$('#select-month').change(function() {
		$('#form-month').submit();
	});
JS;
$this->registerJs($js);

?>

<div class="user-payment-index detail-view">
	
	<div class="panel panel-default margin-20">
		<div class="panel-body">
			
			<?= Html::beginForm(Url::toRoute('/payment/withdrawal/index'), 'get', ['id' => 'form-month']) ?>
			
			<div style="width: 100px">
				<?= Select2::widget([
					'name' => 'month',
					'value' => $currentMonth,
					'items' => $months,
					'clientOptions' => [
						'hideSearch' => true,
					],
					'options' => [
						'id' => 'select-month',
					],
				]) ?>
			</div>
			
			<?= Html::endForm() ?>
			
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => true
			]); ?>
			
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'showFooter' => true,
				//'filterModel' => false,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'emptyText' => Yii::t('payment','error_empty_list'),
				'columns' => [
					
					// Id
					[
						'attribute' => 'id',
						'headerOptions' => ['width' => '80'],
					],
					
					// Date
					[
						'attribute' => 'date_at',
						'value' => function ($data) {
							return $data->getDatetime();
						},
						'headerOptions' => ['width' => '170'],
					],
					
					
					// Price
					[
						'attribute' => 'price',
						'headerOptions' => ['width' => '160'],
						'format' => 'currency',
						'footer' => Yii::$app->formatter->asCurrency(Payment::getTotal($dataProvider->models, 'price')),
						'footerOptions' => ['style' => 'font-weight:bold;'],
					],
					
					// Price with tax
					[
						'attribute' => 'price_tax',
						'value' => function ($data) {
							return $data->price_tax;
						},
						'headerOptions' => ['width' => '180'],
						'format' => 'currency',
						'footer' => Yii::$app->formatter->asCurrency(Payment::getTotal($dataProvider->models, 'price_tax')),
						'footerOptions' => ['style' => 'font-weight:bold;'],
					],
					
					// Title
					[
						'attribute' => 'title',
						'value' => function ($data) {
							return Html::decode($data->title);
						}
					],
					
					// User fio
					[
						'attribute' => 'user_fio',
						'value' => function($data) {
							return $data->user ? $data->user->getFio() : $data->user_id;
						},
					],
					
					// Status
					[
						'class' => EditableColumn::class,
						'type' => 'select2',
						'attribute' => 'status',
						'value' => function($data) {
							return StatusWithdrawal::getLabel($data->status);
						},
						'url' => ['editable'],
						'editableOptions' => function ($data) {
							return [
								'title' => Yii::t('payment-withdrawal', 'editable_status'),
								'source' => StatusWithdrawal::listData(),
								'hideSearch' => true,
								'value' => $data->status,
							];
						},
						'pluginOptions' => [
							'select2' => [
								'width' => 200,
								'hideSearch' => true,
							],
						],
						'headerOptions' => ['width' => '200'],
					],
					
					// Actions
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '30',
							'style' => 'text-align:center;'
						],
						'template' => '{view}',
						'buttons' => [
							'view' => function ($url, $model) {
								return Yii::$app->user->can('payment.withdrawal.view') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
									'title' => Yii::t('base', 'button_view'),
									'data-pjax' => '0',
									'data-toggle' => 'tooltip',
								]) : '';
							},
						],
					],
				],
			]); ?>
			<?php Pjax::end(); ?>
		
		</div>
	</div>

</div>
