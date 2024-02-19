<?php

use common\modules\base\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\lookup\models\Lookup */

$this->title = Yii::t('lookup', 'title_create');

if ($this->context->parentModel) {
	$this->title .= ' - '.$this->context->parentModel->title;
	
	$this->params['breadcrumbs'][] = ['label' => Yii::t('lookup', 'title_index'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $this->context->parentModel->title, 'url' => ['view', 'id' => $this->context->parentId]];
	$this->params['breadcrumbs'][] = Yii::t('lookup', 'title_create');
}
else {
	$this->params['breadcrumbs'][] = ['label' => Yii::t('lookup', 'title_index'), 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
}

?>

<div class="lookup-create">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
