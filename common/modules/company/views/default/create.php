<?php

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\Company */

$this->title = Yii::t('company', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->context->layoutContent = 'content_no_panel';
$this->context->layoutSidebar = null;
$this->context->bodyClass .= ' sidebar-content';

?>

<div class="company-create">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
