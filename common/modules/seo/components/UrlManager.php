<?php
namespace common\modules\seo\components;

use Yii;
use yii\web\UrlManager as BaseUrlManager;
use yii\helpers\ArrayHelper;
use common\modules\base\helpers\Url;

use common\modules\base\components\Debug;

use common\modules\seo\models\SeoUri;
use common\modules\seo\models\SeoUriHistory;

class UrlManager extends BaseUrlManager
{
	/**
	 * @param \yii\web\Request $request
	 *
	 * @return array|bool
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\db\Exception
	 */
	public function parseRequest($request) {
		$uri = rtrim($request->getPathInfo(), '/');
		if (!strlen($uri))
			return parent::parseRequest($request);
		
		$pathInfo = pathinfo($request->url);
		$queryStr = parse_url($request->url, PHP_URL_QUERY);
		
		$uriFormatted = '/'.trim($uri, '/').'/';
		
		// Find uri in table
		$rs = Yii::$app->db->createCommand("
			SELECT *, TRIM(BOTH '/' FROM uri) AS uri
			FROM ".SeoUri::tableName()."
			WHERE uri = :uri
		", [
			':uri' => $uriFormatted,
		])->queryOne();
		
		if (!$rs) {
			$rs = Yii::$app->db->createCommand("
				SELECT su.*, TRIM(BOTH '/' FROM su.uri) AS uri
				FROM ".SeoUriHistory::tableName()." AS suh
				LEFT JOIN ".SeoUri::tableName()." AS su ON su.id = suh.seo_uri_id
				WHERE suh.uri = :uri
			", [
				':uri' => $uriFormatted,
			])->queryOne();
		}
		
		if ($rs) {
			$params = (strlen($rs['module_params'])) ? unserialize($rs['module_params']) : [];
			if ($params && is_array($params)) {
				foreach ($params as $key => $val)
					$_GET[$key] = $val;
			}
			
			// Case matches exactly
			if (strcmp($uri, $rs['uri']) == 0) {
				$route = $rs['module_route'];
				return [$route, $params];
			}
			
			$url = '/'.$rs['uri'].'/';
			if ($queryStr)
				$url .= '?'.$queryStr;
			
			Yii::$app->response->redirect($url, 301)->send();
			exit(0);
		}
		
		$parseRequest = parent::parseRequest($request);
		if (!$parseRequest)
			return false;
		
		$ret = current($parseRequest);
		
		// Again find uri
		$parts = explode('/', $ret);
		$moduleAction = (count($parts) > 1) ? $ret : $ret.'/index';
		
		$rs = Yii::$app->db->createCommand("
			SELECT *, TRIM(BOTH '/' FROM uri) AS uri
			FROM ".SeoUri::tableName()."
			WHERE module_route = :module_route
			AND module_id = :module_id
		", [
			':module_route' => $moduleAction,
			':module_id' => (isset($_GET['id'])) ? $_GET['id'] : 0,
		])->queryOne();
		if ($rs) {
			$url = '/'.$rs['uri'].'/';
			if ($queryStr)
				$url .= '?'.$queryStr;
			
			Yii::$app->response->redirect($url, 301)->send();
			exit(0);
		}
		
		return $parseRequest;
	}
	
	/**
	 * @param array|string $params
	 *
	 * @return string
	 */
	public function createUrl($params) {
		if (!isset($params['seo'])) {
			return parent::createUrl($params);
		}
		
		unset($params['seo']);
		$oldParams = $params;
		
		$params = (array) $params;
		$anchor = isset($params['#']) ? '#' . $params['#'] : '';
		unset($params['#'], $params[$this->routeParam]);
		
		$route = trim($params[0], '/');
		unset($params[0]);
		
		$routeUri = $route.'/'.(isset($params['id']) ? $params['id'] : 0);
		$routeFind = SeoUri::uriForRoute($routeUri);
		if ($routeFind) {
			$url = Yii::$app->urlManager->baseUrl.'/'.$routeFind.'/';
			
			$routeParams = SeoUri::moduleParamsForRoute($routeUri);
			if (count($routeParams)) {
				foreach ($routeParams as $key => $val) {
					if (isset($params[$key])) {
						unset($params[$key]);
					}
				}
			}
			
			if (count($params)) {
				$url .= '?'.Url::createPathInfo($params, '=', '&');
			}
			
			return $url;
		}
		
		return parent::createUrl($oldParams);
	}
}