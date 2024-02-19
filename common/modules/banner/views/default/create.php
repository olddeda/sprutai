<?php

/* @var $this yii\web\View */
/* @var $model common\modules\banner\models\Banner */

$this->title = Yii::t('banner', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('banner', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="content-article-create">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
