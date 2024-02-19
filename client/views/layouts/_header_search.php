<?php

use yii\helpers\Url;

use common\modules\base\extensions\selectize\Selectize;

?>

<form role="search" action="<?= Url::toRoute(['/search/index']) ?>" method="get" class="navbar-form">
	<div class="form-group has-feedback">
		<?= Selectize::widget([
			'name' => 'query',
			'url' => Url::toRoute(['/search/json']),
			'options' => [
				'class' => 'form-control',
			],
			'pluginOptions' => [
				'labelField' => 'title',
				'valueField' => 'id',
				'placeholder' => Yii::t('search', 'placeholder_search'),
				'closeAfterSelect' => false,
			],
 		]) ?>
		<!-- <input type="text" name="query" placeholder="" class="form-control"> -->
		<div data-search-dismiss="" class="fa fa-times form-control-feedback"></div>
	</div>
	<button type="submit" class="hidden btn btn-default">Submit</button>
</form>