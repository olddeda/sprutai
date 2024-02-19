<?php
namespace common\modules\maintenance\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use yii\console\ExitCode;

/**
 * Class MaintenanceController
 * @package comman\maintenance\commands
 */
class MaintenanceController extends Controller
{
    /**
     *  Default action of controller.
     */
    public function actionIndex() {
        echo 'You have to input command "enable" or "disable"!' . PHP_EOL;
    }

    /**
     * Enable maintenance mode.
     * @return int
     */
    public function actionEnable() {
        $maintenance = Yii::$app->maintenance;

        if (!$maintenance->getIsEnabled(true) && $maintenance->enable()) {
            $this->stdout("Maintenance mode enabled successfully.\n", Console::FG_GREEN);
            return ExitCode::OK;
        }
        else {
            $this->stdout("Maintenance mode already enabled.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    /**
     * Disable maintenance mode.
     * @return int
     */
    public function actionDisable() {
        $maintenance = Yii::$app->maintenance;

        if ($maintenance->getIsEnabled(true) && $maintenance->disable()) {
            $this->stdout("Maintenance mode disabled successfully.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stdout("Maintenance mode already disabled.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}