<?php

use yii\data\ActiveDataProvider;
use yii\widgets\ListView;

use common\modules\base\components\ArrayHelper;
use common\modules\base\helpers\enum\Status;

use common\modules\tag\models\Tag;
use common\modules\tag\helpers\enum\Type as TagType;

use common\modules\telegram\models\TelegramChat;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */

$tagsIds = ArrayHelper::getColumn($model->getTags()->andWhere([
	Tag::tableName().'.type' => TagType::SYSTEM,
])->all(), 'id');

if ($tagsIds) {
	
	$query = TelegramChat::find()->joinWith(['tags'])
		->where([
			TelegramChat::tableName().'.status' => Status::ENABLED,
			TelegramChat::tableName().'.notify_content' => true,
		])
		->andWhere([
			'in', Tag::tableName().'.id', $tagsIds,
		]);
	
	// Prepare provider
	$dataProvider = new ActiveDataProvider([
		'query' => $query,
		'pagination' => [
			'pageSize' => 0,
		],
	]);
?>

<?php if ($dataProvider->totalCount) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="margin-0 text-primary"><?= Yii::t('telegram-chat', 'header_content') ?></h4>
		</div>
		<div class="panel-body">
			<div class="content-view-other">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'options' => [
						'class' => 'list-view items',
					],
					'itemView' => '_block_item',
					'layout' => "{items}",
				]); ?>
			</div>
		</div>
	</div>
<?php } ?>

<?php } ?>