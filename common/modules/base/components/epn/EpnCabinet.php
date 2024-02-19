<?php
namespace common\modules\base\components\epn;

use common\modules\base\components\Debug;
use yii\base\Component;

/**
 * Class EPNCabinet
 * @package common\modules\base\components\epn
 */
class EpnCabinet extends Component
{
    const EPN_API_URL = 'http://api.epn.bz/cabinet';
    const EPN_CLIENT_API_VERSION = 1;

    /**
     * @var string 
     */
    public $apiKey = '';

    /**
     * @var string 
     */
    public $privateKey = '';

    /**
     * @var array 
     */
    private $preparedRequests = [];

    /**
     * @var array 
     */
    private $requestResults = [];

    /**
     * @var string 
     */
    private $lastError = '';

    /**
     * @param $name
     * @param $action
     * @param array $params
     *
     * @return bool
     */
    private function addRequest($name, $action, $params = []) {
        if (!is_array($params)) {
            $params = [];
        }
        $params['action'] = $action;
        $this->preparedRequests[$name] = $params;
        return true;
    }

    /**
     * @param $name
     * @param string $clickId
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $dateType
     * @param string $orderStatus
     * @param string $additionalFields
     * @param string $offerType
     * @param int $page
     * @param int $perPage
     *
     * @return bool
     */
    public function addRequestGetTransactions($name, $clickId = '', $dateFrom = '', $dateTo = '', $dateType = '', $orderStatus = '', $additionalFields = '', $offerType = 'aliexpress', $page = 1, $perPage = 300) {
        $this->addRequest($name, 'get_transactions', [
            'click_id' => $clickId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'date_type' => $dateType,
            'order_status' => $orderStatus,
            'additional_fields' => $additionalFields,
            'offer_type' => $offerType,
            'page' => $page,
            'per_page' => $perPage
        ]);
        return true;
    }

    /**
     * @param $name
     * @param $link
     *
     * @return bool
     */
    public function addRequestCheckLink($name, $link) {
        $this->addRequest($name, 'check_link', [
            'link' => $link,
        ]);
        return true;
    }

    /**
     * @param $name
     * @param int $page
     * @param int $perPage
     *
     * @return bool
     */
    public function addRequestGetCreatives($name, $page = 1, $perPage = 50) {
        $this->addRequest($name, 'get_creatives', [
            'page' => $page,
            'per_page' => $perPage
        ]);
        return true;
    }

    /**
     * @param $name
     * @param int $creativeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $offerType
     * @param string $currency
     * @param int $page
     * @param int $perPage
     *
     * @return bool
     */
    public function addRequestGetStatisticsByDay($name, $creativeId = 0, $dateFrom = '', $dateTo = '', $offerType = '', $currency = 'USD', $page = 1, $perPage = 100) {
        $this->addRequest($name, 'get_statistics_by_day', [
            'creative_id' => $creativeId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'offer_type' => $offerType,
            'currency' => $currency,
            'page' => $page,
            'per_page' => $perPage
        ]);
        return true;
    }

    /**
     * @param $name
     * @param int $creativeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $offerType
     * @param string $currency
     * @param int $page
     * @param int $perPage
     *
     * @return bool
     */
    public function addRequestGetStatisticsByHour($name, $creativeId = 0, $dateFrom = '', $dateTo = '', $offerType = '', $currency = 'USD', $page = 1, $perPage = 20) {
        $this->addRequest($name, 'get_statistics_by_hour', [
            'creative_id' => $creativeId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'offer_type' => $offerType,
            'currency' => $currency,
            'page' => $page,
            'per_page' => $perPage
        ]);
        return true;
    }

    /**
     * @param $name
     * @param int $creativeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $offerType
     * @param string $currency
     * @param int $page
     * @param int $perPage
     *
     * @return bool
     */
    public function addRequestGetStatisticsByCreative($name, $creativeId = 0, $dateFrom = '', $dateTo = '', $offerType = '', $currency = 'USD', $page = 1, $perPage = 20) {
        $this->addRequest($name, 'get_statistics_by_creative', [
            'creative_id' => $creativeId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'offer_type' => $offerType,
            'currency' => $currency,
            'page' => $page,
            'per_page' => $perPage
        ]);
        return true;
    }

    /**
     * @param $name
     * @param $creativeId
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $offerType
     * @param string $currency
     * @param int $page
     * @param int $perPage
     *
     * @return bool
     */
    public function addRequestGetStatisticsBySub($name, $creativeId, $dateFrom = '', $dateTo = '', $offerType = '', $currency = 'USD', $page = 1, $perPage = 20) {
        $this->addRequest($name, 'get_statistics_by_sub', [
            'creative_id' => $creativeId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'offer_type' => $offerType,
            'currency' => $currency,
            'page' => $page,
            'per_page' => $perPage
        ]);
        return true;
    }

    /**
     * @return bool
     */
    public function runRequests() {
        $this->requestResults = [];
        $this->lastError = '';

        $data = [
            'user_api_key' => $this->apiKey,
            'api_version' => self::EPN_CLIENT_API_VERSION,
            'requests' => $this->preparedRequests,
        ];

        $postData = json_encode($data);
        $dataSign = md5($this->privateKey.$postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::EPN_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: text/plain",
            "X-EPN-Digest-Sign: $dataSign",
        ]);

        $result = curl_exec($ch);
        $curlErrorMsg = curl_error($ch);
        if ($curlErrorMsg != '') {
            $this->lastError = $curlErrorMsg;
        }
        else {
            $resultData = json_decode($result, true);

            $this->lastError = isset($resultData['error']) ? $resultData['error'] : '';
            $this->requestResults = isset($resultData['results']) && is_array($resultData['results']) ? $resultData['results'] : [];
        }

        $this->preparedRequests = [];

        return $this->lastError == '' ? true : false;

    }

    /**
     * @param $name
     *
     * @return bool|mixed
     */
    public function getRequestResult($name) {
        return isset($this->requestResults[$name]) ? $this->requestResults[$name] : false;
    }

    /**
     * @return string
     */
    public function lastError() {
        return $this->lastError;
    }
}