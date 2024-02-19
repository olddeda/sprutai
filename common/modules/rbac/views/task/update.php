<?php

use yii\bootstrap\Html;

/**
 * @var $model common\modules\rbac\models\Task
 * @var $this yii\web\View
 */

$this->title = Yii::t('rbac-task', 'update_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'title'), 'url' => ['/rbac/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-task', 'title'), 'url' => ['/rbac/task/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="task-update">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>