<?php

/* @var $this yii\web\View */
/* @var $model common\modules\tag\models\Tag */

$this->title = Yii::t('tag', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('tag', 'title'), 'url' => ['/tag/default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="tag-create">
	<?= $this->render('_form', ['model' => $model]) ?>
</div>
