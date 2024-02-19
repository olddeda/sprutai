<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */

$name = $model->getTypeName();
if ($name == 'plugin')
	$name = 'plugins';
if ($name == 'project')
	$name = 'projects/default';
?>

<?= $this->render('//'.$name.'/_view', [
	'model' => $model,
	'showType' => true,
]);
