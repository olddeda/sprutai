<?php

/* @var $this yii\web\View */
/* @var $model common\modules\version\models\Event */
/* @var $plugin common\modules\plugin\models\Project */

$this->title = Yii::t('plugin-version', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin-version', 'title'), 'url' => ['index', 'plugin_id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="plugin-version-update">

    <?= $this->render('_form', [
        'model' => $model,
        'plugin' => $plugin,
	    'isCreate' => false,
    ]) ?>

</div>

