<?php

/* @var $this yii\web\View */
/* @var $model CatalogItem */

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\tag\models\Tag;

use common\modules\media\helpers\enum\Mode;

use common\modules\catalog\models\CatalogItem;

?>
<div class="item table-grid-all margin-bottom-20">
    <div class="col width-70 padding-right-20">
        <?= Html::img($model->image->getImageSrc(70, 70, Mode::CROP_CENTER), ['class' => 'img-thumbnail img-circle']) ?>
    </div>
    <div class="col width-auto">
        <div class="author margin-bottom-10">
            <h4 class="margin-0"><?= Html::a($model->title, 'https://v2.sprut.ai/catalog/item/'.$model->getSeo()->slugify, ['target' => '_blank']) ?></h4>
            <?php if ($model->vendor) { ?>
            <div class="margin-top-5">
                <b>Производитель:</b>&nbsp;<?= $model->vendor->title ?>
            </div>
            <?php } ?>
	        <?php if ($model->data && isset($model->data['shops']) && count($model->data['shops'])) { ?>
	        <div class="margin-top-5">
		        <b>Заказать на:</b><br>
			    <?php foreach ($model->data['shops'] as $shop) { ?>
				<?php if ($tag = Tag::findBy($shop['shop_id'])) { ?>
				<div class="margin-top-5">
                    <?= Html::img($tag->image->getImageSrc(25, 25, Mode::RESIZE), ['class' => 'img-thumbnail margin-right-5']) ?>
                    <?= Html::a($tag->title, $shop['short_url'], ['_target' => 'blank']) ?>
				</div>
				 <?php } ?>
				<?php } ?>
	        </div>
            <?php } ?>
        </div>
    </div>
    <hr/>
</div>
