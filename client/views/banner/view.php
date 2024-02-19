<?php


/* @var $this yii\web\View */
/* @var $showLeaders bool */

?>

<?= $this->render('_banners', [
	'showLeaders' => $showLeaders,
]) ?>

<?= $this->render('_contests'); ?>