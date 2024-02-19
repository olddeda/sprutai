<?php

use yii\widgets\ListView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $menuModel \common\modules\menu\models\Menu */
/* @var $tagModel \common\modules\tag\models\Tag */
/* @var $subtagModel \common\modules\tag\models\Tag */

$this->context->layoutContent = 'content_no_panel';

$title = $tagModel->title;
if ($subtagModel)
	$title = $subtagModel->title;

$this->title = Html::encode($title);

$this->params['breadcrumbs'][] = ['label' => $menuModel->title, 'url' => ['view', 'id' => $menuModel->id, 'seo' => true]];
if ($subtagModel)
	$this->params['breadcrumbs'][] = ['label' => $tagModel->title, 'url' => ['view', 'id' => $menuModel->id, 'tag' => $tagModel->title, 'seo' => true]];
$this->params['breadcrumbs'][] = Html::encode($title);

?>

<?= $this->render('_tabs', [
	'menuModel' => $menuModel,
	'tagModel' => $tagModel,
	'subtagModel' => $subtagModel,
]) ?>

<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<div class="content-index">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view',
				'layout' => "{items}\n{pager}",
			]); ?>
		</div>
	</div>
	
	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
		<?= $this->render('//banner/view', ['showLeaders' => true]) ?>
		
		<?= $this->render('//author/_top') ?>
	</div>
</div>