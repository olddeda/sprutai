<?php
namespace client\controllers;

/**
 * Class NewsController
 * @package client\controllers
 */
class NewsController extends ContentController
{
	/**
	 * @var string
	 */
	public $modelClass = '\common\modules\content\models\News';
	
	/**
	 * @var string
	 */
	public $routeView = '/news/view';
}