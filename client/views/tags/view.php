<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model common\modules\tag\models\Tag */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type string */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title breadcrumb-no-bottom';

$this->title = Yii::t('tag', 'title_view_'.$type, ['title' => $model->title]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('tag', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

$folder = $type;
if ($type == 'companies')
	$folder = 'companies/default';
if ($type == 'projects')
	$folder = 'projects/default';

?>

<div class="tags-view content-view">
	
	<?= $this->render('_header', [
        'model' => $model,
    ]) ?>

	<div class="margin-top-20 margin-left-20 margin-right-20 margin-bottom-0">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
				
				<div class="content-index">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '//'.$folder.'/_view',
					'layout' => "{items}\n{pager}",
				]); ?>
				</div>
				
			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('//author/_top') ?>
			</div>
		</div>
	</div>
	
</div>
