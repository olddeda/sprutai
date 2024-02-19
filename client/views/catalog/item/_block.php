<?php

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Content */

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\content\models\Content;
?>

<?php if ($model->catalogItems) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="margin-0 text-primary"><?= Yii::t('content', 'block_catalog_items') ?></h4>
        </div>
        <div class="panel-body items">
            <?php foreach ($model->catalogItems as $item) { ?>
                <?= $this->render('_block_item', ['model' => $item]) ?>
            <?php } ?>
        </div>
    </div>
<?php } ?>