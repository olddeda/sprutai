<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\base\components\ActiveRecord */

$visible = Yii::$app->settings->get('enabled', 'statistics');
if (!$visible && (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()))
	$visible = true;

?>

<?php if ($visible) { ?>
<div class="statistics-views inline" data-toggle="tooltip" data-original-title="<?= Yii::t('statistics', 'tip_visit_'.strtolower($model->getClassName()), $model->getStatisticsVal()) ?>">
	<?= Html::tag('span', '', [
		'class' => 'glyphicon glyphicon-eye-open',
	]) ?>
	<?= Html::tag('b', $model->getStatisticsVal()) ?>
</div>
<?php } ?>