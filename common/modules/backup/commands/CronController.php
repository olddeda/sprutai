<?php

namespace common\modules\backup\commands;

use common\modules\base\components\Debug;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

use PharData;
use Phar;

use common\modules\backup\Module;
use common\modules\backup\helpers\MysqlHelper;


/**
 * Backup db and data
 */
class CronController extends Controller {
	
	public function actionIndex() {
		$this->actionDb();
		$this->actionFolder();
		$this->actionClear();
	}
	
	/**
	 * Backup directory
	 *
	 * @param string $path Force the path which needs to be backed up
	 */
	public function actionFolder($path = null) {
		/**@var Module $module */
		$module = Yii::$app->getModule('backup');
		if ($module->backupFolderEnable()) {
			
			echo $this->ansiFormat('Start backup FOLDER', Console::FG_BLUE).PHP_EOL;
			
			$paths = $module->backupFolderData();
			if ($path != null) {
				$paths = [$path];
			}
			foreach ($paths as $folder) {
				
				$folder = Yii::getAlias($folder);
				if (file_exists($folder)) {
					$archiveFile = $module->backupPath.DIRECTORY_SEPARATOR.pathinfo($folder, PATHINFO_FILENAME).'_'.date('Y_m_d_H_i_s').'.tar';
					echo $this->ansiFormat('Archive file '.$archiveFile, Console::FG_YELLOW).PHP_EOL;
					
					$archive = new PharData($archiveFile);
					$archive->buildFromDirectory($folder);
					
					// Send email
					if ($module->mail->enable) {
						echo $this->ansiFormat('Send to email', Console::FG_YELLOW).PHP_EOL;
						if (filesize($archiveFile) < 30000000) {
							$module->mail->setFile($archiveFile)->setType(Module::TYPE_FOLDER)->send();
						}
						else {
							echo $this->ansiFormat('Can not send file, file size is too big!', Console::FG_RED).PHP_EOL;
						}
					}
					
					// Send ftp
					if ($module->ftp->enable) {
						echo $this->ansiFormat('Send to ftp', Console::FG_YELLOW).PHP_EOL;
						$module->ftp->setFile($archiveFile)->push();
					}
					
					// Send WebDav
					if ($module->webdav->enable) {
						echo $this->ansiFormat('Send to webdav', Console::FG_YELLOW).PHP_EOL;
						$result = $module->webdav->setFile($archiveFile)->push();
						if ($result) {
							echo $this->ansiFormat('Folder "'.$folder.'" has error', Console::FG_RED).PHP_EOL;
							echo $this->ansiFormat($result, Console::FG_RED).PHP_EOL;
						}
					}
					echo $this->ansiFormat('Folder "'.$folder.'" backed up!', Console::FG_GREEN).PHP_EOL;
				}
				else {
					echo $this->ansiFormat('Folder "'.$folder.'" does not exists', Console::FG_RED).PHP_EOL;
				}
			}
		}
		else {
			echo $this->ansiFormat('Backup FOLDER not enable!', Console::FG_RED).PHP_EOL;
		}
	}
	
	/**
	 * Backup db
	 *
	 * @param string $db Force the db which needs to be backed up
	 */
	public function actionDb($db = null) {
		/**@var Module $module */
		$module = Yii::$app->getModule('backup');
		if ($module->backupDbEnable()) {
			
			echo $this->ansiFormat('Start backup DB', Console::FG_BLUE).PHP_EOL;
			
			$dbs = $module->backupDbData();
			if ($db != null) {
				$dbs = [$db];
			}
			foreach ($dbs as $db => $params) {
				$sql = new MysqlHelper($db);
				
				echo $this->ansiFormat('Generate sql file', Console::FG_YELLOW).PHP_EOL;
				
				$tables = $sql->getTables();
				
				$need = $params['tables'];
				$except = $params['except'];
				
				foreach ($tables as $i => $table) {
					$matched = empty($need);
					foreach ($need as $n) {
						if ($table === $n || !empty($n) && substr_compare($n, '*', -1, 1) === 0 && strpos($table, rtrim($n, '*')) === 0) {
							$matched = true;
							break;
						}
					}
					foreach ($except as $m) {
						$prefix = rtrim($m, '*');
						if (($table === $m || $prefix !== $m) && strpos($table, $prefix) === 0) {
							$matched = false;
							break;
						}
					}
					if (!$matched) {
						unset($tables[$i]);
					}
				}
				
				if (!$sql->StartBackup()) {
					die;
				}
				foreach ($tables as $tableName) {
					$sql->getColumns($tableName);
				}
				foreach ($tables as $tableName) {
					$sql->getData($tableName);
				}
				$sqlFile = $sql->EndBackup();
				
				$archiveFile = $sqlFile.'.bz2';
				echo $this->ansiFormat('Archive file '.$archiveFile, Console::FG_YELLOW).PHP_EOL;
				
				$archive = new PharData($archiveFile);
				$archive->addFile($sqlFile, pathinfo($sqlFile,PATHINFO_BASENAME));
				$archive->compress(Phar::BZ2);
				
				if ($module->mail->enable) {
					echo $this->ansiFormat('Send to email', Console::FG_YELLOW).PHP_EOL;
					$module->mail->setFile($archiveFile)->setType(Module::TYPE_DB)->send();
				}
				if ($module->ftp->enable) {
					echo $this->ansiFormat('Send to ftp', Console::FG_YELLOW).PHP_EOL;
					$module->ftp->setFile($archiveFile)->push();
				}
				if ($module->webdav->enable) {
					echo $this->ansiFormat('Send to webdav', Console::FG_YELLOW).PHP_EOL;
					$module->webdav->setFile($archiveFile)->push();
				}
				echo $this->ansiFormat('Backup DB success!', Console::FG_GREEN).PHP_EOL;
			}
		}
		else {
			echo $this->ansiFormat('Backup DB not enable!', Console::FG_RED).PHP_EOL;
		}
	}
	
	/**
	 * Remove files was created x days ago
	 *
	 * @param int $days Days old of files
	 */
	public function actionClear($days = 5) {
		/**@var Module $module */
		$module = Yii::$app->getModule('backup');
		$files = array_diff(scandir($module->backupPath), [
			'.',
			'..',
		]);
		foreach ($files as $file) {
			$filePath = $module->backupPath.DIRECTORY_SEPARATOR.$file;
			$old = (time() - filemtime($filePath)) / (3600 * 24);
			if ($old >= $days) {
				echo unlink($filePath) ? 'Removed "'.$file.'"'.PHP_EOL : 'Can not remove "'.$file.'"'.PHP_EOL;
			}
		}
	}
}