<?php

return [
	
	// Titles
	'title' => 'Приходы',
	'title_index' => 'Приходы',
	'title_view' => 'Просмотр прихода',
	'title_create' => 'Создание прихода',
	'title_update' => 'Редактирование прихода',
	'title_delete' => 'Удаление прихода',
	'title_log' => 'Лог изменений прихода',
	
	// Headers
	'header_general' => 'Основные данные',
	'header_user' => 'Данные о пользователе',
	'header_accruals' => 'Начисления',
	'header_other' => 'Дополнительные данные',
	
	// Fields
	'field_id' => 'ID',
	'field_kind' => 'Вид платежа',
	'field_title' => 'Название',
	'field_module_type' => 'Тип модуля',
	'field_module_id' => 'ID модуля',
	'field_payment_type_id' => 'Тип',
	'field_user_id' => 'Пользователь',
	'field_provider_id' => 'ID транзакции',
	'field_provider_error' => 'Статус транзакции',
	'field_price' => 'Сумма',
	'field_price_tax' => 'Сумма с комиссией',
	'field_tax' => 'Коммисия',
	'field_descr' => 'Примечание',
	'field_comment' => 'Комментарий',
	'field_pickup' => 'Самовывоз',
    'field_date' => 'Дата',
    'field_datetime' => 'Дата и время',
	'field_status' => 'Статус',
	'field_created_by' => 'Создан пользователем',
	'field_updated_by' => 'Изменен пользователем',
    'field_date_at' => 'Дата платежа',
	'field_created_at' => 'Дата создания',
	'field_updated_at' => 'Дата изменения',
    'field_user_fio' => 'Ф.И.О.',
	'field_user_lastname' => 'Фамилия',
	'field_user_firstname' => 'Имя',
	'field_user_middlename' => 'Отчество',
    'field_user_username' => 'Логин',
    'field_user_email' => 'E-mail',
	'field_user_phone' => 'Телефон',
	'field_user_address' => 'Адрес',
	'field_user_telegram' => 'Telegram',
	'field_user_github' => 'Github',
	'field_user_from' => 'От',
	'field_type_title' => 'Вид платежа',
	
	// Prompt
	'prompt_type' => 'Выберите тип',
	
	// Confirms
	'confirm_delete' => 'Вы действительно хотите удалить этот приход?',
	'confirm_delete_name' => 'Вы действительно хотите удалить приход - <strong>«{title}»</strong>?',
	
	// Tooltips
	'tooltip_user' => 'Перейти к пользователю',
	
	// Tip
	'tip_confirmation' => 'Нажимая кнопку «Перейти к оплате»<br>вы соглашаетесь с {link}',
	'tip_confirmation_link' => 'Условиями оплаты',
	
	// Editable
	
	// Other
	'withdrawal_author_month' => 'Выплата начислений за {date}',
	
	// Messages
	'message_create_success' => 'Платеж был успешно создан',
	'message_update_success' => 'Платеж был успешно обновлен',
	'message_delete_success' => 'Платеж был успешно удален',
	'message_payment_success' => 'Оплата платежа была успешно осуществлена',
	'message_payment_failed' => 'При оплате платежа произошла ошибка',
	'message_payment_failed_min_price' => 'Указанная сумма платежа ниже установленной минимальной',
	
	// Errors
	'error_not_exists' => 'Приход не найден или был удален',
	'error_empty_list' => 'Список приходов пуст',
];