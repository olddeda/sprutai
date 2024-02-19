<?php

/* @var $this yii\web\View */
/* @var $model common\modules\shortener\models\Shortener */

$this->title = Yii::t('shortener', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('shortener', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="content-article-create">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
