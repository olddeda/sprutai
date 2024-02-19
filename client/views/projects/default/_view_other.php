<?php

use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model common\modules\project\models\Project */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="content-view-other">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view_other_view',
		'layout' => "{items}",
	]); ?>
</div>
