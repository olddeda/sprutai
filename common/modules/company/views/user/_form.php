<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\web\JsExpression;

use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\select2\Select2;
use common\modules\base\helpers\enum\Status;

use common\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\CompanyUser */
/* @var $company common\modules\company\models\Company */
/* @var $form yii\widgets\ActiveForm */

?>

<?php

$js = <<<JS
	function renderUser(item, escape) {
	    var html  = '';
	    html += '<div class="user-select table-grid">';
	    html += '   <div class="col img">';
	    html += '       <img src="' + item.image + '">'
	    html += '   </div>';
	    html += '   <div class="col-auto">';
	    html += '       <div class="fio">' + item.fio + '</div>';
	    html += '       <div class="info">';
	    html += '           <div class="info-item"><i class="fa fa-user" aria-hidden="true"></i> ' + item.username + '</div>';
	    html += '           <div class="info-item"><i class="fa fa-envelope" aria-hidden="true"></i> ' + item.email + '</div>';
	    if (item.phone) {
	    	 html += '           <div class="info-item"><i class="fa fa-phone" aria-hidden="true"></i> ' + item.phone + '</div>';
	    }
	     if (item.telegram) {
	    	 html += '           <div class="info-item"><i class="fa fa-telegram" aria-hidden="true"></i> ' + item.telegram + '</div>';
	    }
	    html += '       </div>';
	    html += '   </div>';
	    html += '</div>';
	    return html;
	}
JS;
$this->registerJs($js, View::POS_END);

?>

<div class="company-user-form">

    <?php $form = ActiveForm::begin([
		'id' => 'company-user-form',
		'enableAjaxValidation' => false,
	]); ?>

	<fieldset>
		<legend><?= Yii::t('company-user', 'header_general') ?></legend>
		
		<?= $form->field($model, 'user_id')->widget(Selectize::class, [
			'items' => [],
			'url' => Url::to(['search', 'company_id' => $company->id]),
			'searchField' => 'q',
			'pluginOptions' => [
				'valueField' => 'id',
				'labelField' => 'fio',
				'searchField' => ['fio', 'username', 'email', 'phone', 'telegram'],
				'create' => false,
				'options' => [],
				'render' => [
					'option' => new JsExpression('renderUser'),
				],
			],
		]) ?>
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('company-user', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData(),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('company.user.index')) { ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'company_id' => $company->id], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
		<?php } ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
