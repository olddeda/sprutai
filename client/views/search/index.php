<?php

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\widgets\ListView;

use yii\helpers\Html;

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('search', 'title');

$this->params['breadcrumbs'][] = Yii::t('search', 'title_result', ['query' => $query]);
?>

<?php

$js =<<<JS
    $('form.navbar-form').addClass('open');
    $('input[name=query]').val('$query');
    
JS;
$this->registerJs($js);

?>

<div class="content-index">
	<?= ListView::widget([
		'dataProvider' => $dataProvider,
		'itemView' => '_view',
		'layout' => "{items}\n{pager}",
	
	]); ?>
</div>

