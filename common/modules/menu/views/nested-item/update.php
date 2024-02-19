<?php
/* @var $this yii\web\View */
/* @var $menu common\modules\menu\models\Menu */
/* @var $model common\modules\menu\models\MenuItem */

$this->title = Yii::t('menu-item', 'title_update');

$this->params['breadcrumbs'][] = ['label' => Yii::t('menu', 'title'), 'url' => ['menu/default/index']];
$this->params['breadcrumbs'][] = ['label' => $menu->title, 'url' => ['/menu/default/view', 'id' => $menu->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
	'menu' => $menu,
	'model' => $model
]) ?>