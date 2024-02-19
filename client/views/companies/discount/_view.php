<?php

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\CompanyDiscount */

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\media\helpers\enum\Mode;

?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="item">
			<div class="col width-70 padding-right-20">
				<?= Html::img($model->company->logo->getImageSrc(70, 70, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
			</div>
			<div class="col width-auto">
				<div class="author margin-bottom-10">
					<h4 class="margin-0"><?= Html::a($model->company->title, Url::to(['companies/default/view', 'id' => $model->company->id])) ?></h4>
					<?php if ($model->company->site) { ?>
						<div class="margin-top-5"><?= Html::a($model->company->site, $model->company->site, ['target' => '_blank']) ?></div>
					<?php } ?>
					<div class="clearfix margin-top-5">
						<div class="pull-left padding-right-10"><b><?= $model->getAttributeLabel('promocode') ?>:</b></div>
						<div class="pull-left"><?= $model->promocode ?></div>
					</div>
					<div class="clearfix">
						<div class="pull-left padding-right-10"><b><?= $model->getAttributeLabel('discount') ?>:</b></div>
						<div class="pull-left"><?= $model->getDiscount_all() ?></div>
					</div>
					<?php if ($model->descr) { ?>
						<div class="margin-top-5">
							<i><?= Yii::$app->formatter->asNtext($model->descr) ?></i>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
