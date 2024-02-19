<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */

?>

<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
	<div class="row">
		<div class="col-md-12">
			<div class="date"><?= Yii::$app->formatter->asDate($model->date_at) ?></div>
			<? /**  <h2 class="is-title-lite"><?= $model->title ?></h2> */ ?>
		</div>
	</div>
	
	<?= $model->text ?>
	
	<?= $this->render('//content/_social') ?>

</div>