<?php
use artkost\qa\Module;

$this->title = Yii::t('qa', 'title_update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('qa', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id, 'alias' => $model->alias]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="qa-ask">
	<?= $this->render('parts/form-question', ['model' => $model]) ?>
</div>
