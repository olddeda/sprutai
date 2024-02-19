<?php

/* @var $this yii\web\View */
/* @var $model common\modules\version\models\Event */
/* @var $plugin common\modules\plugin\models\Project */
/* @var $isCreate boolean */

$this->title = Yii::t('plugin-version', 'title_create');

$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin', 'title'), 'url' => ['/plugin/default/index']];
$this->params['breadcrumbs'][] = ['label' => $plugin->title, 'url' => ['/plugin/default/view', 'id' => $plugin->id]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('plugin-version', 'title'), 'url' => ['index', 'plugin_id' => $plugin->id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="plugin-version-create">

    <?= $this->render('_form', [
        'model' => $model,
        'plugin' => $plugin,
	    'isCreate' => $isCreate,
    ]) ?>

</div>