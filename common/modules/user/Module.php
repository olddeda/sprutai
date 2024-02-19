<?php
namespace common\modules\user;

use yii\base\Module as BaseModule;

/**
 * Class Module
 * @package comment\modules\user
 */
class Module extends BaseModule
{
    const VERSION = '0.9.5';

    /** Email is changed right after user enter's new email address. */
    const STRATEGY_INSECURE = 0;

    /** Email is changed after user clicks confirmation link sent to his new email address. */
    const STRATEGY_DEFAULT = 1;

    /** Email is changed after user clicks both confirmation links sent to his old and new email addresses. */
    const STRATEGY_SECURE = 2;

	/**
	 * @var string an ID that uniquely identifies this module among other modules which have the same [[module|parent]].
	 */
	public $id = 'user';

    /**
	 * @var bool Whether to show flash messages.
	 */
    public $enableFlashMessages = true;

    /**
	 * @var bool Whether to enable registration.
	 */
    public $enableRegistration = true;

    /**
	 * @var bool Whether to remove password field from registration form.
	 */
    public $enableGeneratingPassword = false;

    /**
	 * @var bool Whether user has to confirm his account.
	 */
    public $enableConfirmation = true;

    /**
	 * @var bool Whether to allow logging in without confirmation.
	 */
    public $enableUnconfirmedLogin = false;

    /**
	 * @var bool Whether to enable password recovery.
	 */
    public $enablePasswordRecovery = true;

    /**
	 * @var int Email changing strategy.
	 */
    public $emailChangeStrategy = self::STRATEGY_DEFAULT;

    /**
	 * @var int The time you want the user will be remembered without asking for credentials.
	 */
    public $rememberFor = 31536000; // 1 year

    /**
	 * @var int The time before a confirmation token becomes invalid.
	 */
    public $confirmWithin = 86400; // 24 hours

    /**
	 * @var int The time before a recovery token becomes invalid.
	 */
    public $recoverWithin = 21600; // 6 hours

	/**
	 * @var int The time before a recovery token becomes invalid.
	 */
	public $apiWithin = 2678400; // 1 month

    /**
	 * @var int Cost parameter used by the Blowfish hash algorithm.
	 */
    public $cost = 10;

    /**
	 * @var array An array of administrator's usernames.
	 */
    public $admins = [];
	
	/**
	 * @var array An array of administrator's usernames.
	 */
	public $adminsIds = [3, 6];

    /**
	 * @var array Mailer configuration
	 */
    public $mailer = [];

    /**
	 * @var array Model map
	 */
    public $modelMap = [];

    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'user';

    /** @var array The rules to be used in URL management. */
    public $urlRules = [
		'admin/update-profile/<id:\d+>'					=> 'admin/update-profile',
		'admin/update-grounds/<id:\d+>'					=> 'admin/update-grounds',
		'admin/<action:\w+>/<id:\d+>'                  	=> 'admin/<action>',
		'admin/<action:\w+>'                  			=> 'admin/<action>',
		'admin'											=> 'admin/index',
		'signin'										=> 'security/signin',
		'signup/confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'signup/confirm',
		'signup'										=> 'signup/index',
		'forgot'										=> 'security/forgot',
		'profile'										=> 'profile/index',
		'settings'										=> 'settings/profile',
        '<id:\d+>'                               		=> 'profile/show',
        '<action:(signin|logout)>'               		=> 'security/<action>',
        '<action:(signup|resend)>'             	 		=> 'signup/<action>',
        'forgot'                                 		=> 'forgot/index',
        'forgot/<id:\d+>/<code:[A-Za-z0-9_-]+>'  		=> 'forgot/reset',
        'settings/<action:\w+>'                  		=> 'settings/<action>',
		'settings/telegram/connect'						=> 'settings/telegram-connect',
		'setting/telegram/disconnect'					=> 'settings/telegram-disconnect',
    ];
}
