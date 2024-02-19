<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $menu common\modules\menu\models\Menu */
/* @var $model common\modules\menu\models\MenuItem */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="menu-item-form">
	
	<?php $form = ActiveForm::begin(); ?>
	
	<fieldset>
		<legend><?= Yii::t('menu', 'header_general') ?></legend>
		
		<div class="grid">
			<div class="col width-350">
				<?= $model->image->uploaderImageSlim([
					'settings' => [
						'size' => [
							'width' => 1000,
							'height' => 1000,
						],
					],
					'format' => Mode::CROP_CENTER,
				]); ?>
			</div>
			<div class="col width-auto">
				<?= $form->field($model, 'title') ?>
				<?= $form->field($model, 'url') ?>
				<?= $form->field($model, 'sequence') ?>
				<? //= $form->field($model, 'descr')->textarea(['rows' => 7, 'value' => html_entity_decode($model->descr)]) ?>
				
			</div>
		</div>
		
	</fieldset>
	
	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['/menu/default/view', 'id' => $menu->id], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
	</div>
	
	<?php ActiveForm::end(); ?>

</div>

