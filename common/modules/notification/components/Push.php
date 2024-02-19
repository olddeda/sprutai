<?php
namespace common\modules\notification\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Push extends Component
{
    /* @var $APNS_ENVIRONMENT_SANDBOX */
    const APNS_ENVIRONMENT_SANDBOX = '.sandbox';

    /* @var $APNS_ENVIRONMENT_PRODUCTION */
    const APNS_ENVIRONMENT_PRODUCTION = '';

    /* @var $GCM_URL */
    const GCM_URL = 'https://fcm.googleapis.com/fcm/send';

    /* @var $TYPE_APNS */
    const TYPE_APNS = 'apns';

    /* @var $TYPE_GCM */
    const TYPE_GCM = 'gcm';

    /* @var  $apnsConfig array */
    public $apnsConfig;

    /* @var  $gcmConfig array */
    public $gcmConfig;

    /* @var  $options array */
    public $options;

    /* @var  $type string */
    private $type;

    /* @var  $apnsEnabled boolean default is false */
    private $apnsEnabled = false;

    /* @var  $gcmEnabled boolean default is false */
    private $gcmEnabled = false;

    /* @var  $ctx object */
    private $ctx;
	
	/**
	 * @inheritdoc
	 */
    public function init() {
        parent::init();

        if (is_array($this->apnsConfig) && !empty($this->apnsConfig)) {
            $this->validateApns();
            $this->apnsEnabled = true;
        }

        if (is_array($this->gcmConfig) && !empty($this->gcmConfig)) {
            $this->validateGcm();
            $this->gcmEnabled = true;
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function validateApns() {
        if (!ArrayHelper::keyExists('environment', $this->apnsConfig) || !ArrayHelper::isIn(ArrayHelper::getValue($this->apnsConfig, 'environment'), [
            self::APNS_ENVIRONMENT_SANDBOX, self::APNS_ENVIRONMENT_PRODUCTION
		])) {
            throw new InvalidConfigException('Apns environment is invalid.');
        }

        if (ArrayHelper::keyExists('pem', $this->apnsConfig)) {
            if (0 === strpos(ArrayHelper::getValue($this->apnsConfig, 'pem'), '@')) {
                $path = Yii::getAlias(ArrayHelper::getValue($this->apnsConfig, 'pem'));
            } else {
                $path = ArrayHelper::getValue($this->apnsConfig, 'pem');
            }

            if (!is_file($path)) {
                throw new InvalidConfigException('Apns pem is invalid.');
            }

            $this->apnsConfig['pem'] = $path;
        }
        else {
            throw new InvalidConfigException('Apns pem is required.');
        }
    }

    /**
     * @throws InvalidConfigException
     */
    private function validateGcm() {
        if (!ArrayHelper::keyExists('apiAccessKey', $this->gcmConfig)) {
            throw new InvalidConfigException('Gcm api access key is invalid.');
        }
    }

    /**
     * @param $tokens
     * @return array
     */
    private function splitDeviceTokens($tokens) {
        $apnsTokens = [];
        $gcmTokens = [];
        $invalidTokens = [];

        foreach ($tokens as $token) {
            if (strlen($token) == 64) {
                $apnsTokens[] = $token;
            } elseif (strlen($token) == 152) {
                $gcmTokens[] = $token;
            } else {
                $invalidTokens[] = $token;
            }
        }

        return [
          'apns' => $apnsTokens,
          'gcm' => $gcmTokens,
          'invalid' => $invalidTokens
        ];
    }

    /**
     * @return Push
     */
    public function android() {
        $this->type = self::TYPE_GCM;
        return $this;
    }

    /**
     * @return Push
     */
    public function ios() {
        $this->type = self::TYPE_APNS;
        return $this;
    }

    /**
     * @param $id
     * @param $payload
     * @return mixed
     */
    public function send($token, $payload) {
        if ($this->type) {
            switch ($this->type) {
                case self::TYPE_GCM:
                    return self::sendGcm($token, $payload);
                    break;
                case self::TYPE_APNS:
                    return self::sendApns($token, $payload);
                    break;
            }
        }
        else {
            $tokens = self::splitDeviceTokens($token);

            if (!empty(ArrayHelper::getValue($tokens, 'apns'))) {
                self::sendApns(ArrayHelper::getValue($tokens, 'apns'), $payload);
            }

            if (!empty(ArrayHelper::getValue($tokens, 'gcm'))) {
                self::sendGcm(ArrayHelper::getValue($tokens, 'gcm'), $payload);
            }

            if (is_array($this->options) && ArrayHelper::getValue($this->options, 'returnInvalidTokens', false)) {
                return ArrayHelper::getValue($tokens, 'invalid');
            }
        }
    }

    /**
     * @param $id
     * @param $data
     * @throws Exception
     */
    private function sendGcm($id, $data) {
        if (!$this->gcmEnabled) {
            throw new InvalidConfigException('Gcm in not enabled.');
        }

        if (!empty($id)) {
            $fields = [
              'registration_ids' => $id,
              'data' => $data
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::GCM_URL);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
              sprintf('Authorization: key=%s', ArrayHelper::getValue($this->gcmConfig, 'apiAccessKey')),
              'Content-Type: application/json'
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POSTFIELDS, Json::encode($fields));

            $result = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                Yii::error($err);
                return false;
            }

            Yii::info($result);

            return true;
        }
    }

    /**
     * @param $token
     * @param $body
     * @throws Exception
     */
    private function sendApns($token, $body) {
        if (!$this->apnsEnabled) {
            throw new InvalidConfigException('Apns in not enabled.');
        }

        if (is_array($body)) {
            $body = Json::encode($body);
        }

        if(strlen($body) > 256){
            Yii::error("Send message is to long");
            return false;
        }

        $path = sprintf('tls://gateway%s.push.apple.com:2195', ArrayHelper::getValue($this->apnsConfig, 'environment'));
        $this->ctx = stream_context_create();
        stream_context_set_option($this->ctx, 'ssl', 'local_cert', ArrayHelper::getValue($this->apnsConfig, 'pem'));

        if (ArrayHelper::keyExists('passphrase', $this->apnsConfig)) {
            stream_context_set_option($this->ctx, 'ssl', 'passphrase', ArrayHelper::getValue($this->apnsConfig, 'passphrase'));
        }

        $fp = stream_socket_client($path, $err, $message, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $this->ctx);

        if (!$fp) {
            Yii::error(['error' => $err , 'message' => $message]);
            return false;
        }

        stream_set_blocking($fp, 0);

        $ret = false;

        try {
            $msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($body)) . $body;
            $writtenBytes = fwrite($fp, $msg, strlen($msg));

            if($writtenBytes === false){
                Yii::error("An error occurred while the data on the server");
                $ret = false;
            } elseif ($writtenBytes > 0) {
                Yii::info("Copied $writtenBytes bytes to server.");
                $ret = true;
            }

        } catch (Exception $e) {
            Yii::error($e);
            $ret = false;
        }

        fclose($fp);

        return $ret;
    }
}