<?php

use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\components\Debug;

use common\modules\media\helpers\enum\Mode;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = \yii\helpers\HtmlPurifier::process($model->title);

$this->params['breadcrumbs'][] = \yii\helpers\HtmlPurifier::process($model->title);

$this->seo = $model->seo;
?>

<?php if ($model->type == \common\modules\menu\helpers\enum\Type::TAG) { ?>
<?= $this->render('_view_tag', ['model' => $model]); ?>
<?php } else { ?>
<?= $this->render('_view_item', ['model' => $model]); ?>
<?php } ?>

