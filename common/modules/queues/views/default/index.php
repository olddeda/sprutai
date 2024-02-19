<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use common\modules\base\extensions\editable\EditableColumn;
use common\modules\base\extensions\select2\Select2;

use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\content\models\Project;
use common\modules\content\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = Yii::t('queues', 'title');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="queues-default-index">
	
	<div class="row">
		<div class="col-md-12">
			<?php Pjax::begin([
				'timeout' => 10000,
				'enablePushState' => true
			]); ?>
			
			<?= GridView::widget([
				'dataProvider' => $dataProvider,
				'tableOptions' => [
					'class' => 'table table-striped'
				],
				'layout' => '{items}',
				'columns' => [

					[
						'attribute' => 'name',
						'header' => Yii::t('queues', 'field_name'),
					],
					[
						'class' => 'yii\grid\ActionColumn',
						'headerOptions' => [
							'width' => '100',
							'style' => 'text-align:center;'
						],
						'template' => '{run}',
						'buttons' => [
							'run' => function ($url, $model) {
								return Yii::$app->user->can('queues.default.run') ? Html::a(Yii::t('queues', 'button_run'), $url, [
									'class' => 'btn',
									'data-method' => 'POST',
									'data-confirm' => Yii::t('queues', 'confirm_run_name', ['title' => $model['name']]),
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
