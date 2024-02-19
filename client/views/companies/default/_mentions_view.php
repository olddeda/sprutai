<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */

?>

<?= $this->render('//'.$model->getTypeName().'/_view', [
	'model' => $model,
	'showType' => true,
]);
