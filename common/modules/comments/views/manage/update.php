<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

use common\modules\base\helpers\enum\ModuleType;

use common\modules\comments\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \common\modules\comments\models\Comment */

$this->title = Yii::t('comments', 'update_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('comments', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('comments', 'update_title');
?>

<div class="comment-update">

    <div class="comment-form">

        <?php $form = ActiveForm::begin(); ?>

		<fieldset>
			<legend><?= Yii::t('content', 'header_general') ?></legend>
	
			<?= $form->field($model, 'content')->widget(common\modules\base\extensions\imperavi\Widget::class, [
				'settings' => [
					'lang' => 'ru',
					'toolbar' => true,
					'focus' => true,
					'buttons' => ['bold', 'italic', 'ul', 'ol', 'link', 'image'],
					'minHeight' => 300,
					'imageUpload' => Url::to([
						'/media/imperavi/upload',
						'module_type' => ModuleType::COMMENT,
						'module_id' => $model->id,
					]),
					'imageManagerJson' => Url::to([
						'/media/imperavi/index',
						'module_type' => ModuleType::COMMENT,
					]),
					'plugins' => [
						'imagemanager',
					],
				],
			])->label(false) ?>

		</fieldset>
		
		<fieldset>
			<legend><?= Yii::t('content', 'header_other') ?></legend>
        	<?= $form->field($model, 'status')->dropDownList(Status::listData()); ?>
		</fieldset>

		<div class="form-group margin-top-30">
			<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
				'class' => $model->isNewRecord ? 'btn btn-success btn-lg' : 'btn btn-primary btn-lg'
			]) ?>
			<?php if (Yii::$app->user->can('comments.manage.index')) { ?>
				<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
					'class' => 'btn btn-default btn-lg'
				]) ?>
			<?php } ?>
		</div>
		
        <?php ActiveForm::end(); ?>
    </div>
</div>
