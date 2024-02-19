<?php
use artkost\qa\Module;

$this->title = Yii::t('qa', 'title_ask');

$this->params['breadcrumbs'][] = ['label' => Yii::t('qa', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="qa-ask">
    <?= $this->render('parts/form-question', ['model' => $model]) ?>
</div>

