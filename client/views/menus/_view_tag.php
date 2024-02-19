<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\components\Debug;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="menu-view">
	
	<div class="flex row row-eq-height">
		<?php foreach ($model->getNestedTag()->roots()->one()->getChildren()->all() as $item) { ?>
			<div class="flex col-xs margin-bottom-15" style="min-width: 300px;">
				<div class="panel margin-0">
					<div class="panel-body">
						<div class="grid">
							<div class="col width-50 padding-right-10">
								<?= Html::a(Html::img($item->tag->image->getImageSrc(50, 50, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle max-width-clear']), Url::to(['menus/view', 'id' => $model->id, 'tag' => $item->tag->title, 'seo' => true])) ?>
							</div>
							<div class="col width-full align-middle">
								<h4 class="margin-top-0 no-wrap"><?= Html::a($item->tag->title, Url::to(['menus/view', 'id' => $model->id, 'tag' => $item->tag->title, 'seo' => true])) ?></h4>
							</div>
						</div>
						<p class="margin-top-20 margin-bottom-0"><?= $item->tag->descr ?></p>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

</div>

