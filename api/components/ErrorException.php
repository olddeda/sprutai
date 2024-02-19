<?php
namespace api\components;

use Yii;
use yii\base\UserException;

use api\helpers\enum\Error;

use yii\base\ExitException;
use yii\web\HttpException;

/**
 * Class ErrorException
 * @package api\components
 */
class ErrorException extends HttpException
{
	/**
	 * Constructor.
	 * @param integer|object $obj error code
	 * @param \Exception $previous The previous exception used for the exception chaining.
	 */
	public function __construct($obj, $statusCode = 400) {
		$code = Error::ERROR_EMPTY_PARAMS;
		$message = null;
		if (is_numeric($obj)) {
			$code = $obj;
		}
		else if (is_object($obj) && $obj->errors) {
			$errors = $obj->errors;
			reset($errors);
			$code = $errors[key($errors)][0];
			if (!is_numeric($code)) {
				$message = $code;
				$code = Error::ERROR_UNKNOWN;
			}
		}
		if (!$message)
			$message = Error::getLabel($code);
   
		parent::__construct($statusCode, $message, $code);
	}

	/**
	 * @return string the user-friendly name of this exception
	 */
	public function getName() {
		return Error::getItem($this->code);
	}
}
