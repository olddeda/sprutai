<?php
namespace api\components;

use yii\rest\Controller as BaseController;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;

use api\helpers\enum\Error;

/**
 * Class Controller
 * @package api\components
 */
class Controller extends BaseController
{
	/**
	 * @var array
	 */
	public $authMethods = [
		'yii\rest\HttpBearerAuth',
	];
	
	/**
	 * @var array
	 */
	public $serializer = [
		'class' => 'api\components\Serializer',
		'collectionEnvelope' => 'items',
		'modelEnvelope' => 'item'
	];

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			'corsFilter' => [
				'class' => Cors::class,
				'cors' => [
					'Origin' => ['*'],
					'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
				],
				'actions' => [
					'*' => [
						'Access-Control-Allow-Credentials' => true,
					]
				]
			],
			'authenticator' => [
				'class' => HttpBearerAuth::class,
			],
			'access' => [
				'class' => AccessControl::class,
				'denyCallback' => function ($rule, $action) {
					throw new ErrorException(Error::ERROR_ACCESS_DENIED, 401);
				}
			],
		];
	}
}