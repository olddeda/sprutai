<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;

use common\modules\content\models\Question;
use common\modules\content\helpers\enum\Status;

/* @var $this yii\web\View */
/* @var $model \common\modules\company\models\Company */

$query = Question::find()
	->joinWith([
		'author' => function ($query) {
			$query->joinWith(['profile', 'mediaAvatar']);
		},
	])
	->andWhere([
		Question::tableName().'.status' => Status::ENABLED,
	])
	->andWhere(Question::tableName().'.company_id != 0')
	->votes()
	//->groupBy('company_id')
	->orderBy('date_at DESC')
	->limit(5);

$dataProvider = new ActiveDataProvider([
	'query' => $query,
	'pagination' => false,
]);

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="margin-0 text-primary"><?= Yii::t('company-question', 'header_last') ?></h4>
	</div>
	<div class="panel-body">
		<?php if ($dataProvider->totalCount) { ?>
			<div class="content-view-other margin-top-10">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '../default/_view_questions_view',
					'layout' => "{items}",
					'options' => ['class' => 'items']
				]); ?>
			</div>
		<?php } else { ?>
			<p><?= Yii::t('company-question', 'error_empty_list_last') ?></p>
		<?php } ?>
	</div>
</div>
