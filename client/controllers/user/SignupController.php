<?php

namespace client\controllers\user;

use common\modules\user\controllers\SignupController as Controller;

/**
 * Controller that manages user authentication process.
 *
 * @property \common\modules\user\Module $module
 */
class SignupController extends Controller
{
	public $layoutContent = 'content';
	
	public $rememberUrl = false;
}
