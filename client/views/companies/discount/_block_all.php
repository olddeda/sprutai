<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\company\models\CompanyDiscount;

$discounts = CompanyDiscount::findActive()->groupBy('company_id')->limit(5)->all();
?>

<?php if ($discounts) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="margin-0 text-primary"><?= Yii::t('company', 'block_discount') ?></h4>
		</div>
		<div class="panel-body items">
			<?php foreach ($discounts as $item) { ?>
				<?= $this->render('_block_item', ['model' => $item]) ?>
			<?php } ?>
			<div class="align-center margin-top-25 margin-bottom-10">
				<?= Html::a(Yii::t('company-discount', 'link_list_all'), Url::to(['/companies/discount/index']), [
					'class' => 'btn btn-large btn-primary',
				]) ?>
			</div>
		</div>
	</div>
<?php } ?>