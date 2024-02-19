<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\content\models\Article */
?>

<div class="is-container is-container-left container container-fluid contentbuilder-content contentbuilder">
	<div class="row">
		<div class="col-md-12">
			<div class="date inline"><?= $model->getDateTimeHuman() ?></div>
			<?php if (Yii::$app->user->getIsAdmin() || Yii::$app->user->getIsEditor()) { ?>
				<div class="edit inline margin-left-10">
					<?= Html::a('['.mb_strtolower(Yii::t('base', 'button_update')).']', ['/content/article/update', 'id' => $model->id]) ?>
				</div>
			<?php } ?>
		</div>
	</div>
	
	<div class="text"><?= $model->getTextParsed('text') ?></div>
	
	<?= $this->render('//content/_social') ?>

</div>