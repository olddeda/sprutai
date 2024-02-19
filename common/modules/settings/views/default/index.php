<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\widgets\Pjax;

use common\modules\base\helpers\enum\Status;
use common\modules\settings\helpers\enum\Type;
use common\modules\settings\models\Settings;

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel \common\modules\settings\models\search\SettingSearch */

$this->title = Yii::t('settings', 'title');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="setting-index">
    
    <?php Pjax::begin([
		'timeout' => 7000,
		'enablePushState' => false
	]); ?>

	<?= GridView::widget([
		'tableOptions' => [
			'class' => 'table table-striped'
		],
		'dataProvider' => $dataProvider,
		'filterModel' => $searchModel,
		'columns' => [
			[
				'attribute' => 'id',
				'headerOptions' => ['width' => '70'],
			],
			[
				'attribute' => 'type',
				'filter' => Type::listData(),
				'filterInputOptions' => ['class' => 'form-control'],
				'headerOptions' => ['width' => '150'],
			],
			[
				'attribute' => 'section',
				'filter' => ArrayHelper::map(Settings::find()->select('section')->distinct()->all(), 'section', 'section'),
				'filterInputOptions' => ['class' => 'form-control'],
				'headerOptions' => ['width' => '150'],
			],
			[
				'attribute' => 'key',
				'headerOptions' => ['width' => '200'],
			],
			'value:ntext',
			[
				'attribute' => 'descr',
			],
			[
				'attribute' => 'status',
				'value' => function ($model) {
					return Status::getLabel($model->status);
				},
				'filter' => Status::listData(),
				'filterInputOptions' => ['class' => 'form-control'],
				'headerOptions' => ['width' => '150'],
			],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{update} {delete}',
				'headerOptions' => ['width' => '50'],
			],
		],
	]); ?>
    <?php Pjax::end(); ?>

	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>

</div>
