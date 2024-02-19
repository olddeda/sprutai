<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

use common\modules\base\extensions\select2\Select2;

use common\modules\base\extensions\datetimepicker\DateTimePicker;

use common\modules\payment\helpers\enum\Status;

use common\modules\user\models\User;

use kartik\number\NumberControl;


/* @var $this yii\web\View */
/* @var $model common\modules\payment\models\Payment */
/* @var $project common\modules\project\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<?php

$phisycals = [];
foreach ($project->paymentTypes as $t)
    $phisycals[$t->id] = $t->physical;
$phisycals = json_encode($phisycals);

$js = <<<JS
    var physicals = JSON.parse('$phisycals');
    $(document).ready(function() {
        $('#payment-payment_type_id').change(function() {
            var val = $(this).val();
            if (physicals[val])
            	$('#field-pickup').slideDown();
            else
            	$('#field-pickup').slideUp();
        });
    });
JS;

$this->registerJs($js);
?>


<div class="page-form">

    <?php $form = ActiveForm::begin([
		'id' => 'page-form',
		'enableAjaxValidation' => false,
	]); ?>

	<fieldset>
		<legend><?= Yii::t('project-payer', 'header_general') ?></legend>

		<div class="row">
			<div class="col-md-12">
                
                <?= $form->field($model, 'datetime')->widget(DateTimePicker::class, [
                    'template' => '{input}',
                    'pickButtonIcon' => 'glyphicon glyphicon-calendar',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayBtn' => true,
                    ],
                ]);?>

                <?= $form->field($model, 'user_id')->widget(Select2::class, [
                    'items' => ArrayHelper::map(User::find()->joinWith('profile')->all(), 'id', function($data) {
                        $tmp = [];
                        $tmp[] = $data->getFio().':';
                        $tmp[] = '<i class="fa fa-user" aria-hidden="true"></i> '.$data->username;
                        $tmp[] = '<i class="fa fa-envelope" aria-hidden="true"></i> '.$data->email;
                        if ($data->telegram && $data->telegram->username)
	                        $tmp[] = '<i class="fa fa-telegram" aria-hidden="true"></i> '.$data->telegram->username;
	                    if ($data->github && $data->github->username)
		                    $tmp[] = '<i class="fa fa-github" aria-hidden="true"></i> '.$data->github->username;
                        return implode("&nbsp;&nbsp;", $tmp);
                    }),
                    'options' => [
                        'prompt' => Yii::t('project-payer', 'placeholder_user_id'),
                    ],
                    'clientOptions' => [
                        'hideSearch' => false,
	                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    ]
                ]) ?>

                <?= $form->field($model, 'payment_type_id')->widget(Select2::class, [
                    'items' => ArrayHelper::map($project->paymentTypes, 'id', 'title'),
                    'options' => [
                        'prompt' => Yii::t('project-payer', 'placeholder_payment_type_id'),
                    ],
                    'clientOptions' => [
                        'hideSearch' => true,
                    ]
                ]) ?>

                <?= $form->field($model, 'price')->widget(NumberControl::class, [
                    'maskedInputOptions' => [
                        'suffix' => ' ₽',
                        'digits' => 0,
                        'groupSeparator' => ' ',
                        'rightAlign' => false,
                    ],
                    'displayOptions' => [
                        'placeholder' => Yii::t('project-payer', 'placeholder_price'),
                    ],
                ]) ?>
				
				<?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

                <div id="field-pickup" style="display: <?= ($model->type && $model->type->physical ? 'block' : 'none') ?>">
	                <?= $form->field($model, 'pickup')->checkbox() ?>
                </div>
				
			</div>
		</div>
	</fieldset>

	<fieldset>
		<legend><?= Yii::t('project-payer', 'header_other') ?></legend>
		
		<?= $form->field($model, 'status')->widget(Select2::class, [
			'items' => Status::listData([], [Status::WAIT, Status::FAILED]),
			'clientOptions' => [
				'hideSearch' => true,
			]
		]) ?>
	</fieldset>

	<div class="form-group margin-top-30">
		<?= Html::submitButton('<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')), [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
		<?php if (Yii::$app->user->can('project.payer.index')) { ?>
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'project_id' => $project->id], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
		<?php } ?>
	</div>

    <?php ActiveForm::end(); ?>

</div>
