<?php

use yii\widgets\ListView;
use yii\data\ActiveDataProvider;


use common\modules\content\models\ContentAuthorStat;
use common\modules\content\models\Blog;
use common\modules\content\helpers\enum\Status;

use common\modules\user\models\User;

/* @var \common\modules\banner\models\Banner $banner */

$query = Blog::find()
	->joinWith([
		'media',
		'statistics',
		'tags',
		'author' => function($query) {
			$query->joinWith(['profile', 'contentsStat']);
		},
	])
	->andWhere(['between', Blog::tableName().'.date_at', $banner->getDateBeginDay(), $banner->getDateEndDay()])
	->andWhere([Blog::tableName().'.status' => Status::ENABLED])
	->andWhere(['not in', Blog::tableName().'.author_id', [3, 6, 28, 67, 69, 70, 821, 1145]])
	->andWhere(ContentAuthorStat::tableName().'.subscribers > 0')
	->orderBy([ContentAuthorStat::tableName().'.subscribers' => SORT_DESC])
	->groupBy(User::tableName().'.id')
	->limit(5);

$dataProvider = new ActiveDataProvider([
	'query' => $query,
	'pagination' => false,
]);
?>

<?php if ($dataProvider->count && 1 == 2) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="margin-0 text-primary"><?= Yii::t('blog', 'header_leader') ?></h4>
		</div>
		<div class="panel-body">
			<div class="content-view-other">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '_view_blog_item',
					'layout' => "{items}",
				]); ?>
			</div>
		</div>
	</div>
<?php } ?>