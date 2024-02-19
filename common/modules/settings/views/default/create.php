<?php

use yii\helpers\Html;

$this->title = Yii::t('settings', 'create_title');
$this->params['breadcrumbs'][] = ['label' => Yii::t('settings', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-create">
	
    <?= $this->render('_form', [
        'model' => $model
    ]); ?>

</div>
