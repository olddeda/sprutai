<?php

use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\extensions\selectize\Selectize;

use common\modules\rbac\helpers\enum\Type;
use common\modules\rbac\models\forms\AssignForm;

?>

<fieldset>
	<legend><?= Yii::t('rbac', 'header_parents') ?></legend>
	
	<?php Pjax::begin([
		'timeout' => 10000,
		'enablePushState' => false
	]); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'layout' => "{items}\n{pager}",
		'tableOptions' => [
			'class' => 'table table-striped'
		],
		'columns' => [
			[
				'attribute' => 'name',
				'label' => Yii::t('rbac', 'field_name'),
				'value' => function($data) use($model) {
					return Html::a($data['name'], [
						'/rbac/'.Type::getItem($data['type']).'/view',
						'name' => $data['name']
					], [
						'title' => Yii::t('rbac-'.Type::getItem($data['type']), 'tooltip_name'),
						'data-pjax' => '0',
						'data-toggle' => 'tooltip',
					]);
				},
				'options' => [
					'style' => 'width: 20%'
				],
				'format' => 'raw',
			],
			[
				'attribute' => 'description',
				'label' => Yii::t('rbac', 'field_description'),
				'value' => function($data) use($model) {
					return Html::a($data['description'], [
						'/rbac/'.Type::getItem($data['type']).'/view',
						'name' => $data['name']
					], [
						'title' => Yii::t('rbac-'.Type::getItem($data['type']), 'tooltip_name'),
						'data-pjax' => '0',
						'data-toggle' => 'tooltip',
					]);
				},
				'options' => [
					'style' => 'width: 65%'
				],
				'format' => 'raw',
			],
			[
				'attribute' => 'type',
				'label' => Yii::t('rbac', 'field_type'),
				'value' => function($data) use($model) {
					return Html::a(Type::getLabel($data['type']), [
						'/rbac/'.Type::getItem($data['type']).'/view',
						'name' => $data['name']
					], [
						'title' => Yii::t('rbac-'.Type::getItem($data['type']), 'tooltip_name'),
						'data-pjax' => '0',
						'data-toggle' => 'tooltip',
					]);
				},
				'options' => [
					'style' => 'width: 10%'
				],
				'format' => 'raw',
			],
			[
				'class' => ActionColumn::className(),
				'template' => '{view} {revoke}',
				'buttons' => [
					'view' => function ($url, $data) use($model) {
						return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', [
							'/rbac/'.Type::getItem($data['type']).'/view',
							'name' => $data['name'],
						], [
							'title' => Yii::t('rbac', 'button_view'),
							'data-pjax' => '0',
							'data-toggle' => 'tooltip',
						]);
					},
					'revoke' => function ($url, $data) use($model) {
						return Html::a('<span class="glyphicon glyphicon-trash"></span>', [
							'/rbac/'.$model->typeName.'/revoke',
							'name' => $model->name,
							'parent' => $data['name'],
						], [
							'title' => Yii::t('rbac', 'button_revoke'),
							'data-method' => 'POST',
							'data-confirm' => Yii::t('rbac-'.Type::getItem($data['type']), 'confirm_revoke_name', ['title' => $data['name']]),
							'data-pjax' => '1',
							'data-toggle' => 'tooltip',
						]);
					},
				],
				'options' => [
					'style' => 'width: 5%'
				],
			]
		],
	]) ?>
	<?php Pjax::end(); ?>
</fieldset>

<?php

// Create assign form
$assignModel = new AssignForm;
$assignModel->type = AssignForm::TYPE_PARENT;
$assignModel->name = $model->name;

?>

<div class="rbac-assign">

	<?php $form = ActiveForm::begin([
		'enableClientValidation' => true,
		'enableAjaxValidation' => false,
		'action' => [
			'/rbac/'.$model->typeName.'/assign',
			'name' => $model->name,
		],
	]) ?>

	<fieldset>
		<legend><?= Yii::t('rbac', 'header_parents_assign') ?></legend>
		
		<?= Html::activeHiddenInput($assignModel, 'type') ?>

		<div class="row form-group">
			<div class="col-md-10">
				<?= $form->field($assignModel, 'parent', ['options' => ['class' => 'required']])->widget(Selectize::className(), [
					'items' => $model->getTreeParents(),
					'pluginOptions' => [
						'persist' => false,
						'createOnBlur' => false,
						'create' => false,
					]
				])->label(false); ?>
			</div>
			<div class="col-md-2">
				<?= Html::submitButton(Yii::t('rbac', 'button_assign'), [
					'class' => 'btn btn-block btn-primary'
				]) ?>
			</div>
		</div>
	</fieldset>

	<?php ActiveForm::end() ?>
</div>
