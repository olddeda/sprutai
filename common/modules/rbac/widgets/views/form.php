<?php

use kartik\select2\Select2;
use yii\bootstrap\Alert;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\modules\rbac\models\Assignment;

/**
 * @var $model Assignment
 */
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation'  => false,
]) ?>

<?= Html::activeHiddenInput($model, 'user_id') ?>

<?= $form->field($model, 'items')->widget(Select2::className(), [
    'data' => $model->getAvailableItems(),
    'options' => [
        'id' => 'items',
        'multiple' => true
    ],
]) ?>

<?= Html::submitButton(Yii::t('rbac-assignment', 'button_update_assignments'), [
	'class' => 'btn btn-primary btn-block'
]) ?>

<?php ActiveForm::end() ?>

