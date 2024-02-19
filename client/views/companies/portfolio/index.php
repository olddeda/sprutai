<?php

use yii\widgets\ListView;

use common\modules\company\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $company common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('company', 'title_portfolio_title', ['title' => $company->title]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['/companies/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['/companies/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = Yii::t('company', 'title_portfolio');
?>

<div class="companies-news detail-view">
	
	<?= $this->render('../default/_header', [
		'model' => $company,
		'question' => null,
		'portfolio' => null,
	]) ?>

	<div class="content-index padding-20">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
				<?= ListView::widget([
					'dataProvider' => $dataProvider,
					'itemView' => '_view',
					'viewParams' => [
						'hideAuthorName' => true,
					],
					'emptyText' => Yii::t('company', 'error_empty_portfolio'),
					'layout' => "{items}\n{pager}"
				]); ?>
			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('../default/_view_contacts', ['model' => $company]) ?>
				<?= $this->render('../default/_view_discount', ['model' => $company]) ?>
				<?= $this->render('../default/_view_questions', ['model' => $company]) ?>
			</div>
		</div>
	</div>

</div>
