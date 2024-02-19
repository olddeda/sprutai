<div class="row row-table">
	<div class="col-xs-4 text-center pv-lg" style="background-color:rgb(175,216,248);color:white;">
		<em class="fa fa-money fa-3x"></em>
	</div>
	<div class="col-xs-8 pv-lg">
		<div class="h2 mt0"><?= Yii::$app->formatter->asCurrency($costs) ?></div>
		<div class="text-uppercase"><?= Yii::t('payment-dashboard', 'counters_payments_total') ?></div>
	</div>
</div>