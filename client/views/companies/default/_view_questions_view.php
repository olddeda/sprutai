<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model \common\modules\content\models\Question */

?>

<div class="row item">
	<div class="col-md-12">
		<div class="grid">
			<div class="col width-100 margin-right-10">
				<?= Html::img($model->author->avatar->getImageSrc(100, 100, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle', 'style' => 'display: block; width:100px !important; height:auto;']) ?>
			</div>
			<div class="col width-auto">
				<div class="author">
					<?= Html::a($model->author->getFio(), ['/user/profile/view', 'id' => $model->author->id]) ?>
				</div>
				<div class="text">
				<?php if ($model->title) { ?>
					<div class="margin-top-10"><?= Html::a($model->title,  ['companies/question/view', 'company_id' => $model->company_id, 'id' => $model->id]) ?></div>
				<?php } else { ?>
                    <?= Yii::$app->formatter->asHtml(strip_tags($model->text, 'div, p, b, i, a')) ?>
				<?php } ?>
				</div>
				<?php if (!$model->title) { ?>
				<div class="link">
					<?= Html::a('Перейти к вопросу...', ['companies/question/view', 'company_id' => $model->company_id, 'id' => $model->id]) ?>
				</div>
				<?php } ?>
			</div>
		</div>
		<hr>
	</div>
</div>
