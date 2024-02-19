<?php

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramChat */

$this->title = Yii::t('telegram-chat', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('telegram-chat', 'title'), 'url' => ['/telegram/chat/index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['/telegram/chat/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="telegram-chat-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

