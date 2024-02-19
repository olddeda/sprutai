<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */
?>

<?= $this->render('//article/_view', [
	'model' => $model,
	'showType' => true,
	'typeName' => 'Видеообзор',
]);
