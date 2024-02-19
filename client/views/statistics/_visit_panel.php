<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\modules\base\components\ActiveRecord */

$visible = Yii::$app->settings->get('enabled', 'statistics');
if (!$visible && (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()))
	$visible = true;

?>

<?php if ($visible) { ?>
<div class="panel panel-default inline">
	<div class="panel-body">
		<div class="statistics-views inline" style="margin: 0" data-toggle="tooltip" data-original-title="<?= Yii::t('statistics', 'tip_visit_'.strtolower($model->getClassName()), $model->getStatisticsVal()) ?>">
			<?= Html::tag('span', '', [
				'class' => 'glyphicon glyphicon-eye-open',
			]) ?>
			<?= Html::tag('b', $model->getStatisticsVal()) ?>
		</div>
	</div>
</div>
<?php } ?>