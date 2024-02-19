<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Portfolio */

?>

<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
	<div class="row">
		<div class="col-md-12">
			<div class="date inline"><?= Yii::$app->formatter->asDate($model->date_at) ?></div>
		</div>
	</div>
	
	<?= $model->text ?>
	
	<?= $this->render('//content/_social') ?>

</div>