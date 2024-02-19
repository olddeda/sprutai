<?php
namespace client\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Url;

class CliController extends Controller
{
	
	public function actionIndex() {
	
	}
	
	public function actionCacheClear() {
		apc_clear_cache();
		apc_clear_cache('user');
		apc_clear_cache('opcode');
	}
}