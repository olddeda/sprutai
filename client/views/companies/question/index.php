<?php

use yii\helpers\Html;
use yii\widgets\ListView;

use common\modules\comments\CommentAsset;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $company \common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['companies/default/index']];
$this->params['breadcrumbs'][] = ['label' => $company->title, 'url' => ['companies/default/view', 'id' => $company->id]];
$this->params['breadcrumbs'][] = Yii::t('company-question', 'title');

$this->title = Yii::t('company-question', 'title');

CommentAsset::register($this);
?>

<div class="companies-questions-index detail-view">
	
	<?= $this->render('../default/_header', ['model' => $company]) ?>

	<div class="content-index padding-20">
		<div class="row">
			<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">

				<div class="form-group margin-bottom-20">
                    <?= $this->render('_tabs', ['company' => $company]) ?>
				</div>

				<?php if ($dataProvider->totalCount) { ?>
					<?= ListView::widget([
						'dataProvider' => $dataProvider,
						'itemView' => '_view',
						'viewParams' => [
							'company' => $company,
						],
						'layout' => "{items}\n{pager}"
					]); ?>
				<?php } else { ?>
					<div class="panel panel-default">
						<div class="panel-body">
							<?= Yii::t('company-question', 'error_empty_list_user') ?>
						</div>
					</div>
				<?php } ?>

				<div class="form-group margin-top-20">
					<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('company-question', 'button_add'), ['companies/question/create', 'company_id' => $company->id], [
						'class' => 'btn btn-primary btn-lg'
					]) ?>
				</div>
			</div>
			<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
				<?= $this->render('../default/_view_contacts', ['model' => $company]) ?>
				<?= $this->render('../default/_view_discount', ['model' => $company]) ?>
			</div>
		</div>
	</div>

</div>
