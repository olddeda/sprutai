<?php

namespace client\controllers\user;

use common\modules\user\controllers\SecurityController as Controller;

/**
 * Controller that manages user authentication process.
 *
 * @property \common\modules\user\Module $module
 */
class SecurityController extends Controller
{
	public $layoutContent = 'content';
	
	public $rememberUrl = false;
}
