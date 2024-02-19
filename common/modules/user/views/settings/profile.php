<?php

use common\modules\base\extensions\select2\Select2;
use common\modules\user\helpers\enum\WalletType;
use yii\helpers\Html;

use common\modules\base\extensions\phoneInput\PhoneInput;
use common\modules\media\helpers\enum\Mode;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var common\modules\user\models\UserProfile $profile
 */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-profile', 'title_settings_profile');

$this->params['breadcrumbs'][] = ['label' => Yii::t('user-profile', 'title'), 'url' => ['/user/profile']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'profile-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnBlur' => false,
                ]); ?>
				
				<div class="row">
					<div class="col-sm-3">
						<?= $model->user->uploaderAvatar([
							'width' => 170,
							'height' => 170,
							'format' => Mode::CROP_CENTER,
						]); ?>
					</div>
					<div class="col-sm-9">
						
						<?= $form->field($model, 'last_name') ?>
						<?= $form->field($model, 'first_name') ?>
						<?= $form->field($model, 'middle_name') ?>
						
						<?= $form->field($model, 'phone')->widget(PhoneInput::class, [
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

						<div class="form-group field-userprofile-wallet">
							<label class="col-lg-3 control-label" for="userprofile-last_name">
								<?= Yii::t('user-profile', 'field_wallet') ?>
							</label>
							<div class="col-lg-9">
								<div class="row">
									<div class="col-md-5">
										<?= Select2::widget([
											'model' => $model,
											'attribute' => 'wallet_type',
                                            'items' => WalletType::listData(),
                                            'options' => [
                                                'prompt' => Yii::t('user-profile', 'prompt_wallet_type'),
                                            ],
                                            'clientOptions' => [
                                                'hideSearch' => true,
                                            ],
										]) ?>
									</div>
									<div class="col-md-7">
                                        <?= Html::activeTextInput($model, 'wallet_number', [
                                        	'class' => 'form-control',
	                                        'placeholder' => Yii::t('user-profile', 'placeholder_wallet_number')
                                        ]) ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= \yii\helpers\Html::submitButton(Yii::t('user-profile', 'button_save'), ['class' => 'btn btn-block btn-primary']) ?><br>
                    </div>
                </div>

                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
