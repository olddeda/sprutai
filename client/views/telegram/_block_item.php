<?php

use yii\helpers\Html;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramChat */

?>

<div class="item">
	<div class="grid">
		<div class="col width-70 padding-right-20">
			<?= Html::img($model->logo->getImageSrc(70, 70, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle', 'style' => 'display: block; width:100px !important; height:auto;']) ?>
		</div>
		<div class="col width-auto align-middle">
			<div class="author">
				<noindex>
					<?= Html::a($model->title, 'tg://resolve?domain='.$model->username, ['target' => '_blank']) ?>
				</noindex>
			</div>
		</div>
	</div>
	<hr/>
</div>