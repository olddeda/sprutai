<?php

use yii\widgets\DetailView;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\Company */

?>

<?php if (count($model->discounts)) { ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="margin-0 text-primary"><?= Yii::t('company', 'block_discount') ?></h4>
	</div>
	<div class="panel-body items">
	<?php foreach ($model->discounts as $discount) { ?>
		<div class="item">
			<div class="clearfix">
				<div class="pull-left padding-right-10"><b><?= $discount->getAttributeLabel('promocode') ?>:</b></div>
				<div class="pull-left"><?= $discount->promocode ?></div>
			</div>
			<div class="clearfix">
				<div class="pull-left padding-right-10"><b><?= $discount->getAttributeLabel('discount') ?>:</b></div>
				<div class="pull-left"><?= Yii::$app->formatter->asPercent($discount->discount / 100) ?></div>
			</div>
			<?php if ($discount->descr) { ?>
			<div class="margin-top-5">
				<i><?= Yii::$app->formatter->asNtext($discount->descr) ?></i>
			</div>
			<?php } ?>
			<hr/>
		</div>
	<?php } ?>
	</div>
</div>
<?php } ?>