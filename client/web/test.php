<?php

// Класс для работы с Cabinet EPN
class clEPNCabinetAccess {
	const EPN_API_URL = 'http://api.epn.bz/cabinet';
	const EPN_CLIENT_API_VERSION = 1;
	
	// Параметры
	private $user_api_key = '';
	private $user_private_key = '';
	private $prepared_requests = array();
	private $request_results = array();
	private $last_error = '';
	//======================================================================
	// Конструктор
	public function __construct($user_api_key,$user_private_key) {
		$this->user_api_key = $user_api_key;
		$this->user_private_key = $user_private_key;
        }
        //======================================================================
        
        //======================================================================
        // Добавление запроса в список
        private function AddRequest($name, $action, $params = array()) {
		// Нормализуем входные данные
		if (!is_array($params)) {
			$params = array();
		}
		$params['action'] = $action;
		$this->prepared_requests[$name] = $params;
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Запрос на получение транзакций
		public function AddRequestGetTransactions($name, $click_id = '', $date_from = '', $date_to = '', $date_type = '', $order_status = '', $additional_fields = '', $offer_type = 'aliexpress', $page = 1, $per_page = 300){
		// Добавим запрос в список
		$this->AddRequest(
				$name,
				'get_transactions',
				array(
					'click_id' => $click_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'date_type' => $date_type,
					'order_status' => $order_status,
					'additional_fields' => $additional_fields,
					'offer_type' => $offer_type,
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
		}
        //======================================================================
        
        //======================================================================
        // Запрос на проверку ссылок
        public function AddRequestCheckLink($name, $link) {
		// Добавим запрос в список
		$this->AddRequest(
				$name,
				'check_link',
				array(
					'link' => $link,
				)
			);
		return TRUE;
        }
        //======================================================================
		
        //======================================================================
        // Запрос на получение креативов
        public function AddRequestGetCreatives($name,$page = 1,$per_page = 50) {
		// Добавим запрос в список
		$this->AddRequest(
				$name,
				'get_creatives',
				array(
					'page' => $page,
					'per_page' => $per_page
				)
			);
		return TRUE;
        }
        //======================================================================

		//======================================================================
		// Запрос в статистику с группировкой по дням
		public function AddRequestGetStatisticsByDay($name, $creative_id = 0, $date_from = '', $date_to = '', $offer_type = '', $currency = 'USD',$page = 1, $per_page = 20) {
			// Добавим запрос в список
			$this->AddRequest(
				$name,
				'get_statistics_by_day',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'offer_type' => $offer_type,
					'currency' => $currency,
					'page' => $page,
					'per_page' => $per_page
				)
			);
			return TRUE;
		}
		//======================================================================

		//======================================================================
		// Запрос в статистику с группировкой по часам
		public function AddRequestGetStatisticsByHour($name, $creative_id = 0, $date_from = '', $date_to = '', $offer_type = '', $currency = 'USD',$page = 1, $per_page = 20) {
			// Добавим запрос в список
			$this->AddRequest(
				$name,
				'get_statistics_by_hour',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'offer_type' => $offer_type,
					'currency' => $currency,
					'page' => $page,
					'per_page' => $per_page
				)
			);
			return TRUE;
		}
		//======================================================================

		//======================================================================
		// Запрос в статистику с группировкой по кретивам
		public function AddRequestGetStatisticsByCreative($name, $creative_id = 0, $date_from = '', $date_to = '', $offer_type = '', $currency = 'USD',$page = 1, $per_page = 20) {
			// Добавим запрос в список
			$this->AddRequest(
				$name,
				'get_statistics_by_creative',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'offer_type' => $offer_type,
					'currency' => $currency,
					'page' => $page,
					'per_page' => $per_page
				)
			);
			return TRUE;
		}
		//======================================================================

		//======================================================================
		// Запрос в статистику с группировкой по sub
		public function AddRequestGetStatisticsBySub($name, $creative_id, $date_from = '', $date_to = '', $offer_type = '', $currency = 'USD',$page = 1, $per_page = 20) {
			// Добавим запрос в список
			$this->AddRequest(
				$name,
				'get_statistics_by_sub',
				array(
					'creative_id' => $creative_id,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'offer_type' => $offer_type,
					'currency' => $currency,
					'page' => $page,
					'per_page' => $per_page
				)
			);
			return TRUE;
		}
		//======================================================================
        
        //======================================================================
        // Выполнение всех запросов
        public function RunRequests() {
		// Сбрасываем переменные
		$this->request_results = array();
		$this->last_error = '';
        
		// Структура для отправки запросса
		$data = array(
			'user_api_key' => $this->user_api_key,
			'api_version' => self::EPN_CLIENT_API_VERSION,
			'requests' => $this->prepared_requests,
		);
		// Строка запроса
		$post_data = json_encode($data);
		// Подпись запроса
		$data_sign = md5($this->user_private_key . $post_data);

            echo "<pre>";
            var_dump($post_data);
            var_dump($data_sign);
            die;

		// Будем использовать cURL
		$ch = curl_init();
		// Выполняем запрос
		curl_setopt($ch, CURLOPT_URL,            self::EPN_API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST,           1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $post_data); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
				"Content-Type: text/plain",
				"X-EPN-Digest-Sign: $data_sign",
			));
		$result = curl_exec($ch);
		$curl_error_msg = curl_error($ch);
		//print "<!-- $curl_error_msg\n\n$result -->\n";
		// Если http-запрос обработан с ошибкой
		if ($curl_error_msg != '') {
			$this->last_error = $curl_error_msg;
		}
		else {
			// Парсим данные
			$result_data = json_decode($result, TRUE);
			$this->last_error = isset($result_data['error']) ? $result_data['error'] : '';
			$this->request_results = isset($result_data['results']) && is_array($result_data['results']) ? $result_data['results'] : array();
		}
		// Независимо от результата сбрасываем список запросов
		$this->prepared_requests = array();
		// Если не было ошибок то всё хорошо
		return $this->last_error == '' ? TRUE : FALSE;
		
	}
	//======================================================================

		//======================================================================
		// Получение отклика
		public function GetRequestResult($name) {
			return isset($this->request_results[$name]) ? $this->request_results[$name] : FALSE;
		}
			//======================================================================

		//======================================================================
		// Информация о последней ошибке
		public function LastError() {
			return $this->last_error;
		}
		//======================================================================
}


//создаем объект api указав в конструкторе ключи
$api = new clEpnCabinetAccess('9e3914b3dba4b662de83dae202d4bad3','f7e90cf3d254ca31750302efc51c1b40');
//добавляем запросы
//$api->AddRequestGetStatisticsByDay('day_stats',0,'2015-02-01','2015-02-30','aliexpress','USD',1,100);
$api->AddRequestCheckLink('link', 'https://google.com');
//выполняем запросы
$api->RunRequests();
//дампим результаты
//print_r($api->GetRequestResult('day_stats'));
print_r($api->GetRequestResult('link'));