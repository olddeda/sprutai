<?php
namespace client\controllers;

/**
 * Class PortfolioController
 * @package client\controllers
 */
class PortfolioController extends ContentController
{
	/**
	 * @var string
	 */
	public $modelClass = '\common\modules\content\models\Portfolio';
	
	/**
	 * @var string
	 */
	public $routeView = '/portfolio/view';
}