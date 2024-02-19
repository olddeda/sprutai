<?php

use yii\helpers\Html;

$this->title = Yii::t('settings', 'update_title_params', [
    'section' => $model->section,
	'key' => $model->key,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('settings', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('settings', 'update_title');

?>
<div class="settings-update">
	
    <?= $this->render('_form', [
        'model' => $model,
    ]);
    ?>

</div>
