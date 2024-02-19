<?php
/**
 * @var \artkost\qa\models\Question $model
 */

use artkost\qa\models\Question;
use artkost\qa\models\Answer;
use artkost\qa\Module;

$editRoute = [($model instanceof Question ? 'edit' : 'answer'), 'id' => $model->id];
$deleteRoute = [($model instanceof Question ? 'delete' : 'delete-answer'), 'id' => $model->id];
$closeRoute = ['close', 'id' => $model->id];

?>

<?php if ($model->isAuthor()) : ?>
	<span class="qa-edit-links">
    	<a href="<?= Module::url($editRoute) ?>" title="<?= Yii::t('qa', 'button_update'); ?>" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-pencil"></span></a>
		<a href="<?= Module::url($deleteRoute) ?>" title="<?= Yii::t('qa', 'button_remove'); ?>" class="btn btn-sm btn-primary" data-confirm="<?= Yii::t('qa', 'confirm_delete'); ?>" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-remove"></span></a>
		<?php if ($model instanceof Question && $model->status != 3): ?>
		<a href="<?= Module::url($closeRoute) ?>" title="<?= Yii::t('qa', 'button_close'); ?>" class="btn btn-sm btn-primary" data-confirm="<?= Yii::t('qa', 'confirm_close'); ?>" data-method="post" data-pjax="0"><span class="glyphicon glyphicon-ok"></span></a>
		<?php endif; ?>
    </span>
<?php endif; ?>