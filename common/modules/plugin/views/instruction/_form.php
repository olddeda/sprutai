<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Json;

use kartik\number\NumberControl;

use common\modules\base\extensions\select2\Select2;
use common\modules\base\extensions\selectize\Selectize;
use common\modules\base\extensions\contentbuilder\ContentBuilder;

use common\modules\rbac\helpers\enum\Role;

use common\modules\media\helpers\enum\Mode;

use common\modules\seo\widgets\SeoFormWidget;

use common\modules\payment\models\PaymentType;

use common\modules\content\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\plugin */
/* @var $form yii\widgets\ActiveForm */

?>

<?= $this->render('@common/modules/content/views/_contentbuilder_js.php', [
	'model' => $model,
	'field' => 'text',
	'validateTag' => false,
	'validateSpecial' => false,
]) ?>

<div class="plugin-form margin-20">
	
	<?php $form = ActiveForm::begin(['id' => 'form-content', 'options' => ['enctype' => 'multipart/form-data']]); ?>

	<div class="panel panel-default">
		<div class="panel-body">

			<div class="container container-fluid is-container contentbuilder">
				
				<?= $form->field($model, 'text')->widget(ContentBuilder::class, [
					'pluginOptions' => [
						'sourceEditor' => Yii::$app->user->getIsAdmin(),
						'buttons' => Yii::$app->user->getIsAdmin() ?
							['bold', 'italic', 'formatting', 'textsettings', 'color', 'font', 'formatPara', 'align', 'list', 'table', 'image', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat', 'html'] :
							['bold', 'italic', 'formatting', 'align', 'list', 'table', 'createLink', 'unlink', 'icon', 'tags', 'removeFormat'],
						'content' => $model->text,
						''
					],
				])->label(false) ?>

				<div class="form-group margin-top-40 align-center">
					<div class="btn-group btn-group-lg">
						<?php $content = '<span class="glyphicon glyphicon-ok"></span> '.($model->isNewRecord ? Yii::t('base', 'button_create') : Yii::t('base', 'button_save')); ?>
						<?= Html::button($content, [
							'id' => 'button-content-submit',
							'class' => 'btn btn-primary btn-lg',
							'data-title-original' => $content,
							'data-title-wait' => '<span class="glyphicon glyphicon-time"></span> '.Yii::t('base', 'button_wait'),
						]) ?>
					</div>
					<?php if (Yii::$app->user->can('plugin.instruction.index')) { ?>
						<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index', 'plugin_id' => $this->context->pluginModel->id], [
							'class' => 'btn btn-default btn-lg'
						]) ?>
					<?php } ?>
				</div>

			</div>

		</div>
	</div>

    <?php ActiveForm::end(); ?>

</div>
