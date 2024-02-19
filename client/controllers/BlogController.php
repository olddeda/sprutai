<?php
namespace client\controllers;


/**
 * Class BlogController
 * @package client\controllers
 */
class BlogController extends ContentController
{
	/**
	 * @var string
	 */
	public $modelClass = '\common\modules\content\models\Blog';
	
	/**
	 * @var string
	 */
	public $routeView = '/blog/view';
}