<?php

use yii\bootstrap\Html;

/**
 * @var $model common\modules\rbac\models\Role
 * @var $this yii\web\View
 */

$this->title = Yii::t('rbac-role', 'create_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'title'), 'url' => ['/rbac/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-role', 'title'), 'url' => ['/rbac/role/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-create">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>