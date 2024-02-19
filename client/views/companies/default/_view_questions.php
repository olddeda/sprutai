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
		Question::tableName().'.company_id' => $model->id,
		Question::tableName().'.status' => Status::ENABLED,
	])
	->votes()
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
		<div class="content-view-other">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view_questions_view',
				'layout' => "{items}",
				'options' => [
					'class' => 'margin-bottom-0'
				]
			]); ?>
		</div>
		<div class="align-center margin-top-0">
			<?= Html::a(Yii::t('company-question', 'link_list_all'), Url::to(['companies/question/index', 'company_id' => $model->id])) ?>
		</div>
		<hr>
		<?php } else { ?>
		<p><?= Yii::t('company-question', 'error_empty_list_last') ?></p>
		<?php } ?>
		<div class="align-center margin-top-25 margin-bottom-10">
			<?= Html::a(Yii::t('company-question', 'button_add'), Url::to(['companies/question/create', 'company_id' => $model->id]), [
				'class' => 'btn btn-large btn-primary',
			]) ?>
		</div>
	</div>
</div>

