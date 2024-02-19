<?php

use common\modules\media\helpers\enum\Mode;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\paste\models\Paste */

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<div class="row">
			<div class="col-md-12">
				<div class="grid">
					<div class="col width-100 margin-right-10">
						<?= \yii\bootstrap\Html::img($model->user->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle', 'style' => 'display: block; width:100px !important; height:auto;']) ?>
					</div>
					<div class="col width-auto">
						<div class="author">
							<?= Html::a($model->user->getFio(), ['/user/profile/view', 'id' => $model->user->id],  ['class' => 'inline', 'style' => 'border-bottom: 1px solid #e4eaec; padding-bottom: 5px; margin-bottom: 5px']) ?>
						</div>
						<div>
							<?= Html::a($model->getLink('pastes'), $model->getLink('pastes'), ['class' => 'inline', 'style' => 'font-weight: bold']) ?>
							<?php if ($model->is_private) { ?>
								<?= Html::tag('span', Yii::t('paste', 'private'), ['class' => 'badge badge-danger']) ?>
							<?php } ?>
						</div>
						<?php if ($model->descr) { ?>
							<div class="margin-top-5">
								<i><?= Html::decode($model->descr) ?></i>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="panel-body margin-top-10 padding-top-0" style="position: relative">
		<?= \common\modules\base\extensions\aceeditor\AceEditor::widget([
			'name' => 'paste',
			'value' => Html::decode($model->code),
			'mode' => $model->mode,
			'readOnly' => true,
			'pluginOptions' => [
				'maxLines' => 5,
			],
			'options' => [
				'id' => 'paste'.$model->id,
			],
		]) ?>
	</div>
	
	<div class="panel-footer">
		<div class="form-group margin-0">
			<?= Html::a('Все фрагменты пользователя', ['user', 'user_id' => $model->created_by], [
				'class' => 'btn btn-default btn-sm'
			]) ?>
			<?php if (Yii::$app->user->can('paste.default.update')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-pencil"></span> '.Yii::t('base', 'button_update'), ['/paste/default/update', 'id' => $model->id], [
				'class' => 'btn btn-primary btn-sm'
			]) ?>
			<?php } ?>
			<?php if (Yii::$app->user->can('paste.default.delete')) { ?>
			<?= Html::a('<span class="glyphicon glyphicon-trash"></span> '.Yii::t('base', 'button_delete'), ['/paste/default/delete', 'id' => $model->id], [
				'class' => 'btn btn-danger btn-sm',
				'data' => [
					'confirm' => Yii::t('paste', 'confirm_delete'),
					'method' => 'post',
				],
			]) ?>
			<?php } ?>
		</div>
	</div>
</div>
