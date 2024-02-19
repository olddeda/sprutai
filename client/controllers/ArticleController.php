<?php
namespace client\controllers;

/**
 * Class ArticleController
 * @package client\controllers
 */
class ArticleController extends ContentController
{
	/**
	 * @var string
	 */
	public $modelClass = '\common\modules\content\models\Article';
	
	/**
	 * @var string
	 */
	public $routeView = '/article/view';
}