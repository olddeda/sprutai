<?php

use yii\bootstrap\ActiveForm;

use yii\helpers\Html;

use common\modules\base\extensions\phoneInput\PhoneInput;

/**
 * @var yii\web\View $this
 * @var common\modules\user\models\User $user
 * @var common\modules\user\models\UserProfile $profile
 */
?>

<?php $this->beginContent('@common/modules/user/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
			'label' => 'col-sm-2',
            'wrapper' => 'col-sm-10',
       ],
    ],
]); ?>

<div class="row">
	<div class="col-sm-3">
		<?= $user->uploaderAvatar([
			'width' => 170,
			'height' => 170,
		]); ?>
	</div>
	<div class="col-sm-9">
		<?= $form->field($profile, 'last_name') ?>
		<?= $form->field($profile, 'first_name') ?>
		<?= $form->field($profile, 'middle_name') ?>
		<?= $form->field($profile, 'phone')->widget(PhoneInput::className(), [
			'auto' => true,
			'defaultOptions' => [
				'class' => 'form-control',
			],
			'jsOptions' => [
				'nationalMode' => false,
				'autoHideDialCode' => false,
				'preferredCountries' => ['ru'],
			],
		]) ?>

		<div class="form-group">
			<div class="col-lg-offset-2 col-lg-10">
				<?= Html::submitButton(Yii::t('user', 'button_update'), ['class' => 'btn btn-block btn-primary']) ?>
			</div>
		</div>
	</div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
