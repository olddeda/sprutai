<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

use common\modules\payment\helpers\enum\StatusWithdrawal;

/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\Payment */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('payment-withdrawal', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('payment-withdrawal', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="content-article-view">
	
	<div class="row margin-top-20">
		<div class="col-md-12">

			<fieldset>
				<legend><?= Yii::t('payment', 'header_general') ?></legend>

				<div class="row margin-top-15">
					<div class="col-md-12">
						<?= DetailView::widget([
							'model' => $model,
							'options' => [
								'class' => 'table table-striped detail-view'
							],
							'attributes' => [
								[
									'attribute' => 'id'
								],
								[
									'attribute' => 'datetime',
								],
								[
									'attribute' => 'title',
									'format' => 'raw',
								],
								[
									'attribute' => 'price',
									'format' => 'currency',
								],
								[
									'attribute' => 'price_tax',
									'format' => 'currency',
								],
								[
									'attribute' => 'tax',
									'value' => (int)$model->tax.'%',
								],
								[
									'attribute' => 'status',
									'value' => StatusWithdrawal::getLabel($model->status),
								],
							],
						]) ?>
					</div>
				</div>
			</fieldset>

			<fieldset>
				<legend><?= Yii::t('payment', 'header_user') ?></legend>

				<div class="row margin-top-15">
					<div class="col-md-12">
						<?= DetailView::widget([
							'model' => $model,
							'options' => [
								'class' => 'table table-striped detail-view'
							],
							'attributes' => [
								[
									'attribute' => 'id'
								],
								[
									'label' => Yii::t('payment', 'field_user_fio'),
									'value' => $model->user->getFio(),
								],
								[
									'label' => Yii::t('payment', 'field_user_email'),
									'value' => $model->user->email,
								],
								[
									'label' => Yii::t('payment', 'field_user_phone'),
									'value' => $model->user->profile->phone,
								],
								[
									'label' => Yii::t('payment', 'field_user_telegram'),
									'value' => ($model->user->telegram) ? Html::a('@'.$model->user->telegram->username, 'tg://resolve?domain='.$model->user->telegram->username) : '-',
									'format' => 'raw',
								],
							],
						]) ?>
					</div>
				</div>
			</fieldset>

			<?php if ($dataProvider->totalCount) { ?>
			<fieldset>
				<legend><?= Yii::t('payment', 'header_accruals') ?></legend>

				<div class="row">
					<div class="col-md-12">
						
						<?= GridView::widget([
							'dataProvider' => $dataProvider,
							'filterModel' => null,
							'tableOptions' => [
								'class' => 'table table-striped'
							],
							'summary' => false,
							'columns' => [
								
								// ID
								[
									'header' => Yii::t('payment', 'field_id'),
									'value' => function ($data) {
										return $data->paymentSource->id;
									},
									'headerOptions' => ['width' => 70],
								],
								
								// Date
								[
									'header' => Yii::t('payment', 'field_datetime'),
									'value' => function ($data) {
										return $data->paymentSource->datetime;
									},
									'headerOptions' => ['width' => 150],
								],
								
								// Price
								[
									'header' => Yii::t('payment', 'field_price'),
									'value' => function ($data) {
										return Yii::$app->formatter->asCurrency($data->paymentSource->price);
									},
									'headerOptions' => ['width' => 150],
								],
								
								// Title
								[
									'header' => Yii::t('payment', 'field_title'),
									'value' => function ($data) {
										return $data->paymentSource->title;
									}
								],
								
								// Price
								[
									'header' => Yii::t('payment', 'field_user_id'),
									'value' => function ($data) {
										return $data->paymentSource->user->getFio();
									}
								],
							],
						]); ?>
						
					</div>
				</div>
			</fieldset>
			<?php } ?>
			
		</div>
	</div>

	<div class="form-group margin-top-30">
		<div class="row">
			<div class="col-md-8">
				<?php if (Yii::$app->user->can('payment.withdrawal.index')) { ?>
					<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
						'class' => 'btn btn-default btn-lg'
					]) ?>
				<?php } ?>
			</div>
		</div>
	</div>

</div>
