<?php

use yii\helpers\Html;

use common\modules\base\components\Debug;

use common\modules\base\extensions\select2\Select2;

use common\modules\user\helpers\enum\Subscribe;


/*
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-profile', 'title_subscribe');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ['/user/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-settings-networks">
	<div class="row">
		<div class="col-md-3">
			<?= $this->render('_menu') ?>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-body">
					<?php $form = \yii\widgets\ActiveForm::begin([
						'id' => 'subscribe-form',
						'options' => ['class' => 'form-horizontal'],
						'fieldConfig' => [
							'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
							'labelOptions' => ['class' => 'col-lg-3 control-label'],
						],
						'enableAjaxValidation' => true,
						'enableClientValidation' => false,
						'validateOnBlur' => false,
					]); ?>
					
					<?= $form->field($model, 'flag_system')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_author')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_article')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_news')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_project')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_blog')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_plugin')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_comment')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>
					
					<?= $form->field($model, 'flag_vote')->widget(Select2::class, [
						'items' => Subscribe::listData(),
						'clientOptions' => [
							'hideSearch' => true,
						]
					]) ?>

					<div class="form-group">
						<div class="col-lg-offset-3 col-lg-9">
							<?= Html::submitButton(Yii::t('user-subscribe', 'button_save'), ['class' => 'btn btn-block btn-primary']) ?><br>
						</div>
					</div>
					
					<?php \yii\widgets\ActiveForm::end(); ?>
					
				</div>
			</div>
		</div>
	</div>
</div>
