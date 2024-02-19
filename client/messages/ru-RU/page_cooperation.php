<?php

return [
	
	// Fields
	'field_name' => 'Ваше имя',
	'field_email' => 'Ваш E-mail',
	'field_phone' => 'Ваш телефон',
	'field_body' => 'Сообщение',
	'field_captcha' => 'Проверочный код',
	
	// Mail
	'mail_subject' => 'Предложение сотрудничества',
	'mail_body' => '<b>Новое предложение сотрудничества</b>'.PHP_EOL.PHP_EOL.'<b>Имя:</b> {name}'.PHP_EOL.'<b>E-mail:</b> {email}'.PHP_EOL.'<b>Телефон:</b> {phone}'.PHP_EOL.'<b>Сообщение:</b>'.PHP_EOL.'{body}',
	
	// Messages
	'message_send_success' => 'Ваше сообщение было успешно отправлено',
	
	// Buttons
	'button_submit' => 'Отправить',
	
	// Errors
	'error_empty_captcha' => 'Подтвердите что вы не робот',
];