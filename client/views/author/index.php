<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('author', 'title_top');

$this->params['breadcrumbs'][] = $this->title;

$bannerCount = \common\modules\banner\models\Banner::countActive();
?>


<?php if ($bannerCount) { ?>
<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<?= $this->render('_index', [
			'dataProvider' => $dataProvider,
		]) ?>
	</div>
	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3" style="padding-top: 65px">
		<?= $this->render('//banner/view', ['showLeaders' => true]) ?>
	</div>
</div>

<?php } else { ?>
	
<?= $this->render('_index', [
	'dataProvider' => $dataProvider,
]) ?>
	
<?php } ?>


