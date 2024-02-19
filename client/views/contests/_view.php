<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */
/* @var $canVote boolean */
/* @var $showCounters boolean */

$name = $model->getTypeName();
if ($name == 'plugin')
	$name = 'plugins';
if ($name == 'project')
	$name = 'projects';
?>

<?= $this->render('_view_'.$name, [
	'model' => $model,
	'canVote' => $canVote,
	'showCounters' => $showCounters,
]);

