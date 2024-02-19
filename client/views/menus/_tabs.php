<?php

use yii\helpers\Html;
use yii\helpers\Url;

use common\modules\base\extensions\bootstrap\Nav;
use common\modules\base\components\Debug;

use common\modules\tag\models\TagNested;


/* @var $this yii\web\View */
/* @var $menuModel \common\modules\menu\models\Menu */
/* @var $tagModel \common\modules\tag\models\Tag */
/* @var $subtagModel \common\modules\tag\models\Tag */

$tagNested = TagNested::findNode($menuModel->getModuleType(), $menuModel->id, $menuModel->nested->id, $tagModel->id);
$tree = TagNested::tree($menuModel->getModuleType(), $menuModel->id, $tagNested);

$items = [];
if ($tree) {
	foreach ($tree as $t) {
		$items[] = [
			'label' => $t['title'],
			'url' => Url::to(['menus/view', 'id' => $menuModel->id, 'tag' => $tagModel->title, 'subtag' => $t['title'], 'seo' => true]),
			'active' => ($subtagModel) ? $subtagModel->id == $t['id'] : false,
		];
	}
}

?>
<?php if (count($items)) { ?>
<div class="form-group margin-bottom-20">
	<?= Nav::widget([
		'options' => ['class' => 'nav navbar-nav tabs'],
		'activateParents' => true,
		'labelTag' => false,
		'debug' => true,
		'classNormal' => 'btn btn-lg btn-default',
		'classActive' => 'btn btn-lg btn-primary',
		'items' => $items,
	]) ?>
	<div class="clearfix"></div>
</div>
<hr />
<?php } ?>