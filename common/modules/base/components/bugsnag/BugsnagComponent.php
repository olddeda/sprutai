<?php
namespace common\modules\base\components\bugsnag;

use common\modules\base\components\Debug;
use Yii;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;
use yii\log\Logger;

use Bugsnag\Client;
use Bugsnag\Report;

/**
 * Class BugsnagComponent
 * @package common\modules\base\components\bugsnag
 */
class BugsnagComponent extends Component
{
	/**
	 * @var string
	 */
    public $apiKey;
	
	/**
	 * @var string
	 */
	public $endpoint;
	
	/**
	 * @var string|null
	 */
    public $stage;
	
	/**
	 * @var array
	 */
    public $messages = [];
	
	/**
	 * @var bool
	 */
    public $sendWarnings;
	
	/**
	 * @var bool
	 */
	public $autoCaptureSessions;
	
	/**
	 * @var \common\modules\user\models\User;
	 */
    public $identityClass;
	
	/**
	 * @var bool
	 */
    public $inException = false;

    /**
	 * @var \Bugsnag\Client
	 */
    private $client;
	
	/**
	 * @inheritdoc
	 */
    public function init() {
        $this->prepareClient();
    }
	
	/**
	 * Prepare client
	 */
    public function prepareClient() {
        if ($this->client === null) {
            $this->client = Client::make($this->apiKey, $this->endpoint);

            if (!is_null($this->stage))
                $this->client->getConfig()->setReleaseStage($this->stage);
            
			$this->client->setAutoCaptureSessions($this->autoCaptureSessions);

            $this->client->registerCallback([$this, 'attachMeta']);
        }
    }
	
	/**
	 * @return array
	 */
    private function getLogs() {
        $result = [];
        $index = 0;

        $messages = $this->messages;
        foreach ($messages as $messageData) {
            list($message, $level, $category, $timestamp, $traces, $memoryUsage) = $messageData;
            if (!is_string($message))
                continue;

            $betterTraces = [];

            foreach ($traces as $trace)
                $betterTraces[] = "{$trace['file']}:{$trace['line']} - {$trace['class']}{$trace['type']}{$trace['function']}()";

            $result[str_pad($index, 2, '0', STR_PAD_LEFT)] = VarDumper::dumpAsString([
                'message' => $message,
                'level' => Logger::getLevelName($level),
                'category' => $category,
                'timestamp' => $timestamp,
                'time' => date('Y-m-d H:i:s', $timestamp) . '.' . substr(fmod($timestamp, 1), 2, 4),
                'traces' => $betterTraces,
                'memoryUsage' => $memoryUsage,
            ]);
            
            $index++;
        }

        return $result;
    }
    
	/**
	 * @return array
	 */
    private function getFiles() {
        $files = [];

        foreach ($_FILES as $fileData) {
            try {
                $files[$fileData['name']] = @file_get_contents($fileData['tmp_name']);
            } catch (\Exception $e) {}
        }

        return $files;
    }
	
	/**
	 * @param \Bugsnag\Report $report
	 * @param $newTrace
	 */
    private function replaceTrace(Report $report, $newTrace) {
        $stacktrace = $report->getStacktrace();
        while (count($stacktrace->getFrames()) > 0) {
            $stacktrace->removeFrame(0);
        }
        foreach ($newTrace as $traceEntry) {
            $stacktrace->addFrame($traceEntry['file'], $traceEntry['line'], $traceEntry['function'], $traceEntry['class']);
        }
    }
	
	/**
	 * @param \Bugsnag\Report $report
	 */
    public function attachMeta(Report $report) {
		if (Yii::$app->has('user') && isset(Yii::$app->user->isGuest)) {
        	
            /** @var \common\modules\user\models\User $user */
            $user = Yii::$app->user->identity;
            if ($user) {
            	$meta  = [
            		'id' => $user->id,
					'login' => $user->username,
					'email' => $user->email,
					'name' => $user->getFio(false),
				];
                $report->setUser($meta);
            }
        }

        $report->setMetaData([
            'log' => $this->getLogs(),
            'files' => $this->getFiles(),
        ]);
    }
	
	/**
	 * Flush log
	 */
    public function flush() {
        $logger = Yii::getLogger();
        if ($logger)
            $logger->flush(true);
        
        $this->client->flush();
    }
	
	/**
	 * @param $message
	 */
    public function notifyException($message) {
        $this->inException = true;
        
        $this->prepareClient();
        
        $this->client->notifyException($message, function (Report $report) use ($message) {
            $report->setSeverity('error');
        });
    }
	
	/**
	 * @param $message
	 * @param $trace
	 */
    public function notifyCustomError($message, $trace) {
        $this->inException = true;
        
        $this->prepareClient();
        
        $this->client->notifyError('Error', $message, function (Report $report) use ($message, $trace) {
            $report->setSeverity('error');
            $this->replaceTrace($report, $trace);
        });
    }
	
	/**
	 * @param $message
	 * @param $trace
	 */
    public function notifyCustomWarning($message, $trace) {
        $this->prepareClient();

        $this->client->notifyError('Warning', $message, function (Report $report) use ($message, $trace) {
            $report->setSeverity('warning');
            $this->replaceTrace($report, $trace);
        });
    }
}
