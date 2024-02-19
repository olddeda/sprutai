<?php

/* @var $this yii\web\View */
/* @var $model common\modules\contest\models\Contest */

$this->title = Yii::t('contest', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('contest', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="contest-create">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
