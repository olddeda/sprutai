<?php

return [

	// General
	'ERROR_NONE' => 'Ошибок нет',
	'ERROR_UNKNOWN' => 'Неизвестная ошибка',
	'ERROR_ACCESS_DENIED' => 'Недостаточно прав',
	'ERROR_EMPTY_PARAMS' => 'Не переданы обязательные параметры',
	'ERROR_INVALID_PARAMS' => 'Переданы ошибочные параметры',
	'ERROR_REGISTRATION_DISABLED' => 'Регистрация временно отключена',
	'ERROR_FORGOT_DISABLED' => 'Восстановление пароля времено отключено',

	// User
	'ERROR_USER_FIELD_EMPTY_EMAIL' => 'Не указан обязательный параметр «email»',
	'ERROR_USER_FIELD_EMPTY_USERNAME' => 'Не указан обязательный параметр «username»',
	'ERROR_USER_FIELD_EMPTY_PASSWORD' => 'Не указан обязательный параметр «password»',
	'ERROR_USER_FIELD_EMPTY_FIRST_NAME' => 'Не указан обязательный параметр «first_name»',

	'ERROR_USER_FIELD_SHORT_USERNAME' => 'Значение «username» должно содержать минимум 4 символа',
	'ERROR_USER_FIELD_SHORT_PASSWORD' => 'Значение «password» должно содержать минимум 4 символа',
	'ERROR_USER_FIELD_SHORT_FIRST_NAME' => 'Значение «first_name» должно содержать минимум 4 символа',
	'ERROR_USER_FIELD_SHORT_LAST_NAME' => 'Значение «last_name» должно содержать минимум 4 символа',
	'ERROR_USER_FIELD_SHORT_MIDDLE_NAME' => 'Значение «middle_name» должно содержать минимум 4 символа',

	'ERROR_USER_FIELD_LONG_USERNAME' => 'Значение «username» должно содержать максимум 20 символов',
	'ERROR_USER_FIELD_LONG_PASSWORD' => 'Значение «password» должно содержать максимум 255 символа',
	'ERROR_USER_FIELD_LONG_FIRST_NAME' => 'Значение «first_name» должно содержать максимум 255 символа',
	'ERROR_USER_FIELD_LONG_LAST_NAME' => 'Значение «last_name» должно содержать максимум 255 символа',
	'ERROR_USER_FIELD_LONG_MIDDLE_NAME' => 'Значение «middle_name» должно содержать максимум 255 символа',

	'ERROR_USER_FIELD_INVALID_EMAIL' => 'Указан ошибочный параметр «email»',
	'ERROR_USER_FIELD_INVALID_USERNAME' => 'Указан ошибочный параметр «username»',
	'ERROR_USER_FIELD_INVALID_PASSWORD' => 'Неправильный логин или пароль',
	'ERROR_USER_FIELD_INVALID_PHONE' => 'Указан ошибочный параметр «phone»',

	'ERROR_USER_FIELD_EXISTS_EMAIL' => 'Пользователь с таким E-mail уже существует',
	'ERROR_USER_FIELD_EXISTS_USERNAME' => 'Пользователь с таким логином уже существует',

	'ERROR_USER_FIELD_NOT_EXISTS_EMAIL' => 'Пользователь с таким E-mail не зарегистрирован',
	'ERROR_USER_FIELD_NOT_EXISTS_TOKEN' => 'Токен неправильный или устарел',

	'ERROR_USER_STATUS_UNCONFIRMED' => 'Аккаунт не активирован',
	'ERROR_USER_STATUS_BLOCKED' => 'Аккаунт заблокирован',
	'ERROR_USER_STATUS_DELETED' => 'Аккаунт удален',
    
    'ERROR_MAILING_USER_ACCESS' => 'Доступ запрещен',
    'ERROR_MAILING_USER_EMPTY_EMAIL' => 'Не указан обязательный параметр «email»',
    'ERROR_MAILING_USER_EXISTS' => 'Вы уже подавали заявку на уведомление',
];