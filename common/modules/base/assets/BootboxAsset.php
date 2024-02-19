<?php
namespace common\modules\base\assets;

use Yii;
use yii\web\AssetBundle;

class BootboxAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@bower/bootbox';

	/**
	 * @inheritdoc
	 */
	public $js = [
		'bootbox.js',
	];

	public static function overrideSystemConfirm() {
		Yii::$app->view->registerJs('
            yii.confirm = function(message, ok, cancel) {
            	bootbox.dialog({
            		title: "'.Yii::t('base', 'confirm_title').'",
  					message: message,
  					buttons: {
  						success: {
  							label: "'.Yii::t('base', 'confirm_button_success').'",
      						className: "btn-primary",
      						callback: function() {
        						!ok || ok();
      						}
  						},
  						cancel: {
  							label: "'.Yii::t('base', 'confirm_button_cancel').'",
      						className: "btn-default",
      						callback: function() {
        						!cancel || cancel();
      						}
  						}
  					}
  				});
            }
        ');
	}
}