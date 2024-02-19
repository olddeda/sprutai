<?php
/**
 * @var \artkost\qa\models\Question[] $models
 * @var \yii\data\ActiveDataProvider $dataProvider
 */

use artkost\qa\Module;

use artkost\qa\widgets\Tags;

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('qa', 'title');

$this->params['breadcrumbs'][] = $this->title;


?>
<div class="qa-index">
	<div class="row">
		<div class="col-md-9">
			<div class="row">
				<div class="col-md-12">
					<?= $this->render('_tabs', ['route' => $this->context->action->id]) ?>
					<?= $this->render('parts/list', ['models' => $models]) ?>
				</div>
			</div>
			<?= ($dataProvider) ? $this->render('parts/pager', ['dataProvider' => $dataProvider]) : false ?>
		</div>
		<div class="col-md-3">
			<?= Tags::widget(['limit' => 20]) ?>
		</div>
	</div>
</div>


