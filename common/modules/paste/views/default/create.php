<?php

/* @var $this yii\web\View */
/* @var $model common\modules\paste\models\Paste */

$this->title = Yii::t('paste', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('paste', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="paste-create">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
