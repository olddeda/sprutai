<?php

use common\modules\base\helpers\enum\ModuleType;
use yii\helpers\Url;
use yii\helpers\Html;

use common\modules\base\components\Helper;

use common\modules\comments\widgets\CommentWidget;

use common\modules\base\extensions\contentbuilder\ContentBuilderAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderContentAsset;
use common\modules\base\extensions\contentbuilder\ContentBuilderSimpleLightBoxAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->context->layoutContent = 'content_no_panel';

$this->title = \yii\helpers\HtmlPurifier::process($model->title);

$this->params['breadcrumbs'][] = \yii\helpers\HtmlPurifier::process($model->title);

$this->seo = $model->seo;

ContentBuilderAsset::register($this);
ContentBuilderContentAsset::register($this);
ContentBuilderSimpleLightBoxAsset::register($this);

$js = <<<JS
    contentbuilderLocalize();

    $('a.is-lightbox').simpleLightbox();

    $('code.code').each(function () {
         codeMirrorHighlight($(this));
    });
    
     //$('[data-i18n]').i18n();
JS;
$this->registerJs($js);

?>

<div class="content-view">
	
	<div class="panel panel-default">
		<div class="panel-body">
			
            <div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
                <?= $model->text ?>
            </div>
		</div>
	</div>
	
</div>
