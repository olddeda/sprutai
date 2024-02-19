<?php

/* @var $this yii\web\View */
/* @var $model common\modules\menu\models\Menu */

$this->title = Yii::t('menu', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="menu-update">
	<?= $this->render('_form', ['model' => $model]) ?>
</div>
