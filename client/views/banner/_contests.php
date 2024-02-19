<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

use common\modules\base\components\Debug;

use common\modules\media\helpers\enum\Mode;

use common\modules\contest\models\Contest;

/* @var $this yii\web\View */

$models = Contest::getActives();
?>

<?php if (count($models)) { ?>
	<?php foreach ($models as $model) { ?>
		<div class="panel panel-default">
			<div class="panel-body">
				<?= Html::a(Html::img($model->image->getImageSrc(1000, 521, Mode::RESIZE_WIDTH), ['class' => 'img-responsive']), $model->content->url) ?>
				<?php if ($model->is_paid) { ?>
					<hr>
					<div class="align-center margin-top-15 margin-bottom-15">
						<h5>Призовой фонд <span class="text-primary"><?= Yii::$app->formatter->asCurrency($model->getPaidTotal()) ?></span></h5>
					</div>
					<div class="align-center margin-bottom-5">
						<?= $this->render('//'.$model->content->getTypeName().'/_view_payment', [
							'model' => $model->content,
							'headerTitle' => Yii::t('contest', 'header_paid'),
							'buttonText' => Yii::t('contest', 'button_paid'),
						]) ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
<?php } ?>