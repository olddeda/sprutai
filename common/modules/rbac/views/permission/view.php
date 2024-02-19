<?php

use yii\bootstrap\Html;

/**
 * @var $model common\modules\rbac\models\Permission
 * @var $this yii\web\View
 */

$this->title = Yii::t('rbac-permission', 'view_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'title'), 'url' => ['/rbac/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-permission', 'title'), 'url' => ['/rbac/permission/index']];
$this->params['breadcrumbs'][] = $model->name;
?>

<div class="permission-view">
	
	<?= $this->render('@common/modules/rbac/views/links/_links', [
		'model' => $model,
		'parentFilterModel' => $parentFilterModel,
		'parentDataProvider' => $parentDataProvider,
		'childFilterModel' => $childFilterModel,
		'childDataProvider' => $childDataProvider,
	]) ?>

</div>