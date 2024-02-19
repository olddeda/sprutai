<?php

/* @var $this yii\web\View */
/* @var $model common\modules\telegram\models\TelegramChat */
/* @var $plugin common\modules\plugin\models\Project */

$this->title = Yii::t('telegram-chat', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/telegram/chat/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="telegram-chat-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>