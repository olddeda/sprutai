<?php

use yii\helpers\Html;

/**
 * @var $model common\modules\rbac\models\Role
 * @var $this  yii\web\View
 */

$this->title = Yii::t('rbac-permission', 'update_title');

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'title'), 'url' => ['/rbac/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-permission', 'title'), 'url' => ['/rbac/permission/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="permission-update">
	
	<?= $this->render('_form', [
		'model' => $model,
	]) ?>

</div>