<?php

namespace client\forms;

use common\modules\base\components\Debug;
use yii\base\Model;
use yii\web\UploadedFile;

class ItunesReportForm extends Model
{
    /**
     * @var array
     */
    private $_data;

    /**
     * @var array
     */
    private $_dataRates;

    /**
     * @var array
     */
    private $_rates;

    /**
     * @var array
     */
    private $_transactions;

    /**
     * @var UploadedFile
     */
    public $rates;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @var array
     */
    public $result;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['rates'], 'file', 'skipOnEmpty' => false, 'extensions' => 'csv'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'txt'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'rates' => 'Курсы валют (csv)',
            'file' => 'Отчет (txt)',
        ];
    }

    public function upload() {
        $this->_dataRates = file($this->rates->tempName);
        $this->_data = file($this->file->tempName);

        $result = [];
        foreach ($this->_getSKUS() as $sku) {
            $item = [
                'sku' => $sku,
                'title' => $this->_getTitle($sku),
                'quantity' => 0,
                'eur' => 0,
                'usd' => 0,
                'rub' => 0,
            ];

            $transactions = [];
            foreach ($this->_getTransactionsBySKU($sku) as $t) {
                $currency = $t['Customer Currency'];
                $usdRate = $this->_getRate($currency);
                $usdPrice = (float)$t['Partner Share'] * $usdRate;
                $eurPrice = $usdPrice / $this->_getRate('EUR');
                $rubPrice = $usdPrice / $this->_getRate('RUB');

                $quantity = (int)$t['Quantity'];

                $eur = $eurPrice * $quantity;
                $usd = $usdPrice * $quantity;
                $rub = $rubPrice * $quantity;

                $item['quantity'] += $quantity;
                $item['eur'] += $eur;
                $item['usd'] += $usd;
                $item['rub'] += $rub;

                $transactions[] = [
                    'date' => $t['Transaction Date'],
                    'type' => $t['Sale or Return'] == 'R' ? 'Возврат' : 'Покупка',
                    'quantity' => $quantity,
                    'eur' => $eur,
                    'usd' => $usd,
                    'rub' => $rub,
                ];
            }
            $item['transactions'] = $transactions;

            $result[] = $item;
        }

        $this->result = [
            'dates' => $this->_getDates(),
            'items' => $result,
        ];
    }

    /**
     * @return array
     */
    private function _getDates(): array {
        return [
            'start' => $this->_explode($this->_data[1], "\t")[1],
            'end' => $this->_explode($this->_data[2], "\t")[1],
        ];
    }

    /**
     * @return array
     */
    private function _getHeaders(): array {
        $tmp = [];
        foreach ($this->_explode($this->_data[3], "\t") as $key => $field) {
            $tmp[$key] = trim($field);
        }
        return $tmp;
    }

    /**
     * @return array
     */
    private function _getTransactions(): array {
        if (is_null($this->_transactions)) {
            $this->_transactions = [];
            $headers = $this->_getHeaders();
            for ($i = 4; $i < count($this->_data); $i++) {
                $data = $this->_explode($this->_data[$i], "\t");
                if (preg_match("/\d{2}\\/\d{2}\/\d{4}/", $data[0]) == false) {
                    continue;
                }

                $row = [];
                foreach ($data as $key => $val) {
                    $row[$headers[$key]] = $val;
                }
                $this->_transactions[] = $row;
            }
        }
        return $this->_transactions;
    }

    /**
     * @param string $sku
     * @return array
     */
    private function _getTransactionsBySKU(string $sku): array {
        $tmp = [];
        foreach ($this->_getTransactions() as $t) {
            if ($t['SKU'] == $sku) {
                $tmp[] = $t;
            }
        }

        usort($tmp, function($a, $b)
        {
            return strcmp($a['Transaction Date'], $b['Transaction Date']);
        });

        return $tmp;
    }

    /**
     * @param string $currency
     * @param string $sku
     * @return mixed
     */
    private function _getCurrency(string $currency, string $sku): ?string {
        foreach ($this->_getTransactions() as $t) {
            if ($t['Customer Currency'] == $currency && $t['SKU'] == $sku) {
                return $t['Partner Share'];
            }
        }
        return null;
    }

    /**
     * @return array
     */
    private function _getSKUS(): array {
        $tmp = [];
        foreach ($this->_getTransactions() as $t) {
            if (!in_array($t['SKU'], $tmp)) {
                $tmp[] = $t['SKU'];
            }
        }
        return $tmp;
    }

    /**
     * @param string $sku
     * @return string|null
     */
    private function _getTitle(string $sku): ?string {
        foreach ($this->_getTransactions() as $t) {
            if ($t['SKU'] == $sku) {
                return $t['Title'];
            }
        }
        return null;
    }

    /**
     * @return array
     */
    private function _getRates(): array {
        if (is_null($this->_rates)) {
            $this->_rates = [];
            for ($i = 3; $i < count($this->_dataRates); $i++) {
                $data = array_map(function($i) {
                    return str_replace('"', '', $i);
                }, $this->_explode($this->_dataRates[$i], ","));
                if (preg_match("/\(([A-Z]{3})\)/", $data[0], $matches) == false) {
                    continue;
                }
                $this->_rates[strtoupper($matches[1])] = $data[8];
            }
        }
        return $this->_rates;
    }

    /**
     * @param string $currency
     * @return float
     */
    private function _getRate(string $currency): float {
        $currency = strtoupper($currency);
        $rates = $this->_getRates();
        return isset($rates[$currency]) ? $rates[$currency] : 0.0;
    }

    /**
     * @param string $data
     * @param string $separator
     * @return array
     */
    private function _explode(string $data, string $separator): array
    {
        return array_map('trim', explode($separator, $data));
    }
}