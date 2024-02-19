<?php

use yii\widgets\ListView;
use yii\data\ActiveDataProvider;

use common\modules\content\models\Article;
use common\modules\content\helpers\enum\Status;

/* @var \common\modules\banner\models\Banner $banner */

$query = Article::find()
	->joinWith([
		'media',
		'statistics',
		'stat',
		'tags',
		'author' => function($query) {
			$query->joinWith('profile');
		},
	])
	->andWhere(['between', Article::tableName().'.date_at', $banner->getDateBeginDay(), $banner->getDateEndDay()])
	->andHaving('contentVoteRating > 0')
	->andWhere([Article::tableName().'.status' => Status::ENABLED])
	->andWhere(['not in', Article::tableName().'.author_id', [3, 6, 28, 67, 69, 70, 821, 1145]])
	->orderBy(['contentVotePositive' => SORT_DESC])
	->limit(5)
	->votes();

$dataProvider = new ActiveDataProvider([
	'query' => $query,
	'pagination' => false,
]);
?>

<?php if ($dataProvider->count && 1 == 2) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="margin-0 text-primary"><?= Yii::t('article', 'header_leader') ?></h4>
		</div>
		<div class="panel-body">
			<div class="content-view-other">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '_view_article_item',
					'layout' => "{items}",
				]); ?>
			</div>
		</div>
	</div>
<?php } ?>
