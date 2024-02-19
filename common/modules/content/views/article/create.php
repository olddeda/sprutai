<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */

$this->title = Yii::t('content-article', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('content-article', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';
?>

<div class="content-article-create">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>
