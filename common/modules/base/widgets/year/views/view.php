<?php
use yii\helpers\Html;

use common\modules\base\widgets\year\YearWidgetAsset;

YearWidgetAsset::register($this);

?>

<div class="year-widget">
	<div class="row">
		<div class="col-sm-6">
			<?= Html::activeInput('text', $model, $from, ['class' => 'form-control', 'placeholder' => $placeholder_from, 'maxlength' => 4]) ?>
		</div>
		<div class="col-sm-6">
			<?= Html::activeInput('text', $model, $to, ['class' => 'form-control', 'placeholder' => $placeholder_to, 'maxlength' => 4]) ?>
		</div>
	</div>
</div>
