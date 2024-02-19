<?php

/* @var $this yii\web\View */
/* @var $searchModel common\modules\content\models\search\ArticleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\widgets\ListView;

use yii\helpers\Html;
use yii\helpers\Url;

$this->context->layoutContent = 'content_no_panel';

$this->title = Yii::t('content-blog', 'title_author', ['author' => $author->fio]);

$this->params['breadcrumbs'][] = ['url' => Url::toRoute('blog/index'), 'label' => Yii::t('content-blog', 'title')];
$this->params['breadcrumbs'][] = Html::encode($this->title);

?>
	
	<div class="content-index">
		<?= ListView::widget([
			'dataProvider' => $dataProvider,
			'itemView' => '_view',
			'layout' => "{items}\n{pager}",
		
		]); ?>
	</div>

<?php if (Yii::$app->user->can('content.blog.create')) { ?>
	<div class="form-group margin-top-20">
		<?= Html::a('<span class="glyphicon glyphicon-plus"></span> '.Yii::t('base', 'button_add'), ['/content/blog/create'], [
			'class' => 'btn btn-primary btn-lg'
		]) ?>
	</div>
<?php } ?>