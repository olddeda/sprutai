<?php

/* @var $this yii\web\View */
/* @var $model common\modules\notify\forms\NotifyForm */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = Yii::t('notification', 'title_send');

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="notification-send">
	<?php $form = ActiveForm::begin(['id' => 'notify-form']); ?>
	
	<?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>
	
	<div class="form-group">
		<?= Html::submitButton(Yii::t('notification-form', 'button_submit'), ['class' => 'btn btn-primary']) ?>
	</div>
	
	<?php ActiveForm::end(); ?>
</div>
