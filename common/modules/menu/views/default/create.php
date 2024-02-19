<?php

/* @var $this yii\web\View */
/* @var $model common\modules\menu\models\Menu */

$this->title = Yii::t('menu', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="menu-create">
	<?= $this->render('_form', ['model' => $model]) ?>
</div>
