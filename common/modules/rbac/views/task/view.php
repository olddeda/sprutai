<?php

use yii\bootstrap\Html;

/**
 * @var $model common\modules\rbac\models\Role
 * @var $this yii\web\View
 */

$this->title = Yii::t('rbac-task', 'view_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'title'), 'url' => ['/rbac/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-task', 'title'), 'url' => ['/rbac/task/index']];
$this->params['breadcrumbs'][] = $model->name;
?>

<div class="task-view">
	
	<?= $this->render('@common/modules/rbac/views/links/_links', [
		'model' => $model,
		'parentFilterModel' => $parentFilterModel,
		'parentDataProvider' => $parentDataProvider,
		'childFilterModel' => $childFilterModel,
		'childDataProvider' => $childDataProvider,
	]) ?>

	<div class="form-group margin-top-30">
		<?= Html::a('<span class="glyphicon glyphicon-arrow-left"></span> '.Yii::t('base', 'button_back'), ['index'], [
			'class' => 'btn btn-default btn-lg'
		]) ?>
	</div>

</div>