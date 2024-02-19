<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $type string */

use yii\widgets\ListView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="content-index">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view',
		'layout' => "{items}\n{pager}",
		'emptyText' => Yii::t('company', 'list_is_empty'),
	]); ?>
</div>

