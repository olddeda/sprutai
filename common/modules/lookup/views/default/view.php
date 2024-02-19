<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use common\modules\base\helpers\enum\Status;

use common\modules\lookup\models\Lookup;

/* @var $this yii\web\View */
/* @var $model common\modules\lookup\models\Lookup */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('lookup', 'title_index'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lookup-view">
	
	<div class="row">
		<div class="col-md-12">
			
			<fieldset>
				<legend><?= Yii::t('lookup', 'header_general') ?></legend>

				<div class="row">
					<div class="col-md-12">
						<?= DetailView::widget([
							'model' => $model,
							'attributes' => [
								[
									'attribute' => 'type',
									'value' => Lookup::types($model->type),
								],
								[
									'attribute' => 'title',
									'format' => 'raw',
								],
								[
									'attribute' => 'sequence',
								],
							],
						]) ?>
					</div>
				</div>
			</fieldset>
			
			<fieldset>
				<legend><?= Yii::t('lookup', 'header_other') ?></legend>
				
				<?= DetailView::widget([
					'model' => $model,
					'attributes' => [
						'id',
						[
							'attribute' => 'status',
							'value' => Status::getLabel($model->status),
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
				<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['update', 'id' => $model->id], [
					'class' => 'btn btn-lg btn-primary'
				]) ?>
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			</div>
			<div class="col-md-4 align-right">
				<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['delete', 'id' => $model->id], [
					'class' => 'btn btn-lg btn-danger',
					'data' => [
						'confirm' => Yii::t('lookup', 'confirm_delete'),
						'method' => 'post',
					],
				]) ?>
			</div>
		</div>
	</div>

</div>
