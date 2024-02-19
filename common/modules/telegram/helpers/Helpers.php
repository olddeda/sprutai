<?php
namespace common\modules\telegram\helpers;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;

class Helpers {
	
	/**
	 * Dumper, to send variable contents to the passed chat_id.
	 *
	 * Used to log and send variable dump (var_export) to the developer or any Telegram chat ID provided.
	 * Will return ServerResponse object for later use.
	 *
	 * @param mixed $data
	 * @param int   $chat_id
	 *
	 * @return \Longman\TelegramBot\Entities\ServerResponse
	 * @throws \Longman\TelegramBot\Exception\TelegramException
	 */
	public static function dump($data, $chat_id = 357615556) {
		if (is_object($data))
			$dump = self::features_var_export($data);
		else
			$dump = var_export($data, true);
		
		// Write the dump to the debug log, if enabled.
		TelegramLog::debug($dump);
		
		// Send the dump to the passed chat_id.
		if ($chat_id !== null || (property_exists(self::class, 'dump_chat_id') && $chat_id = self::$dump_chat_id)) {
			$result = Request::sendMessage([
				'chat_id'                  => $chat_id,
				'text'                     => $dump,
				'disable_web_page_preview' => true,
				'disable_notification'     => true,
			]);
			
			if ($result->isOk()) {
				return $result;
			}
			
			TelegramLog::error('Var not dumped to chat_id %s; %s', $chat_id, $result->printError());
		}
		
		return Request::emptyResponse();
	}
	
	private static function features_var_export($var, $prefix = '', $init = TRUE, $count = 0) {
		if ($count > 5) {
			// Recursion depth reached.
			return '...';
		}
		
		if (is_object($var)) {
			$output = method_exists($var, 'export') ? $var->export() : self::features_var_export((array) $var, '', FALSE, $count+1);
		}
		else if (is_array($var)) {
			if (empty($var)) {
				$output = 'array()';
			}
			else {
				$output = "array(\n";
				foreach ($var as $key => $value) {
					// Using normal var_export on the key to ensure correct quoting.
					$output .= "  " . var_export($key, TRUE) . " => " . self::features_var_export($value, '  ', FALSE, $count+1) . ",\n";
				}
				$output .= ')';
			}
		}
		else if (is_bool($var)) {
			$output = $var ? 'TRUE' : 'FALSE';
		}
		else if (is_int($var)) {
			$output = intval($var);
		}
		else if (is_numeric($var)) {
			$floatval = floatval($var);
			if (is_string($var) && ((string) $floatval !== $var)) {
				// Do not convert a string to a number if the string
				// representation of that number is not identical to the
				// original value.
				$output = var_export($var, TRUE);
			}
			else {
				$output = $floatval;
			}
		}
		else if (is_string($var) && strpos($var, "\n") !== FALSE) {
			// Replace line breaks in strings with a token for replacement
			// at the very end. This protects whitespace in strings from
			// unintentional indentation.
			$var = str_replace("\n", "***BREAK***", $var);
			$output = var_export($var, TRUE);
		}
		else {
			$output = var_export($var, TRUE);
		}
		
		if ($prefix) {
			$output = str_replace("\n", "\n$prefix", $output);
		}
		
		if ($init) {
			$output = str_replace("***BREAK***", "\n", $output);
		}
		
		return $output;
	}
}