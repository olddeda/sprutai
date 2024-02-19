<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('user-profile', 'title_settings_account');

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
                <?php $form = ActiveForm::begin([
                    'id' => 'account-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-lg-3 control-label'],
                    ],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'new_password')->passwordInput() ?>

                <hr />

                <?= $form->field($model, 'current_password')->passwordInput() ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= Html::submitButton(Yii::t('user-profile', 'button_save'), ['class' => 'btn btn-block btn-primary']) ?><br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
