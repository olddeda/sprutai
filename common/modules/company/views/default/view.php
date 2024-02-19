<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

use common\modules\content\helpers\TabHelper;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $model common\modules\company\models\Company */

$this->context->layoutContent = 'content_no_panel';
$this->context->bodyClass = 'content-no-padding content-no-title';

$this->title = Yii::t('company', 'title_view');

$this->params['breadcrumbs'][] = ['label' => Yii::t('company', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<?php
ContentBuilderAsset::register($this);
ContentBuilderContentAsset::register($this);
ContentBuilderSimpleLightBoxAsset::register($this);

$js = <<<JS
    contentbuilderLocalize();

    $('a.is-lightbox').simpleLightbox();

    $('code.code').each(function () {
         codeMirrorHighlight($(this));
    });
JS;
$this->registerJs($js);
?>

<div class="company-default-view detail-view">
	
	<?= $this->render('_header', [
		'model' => $model,
	]) ?>

	<div class="panel panel-default margin-20">
		<div class="panel-body">
			<article>
				<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
					<?= $model->text ?>
				</div>
			</article>
		</div>
	</div>

	<div class="panel panel-default margin-20">
		<div class="panel-heading"><?= Yii::t('company', 'header_view_contacts') ?></div>
		<div class="panel-body">
			<?= DetailView::widget([
				'model' => $model,
				'options' => [
					'class' => 'table table-striped detail-view detail-view-general',
				],
				'attributes' => [
					[
						'format' => 'raw',
						'attribute' => 'site',
						'value' => ($model->site) ? Html::a($model->site, $model->site, ['target' => '_blank']) : null,
						'visible' => $model->site,
					],
					[
						'format' => 'raw',
						'attribute' => 'email',
						'value' => Html::mailto($model->email, $model->email),
						'visible' => strlen($model->email),
					],
					[
						'format' => 'raw',
						'attribute' => 'phone',
						'value' => $model->phone,
						'visible'=> strlen($model->phone),
					],
					[
						'format' => 'raw',
						'attribute' => 'telegram',
						'value' => ($model->telegram) ? Html::a('t.me/'.$model->telegram, 'tg://resolve?domain='.$model->telegram, ['target' => '_blank']) : null,
						'visible'=> strlen($model->telegram),
					],
					[
						'format' => 'raw',
						'attribute' => 'instagram',
						'value' => ($model->instagram) ? Html::a('instagram.com/'.$model->instagram, 'https://instagram.com/'.$model->instagram, ['target' => '_blank']) : null,
						'visible'=> strlen($model->instagram),
					],
					[
						'format' => 'raw',
						'attribute' => 'facebook',
						'value' => ($model->facebook) ? Html::a('facebook.com/'.$model->facebook, 'https://facebook.com/'.$model->facebook, ['target' => '_blank']) : null,
						'visible'=> strlen($model->facebook),
					],
					[
						'format' => 'raw',
						'attribute' => 'vk',
						'value' => ($model->vk) ? Html::a('vk.com/'.$model->vk, 'https://vk.com/'.$model->vk, ['target' => '_blank']) : null,
						'visible'=> strlen($model->vk),
					],
					[
						'format' => 'raw',
						'attribute' => 'ok',
						'value' => ($model->ok) ? Html::a('ok.ru/group/'.$model->ok, 'https://ok.ru/group/'.$model->ok, ['target' => '_blank']) : null,
						'visible'=> strlen($model->ok),
					],
					[
						'format' => 'raw',
						'attribute' => 'address',
						'value' => ($model->address) ? $model->address->address : null,
						'visible' => ($model->address && $model->address->address),
					],
				],
			]) ?>
		</div>
	</div>
</div>
