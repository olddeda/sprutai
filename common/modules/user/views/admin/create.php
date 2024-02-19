<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\modules\user\models\User $user
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-admin', 'create_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="post-create">
	<div class="row margin-top-20">
		<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-body">
					<?= Nav::widget([
						'options' => [
							'class' => 'nav-pills nav-stacked',
						],
						'items' => [
							['label' => Yii::t('user', 'menu_account_details'), 'url' => ['/user/admin/create']],
							['label' => Yii::t('user', 'menu_profile_details'), 'options' => [
								'class' => 'disabled',
								'onclick' => 'return false;',
							]],
							['label' => Yii::t('user', 'menu_information'), 'options' => [
								'class' => 'disabled',
								'onclick' => 'return false;',
							]],
						],
					]) ?>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="alert alert-info">
						<?= Yii::t('user', 'message_credentials_will_be_sent_to_the_user_by_email') ?>.
						<?= Yii::t('user', 'message_a_password_will_be_generated_automatically_if_not_provided') ?>.
					</div>
					<?php $form = ActiveForm::begin([
						'layout' => 'horizontal',
						'enableAjaxValidation' => true,
						'enableClientValidation' => false,
						'fieldConfig' => [
							'horizontalCssClasses' => [
								'wrapper' => 'col-sm-9',
							],
						],
					]); ?>

					<?= $this->render('_user', [
						'form' => $form,
						'user' => $user
					]) ?>

					<div class="form-group">
						<div class="col-lg-offset-3 col-lg-9">
							<?= Html::submitButton(Yii::t('base', 'button_save'), ['class' => 'btn btn-block btn-primary']) ?>
						</div>
					</div>

					<?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
	</div>

</div>

