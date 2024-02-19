<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\data\ActiveDataProvider;

use common\modules\base\helpers\enum\Status;

use common\modules\company\models\Company;

use common\modules\user\models\User;
use common\modules\user\models\UserProfile;
use common\modules\user\models\UserAddress;
use common\modules\user\models\UserAccount;

use common\modules\media\models\Media;

use common\modules\vote\models\Vote;
use common\modules\vote\models\VoteAggregate;

/* @var $this yii\web\View */

$query = Company::find()
	->joinWith([
	])
	->andWhere('status = :status AND `companyFavoriteAggregate`.`positive` > 0', [
		':status' => \common\modules\base\helpers\enum\Status::ENABLED
	])
	->votes()
	->orderBy('companyFavoritePositive DESC')
	->limit(5);

$dataProvider = new ActiveDataProvider([
	'query' => $query,
	'pagination' => false,
]);

$voteModule = Yii::$app->getModule('vote');
?>

<?php if ($dataProvider->count) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="margin-0 text-primary"><?= Yii::t('company', 'header_top') ?></h4>
		</div>
		<div class="panel-body">
			<div class="content-view-other">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '_view',
					'layout' => "{items}",
				]); ?>
			</div>
			
			<div class="align-center margin-top-25 margin-bottom-10">
				<?= Html::a(Yii::t('author', 'link_list_all'), Url::to(['/companies/default/index']), [
					'class' => 'btn btn-large btn-primary',
				]) ?>
			</div>

		</div>
	</div>
<?php } ?>