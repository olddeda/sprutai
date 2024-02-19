<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\base\helpers\enum\Status;
use common\modules\base\extensions\select2\Select2;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\tag\models\Tag;

use common\modules\menu\models\Menu;
use common\modules\menu\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $model common\modules\menu\models\Menu */
/* @var $form yii\widgets\ActiveForm */

?>

<?php

$js = <<<JS
$('#menu-type').change(function() {
	let val = $(this).val();
	if (val == 1) {
	    $('#menu-type-title').slideUp();
	    $('#menu-type-tag').slideDown();
	}
	else {
	    $('#menu-type-title').slideDown();
	    $('#menu-type-tag').slideUp();
	}
})
JS;
$this->registerJs($js);


?>

<div class="content-news-form">
	
	<?php $form = ActiveForm::begin(); ?>
	
	<fieldset>
		<legend><?= Yii::t('menu', 'header_general') ?></legend>
		
		<?= $form->field($model, 'type')->widget(Select2::class, [
			'items' => Type::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
		<div id="menu-type-title" style="display: <?= $model->type == Type::TITLE ? 'block': 'none' ?>">
			<?= $form->field($model, 'title') ?>
		</div>
		
		<div id="menu-type-tag" style="display: <?= $model->type == Type::TAG ? 'block': 'none' ?>">
			<?= $form->field($model, 'tag_id')->widget(Select2::class, [
				'items' => Tag::listData(),
				'clientOptions' => [
					'hideSearch' => false,
				]
			]) ?>
		</div>
		
		<?= $form->field($model, 'visible')->widget(Select2::class, [
			'items' => \common\modules\base\helpers\enum\Boolean::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
	</fieldset>
	
	<fieldset>
		<legend><?= Yii::t('menu', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
		
		<?= $form->field($model, 'sequence') ?>
		
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('menu', 'header_seo') ?></legend>
		
		<?= SeoFormWidget::widget([
			'model' => $model,
			'form' => $form,
		]); ?>
	</fieldset>
	
	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('menu.default.index')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
				'class' => 'btn btn-default btn-lg'
			]) ?>
		<?php } ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>

