<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\eav\models\EavAttributeType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="eav-attribute-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'handlerClass')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'storeType')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ?Yii::t('eav', 'Create') : Yii::t('eav','Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
