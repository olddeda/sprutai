<?php

use yii\web\JsExpression;
use yii\bootstrap\Modal;

use common\modules\base\helpers\Url;

use common\modules\menu\widgets\MenuTree;
use common\modules\menu\helpers\enum\Type;

/* @var $this yii\web\View */
/* @var $model common\modules\menu\models\Menu */

$this->title = $model->title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'title'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
?>

<?php
$js = <<<JS
function createItem(menuId, nestedId) {
    var url = '/client/menu/nested-item/create?menu_id=' + menuId + '&nested_id=' + nestedId;
    document.location.href = url;
}
function updateItem(menuId, nestedId, id) {
    var url = '/client/menu/nested-item/update?menu_id=' + menuId + '&nested_id=' + nestedId + '&id=' + id;
    document.location.href = url;
}
function deleteItem(menuId, nestedId, id) {
    var url = '/client/menu/nested-item/delete?menu_id=' + menuId + '&nested_id=' + nestedId + '&id=' + id;
    document.location.href = url;
}
JS;
$this->registerJs($js);

?>



<div class="menu-view">
	<div class="overflow">
		<?= MenuTree::widget([
			'options' => [
				'class' => 'tree',
			],
			'fullWidthOption' => '30000px',
			'items' => $model->tree,
			'urlSearch' => Url::to(['/tag/default/search', 'menu_id' => $model->id]),
			'urlCreate' => Url::to(['/menu/nested/create', 'menu_id' => $model->id]),
			'urlUpdate' => Url::to(['/menu/nested/update', 'menu_id' => $model->id]),
			'urlDelete' => Url::to(['/menu/nested/delete', 'menu_id' => $model->id]),
			'callbacks' => [
				'create' => (($model->type == Type::TITLE) ? new JsExpression('function (nestedId) { createItem('.$model->id.', nestedId) } ') : 0),
				'update' => (($model->type == Type::TITLE) ? new JsExpression('function (nestedId, id) { updateItem('.$model->id.', nestedId, id) } ') : 0),
				'delete' => (($model->type == Type::TITLE) ? new JsExpression('function (nestedId, id) { deleteItem('.$model->id.', nestedId, id) } ') : 0),
			]
		]) ?>
	</div>
</div>