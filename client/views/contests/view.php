<?php

use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $date string */
/* @var $canVote boolean */
/* @var $showCounters boolean */

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('contest', 'title_result', ['date' => $date]);
?>

<div class="row">
	<div class="col-sx-12 col-sm-12 col-md-8 col-lg-9">
		<div class="content-index">
			<?= ListView::widget([
				'dataProvider' => $dataProvider,
				'itemView' => '_view',
				'viewParams' => [
					'canVote' => $canVote,
					'showCounters' => $showCounters,
				],
				'layout' => "{items}\n{pager}",
			]); ?>
		</div>
	</div>
	
	<div class="col-sx-12 col-sm-12 col-md-4 col-lg-3">
		<?= $this->render('//banner/_contests') ?>
	</div>
</div>

<?php
if (!$canVote) {
	$urlCreate = Url::to(['/content/article/create']);
	$footer = <<<HTML
<a class="btn btn-primary" href="$urlCreate">Написать статью</a>
<a class="btn btn-default" data-dismiss="modal">Понятно</a>
HTML;
	
	Modal::begin([
		'id' => 'modal-contest-vote-warning',
		'header' => '<h4 class="modal-title">Мы сожалеем</h4>',
		'footer' => $footer,
	]);
	
	echo Html::tag('p', 'Для участия в голосовании вы должны внести свой вклад в развития сообщества в виде статьи, блога, новости или любого другого материала.');
	
	Modal::end();
	
	$js = <<<JS
	$('.vote-btn').click(function() {
	    $('#modal-contest-vote-warning').modal('show');
	});
JS;
	$this->registerJs($js);
	
}
?>