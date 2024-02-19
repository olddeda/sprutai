<?php

use common\modules\rbac\components\Item;

?>

<?php if (in_array($model->type, [Item::TYPE_TASK, Item::TYPE_PERMISSION])) { ?>
<?= $this->render('_links_parents', [
	'model' => $model,
	'filterModel' => $parentFilterModel,
	'dataProvider' => $parentDataProvider,
]); ?>
<?php } ?>

<?php if (in_array($model->type, [Item::TYPE_ROLE, Item::TYPE_TASK])) { ?>
<?= $this->render('_links_childs', [
	'model' => $model,
	'filterModel' => $childFilterModel,
	'dataProvider' => $childDataProvider,
]); ?>
<?php } ?>

