<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tagModel common\modules\tag\models\Tag */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('content-news', 'title').' - '.$tagModel->title;

$this->params['breadcrumbs'][] = ['url' => Url::toRoute('news/index'), 'label' => Yii::t('content-news', 'title')];
$this->params['breadcrumbs'][] = Html::encode($this->title);

?>

<div class="form-group margin-bottom-20">
	<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('news', 'button_add'), ['/content/news/create'], [
		'class' => 'btn btn-primary btn-lg'
	]) ?>
</div>

<div class="content-index">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view',
		'layout' => "{items}\n{pager}",
	
	]); ?>
</div>

<div class="form-group margin-top-20">
<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('news', 'button_add'), ['/content/news/create'], [
		'class' => 'btn btn-primary btn-lg'
	]) ?>
</div>

