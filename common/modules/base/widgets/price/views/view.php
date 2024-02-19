<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\modules\base\extensions\select2\Select2;
use common\modules\item\helpers\enum\Currency;

use common\modules\base\widgets\price\PriceWidgetAsset;

PriceWidgetAsset::register($this);

$isRequest = $model->getAttribute($attribute_request);
?>

<div class="price-widget">
	<div class="price-widget-input<?= ($attribute_request ? ' is-request' : '') ?>"<?= ($isRequest) ? ' style="display:none;"' : '' ?>>
		<div class="price-widget-input-price"><?= Html::activeInput('text', $model, $attribute, ['class' => 'form-control']) ?></div>
		<?php if ($attribute_currency) { ?>
		<div class="price-widget-input-currency">
			<?= Select2::widget([
				'model' => $model,
				'attribute' => $attribute_currency,
				'items' => ArrayHelper::merge(['' => ''], Currency::listData()),
				'clientOptions' => [
					'hideSearch' => true,
				],
			]) ?>
		</div>
		<?php } ?>
	</div>
	<?php if ($attribute_request) { ?>
	<div class="price-widget-input-request checkbox-default checkbox-primary">
		<?= Html::activeCheckbox($model, $attribute_request, ['label' => false]) ?>
		<?= Html::activeLabel($model, $attribute_request) ?>
	</div>
	<?php } ?>
</div>
