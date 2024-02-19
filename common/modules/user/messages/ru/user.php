<?php

return [
	
	// Titles
	'title' => 'Пользователи',
	'title_signin' => 'Авторизация',
	'title_signin_social' => 'Вход с помощью социальной сети',
	'title_signup' => 'Регистрация',
	'title_signup_confirm' => 'Активация аккаунта',
	'title_signup_confirm_request' => 'Повторная отправка активации',
	'title_forgot' => 'Восстановление пароля',
	'title_forgot_reset' => 'Сброс пароля',
	
	// Menu
	'menu_account_details' => 'Аккаунт',
	'menu_profile_details' => 'Профиль',
	'menu_grounds_details' => 'Участки',
	'menu_information' => 'Информация',
	'menu_block' => 'Блокировать',
	'menu_unblock' => 'Разблокировать',
	'menu_delete' => 'Удалить',
	'menu_assignments' => 'Права',
	
	// Fields
	'field_id' => 'ID',
	'field_company_id' => 'Компания',
	'field_login' => 'E-mail',
	'field_username' => 'Логин',
	'field_email' => 'E-mail',
	'field_password' => 'Пароль',
	'field_new_password' => 'Новый пароль',
	'field_new_password_hint' => 'Введите новый пароль',
	'field_current_password' => 'Текущий пароль',
	'field_registration_ip' => 'IP при регистрации',
	'field_created_at' => 'Дата регистрации',
	'field_fio' => 'Ф.И.О.',
	'field_phone' => 'Телефон',
	'field_status' => 'Статус',
	'field_items' => 'Назначения',
	'field_remember_me_next_time' => 'Запомнить',
	'field_another_computer' => 'Чужой компьютер',
	'field_avatar' => 'Аватар',
	'field_address_country_city' => 'Страна, город',
	'field_telegram' => 'Telegram',
	
	// Placeholders
	'placeholder_login' => 'Ваш E-mail',
	'placeholder_email' => 'Ваш E-mail',
	'placeholder_username' => 'Ваш логин',
	'placeholder_password' => 'Ваш пароль',
	'placeholder_password_new' => 'Ваш новый пароль',
	
	// Status
	'status_confirmed' => 'Подтвержден',
	'status_unconfirmed' => 'Не подтвержден',
	'status_blocked' => 'Заблокирован',
	'status_no_blocked' => 'Не заблокирован',
	
	// Buttons
	'button_update' => 'Сохранить',
	'button_block' => 'Блокировать',
	'button_unblock' => 'Разблокировать',
	'button_confirm_activation' => 'Подтвердить',
	'button_remove' => 'Удалить',
	
	// Headers
	'header_confirmation' => 'Статус',
	'header_block' => 'Блокировка',
	
	// Confirm
	'confirm_activate' => 'Вы уверены, что хотите подтвердить аккаунт пользователя?',
	'confirm_block' => 'Вы уверены, что хотите заблокировать пользователя?',
	'confirm_unblock' => 'Вы уверены, что хотите разблокировать пользователя?',
	'confirm_delete' => 'Вы уверены, что хотите удалить этого пользователя?',
	
	// Formats
	'format_created_at' => '{0, date, dd-MM-YYYY HH:mm}',
	'format_confirmed_at' => 'Активирован {0, date, dd MMMM, YYYY HH:mm}',
	'format_blocked_at' => 'Заблокирован {0, date, dd MMMM, YYYY HH:mm}',
	'format_date_time' => '{0, date, dd MMMM YYYY HH:mm}',
	
	// Tooltips
	'tooltip_company' => 'Перейти к компании',
	'tooltip_email' => 'Написать письмо',
	'tooltip_fio' => 'Перейти к профилю',
	
	// Links
	'link_signup' => 'Создать аккаунт',
	'link_forgot_password' => 'Забыли пароль?',
	'link_signin' => 'Войти',
	'link_signup' => 'Зарегистрироваться',
	'link_didnt_receive_confirmation_message' => 'Не пришло письмо?',
	'link_dont_have_an_account' => 'Нет аккаунта? Зарегистрируйтесь!',
	'link_already_registered' => 'Уже зарегистрированы?',
	'link_continue' => 'Продолжить',
	'link_finish' => 'Завершить',
	
	// Socials
	'social_facebook' => 'Facebook',
	'social_twitter' => 'Twitter',
	'social_vkontakte' => 'ВКонтакте',
	'social_odnoklassniki' => 'Одноклассники',
	'social_yandex' => 'Яндекс',
	'social_google' => 'Google',
	'social_telegram' => 'Телеграмм',
	'social_github' => 'GitHub',
	
	// Other
	'not_set' => '(не задано)',
	
	// Messages
	'message_user_has_been_created' => 'Пользователь был создан',
	'message_user_has_been_confirmed' => 'Пользователь был активирован',
	'message_user_has_been_deleted' => 'Пользователь был удален',
	'message_user_has_been_blocked' => 'Пользователь был блокирован',
	'message_user_has_been_unblocked' => 'Пользователь был разблокирован',
	'message_user_details_have_been_updated' => 'Аккаунт пользователя был обновлен',
	'message_user_profile_details_have_been_updated' => 'Профиль пользователя был обновлен',
	'message_you_can_not_remove_your_own_account' => 'Вы не можете удалить свой собственный аккаунт',
	'message_you_can_not_block_your_own_account' => 'Вы не можете заблокировать свой собственный аккаунт',
	'message_credentials_will_be_sent_to_the_user_by_email' => 'Данные для входа будут отправлены пользователю на почту',
	'message_a_password_will_be_generated_automatically_if_not_provided' => 'Если вы хотите, чтобы пароль был сгенерирован, оставьте поле пустым',
	'message_you_can_assign_multiple_roles_or_permissions_to_user_by_using_the_form_below' => 'Вы можете добавить пользователю несколько ролей или разрешений, используя форму ниже',
	'message_thank_you_registration_is_now_complete' => 'Ваш аккаунт был успешно активирован.',
	'message_something_went_wrong_and_your_account_has_not_been_confirmed' => 'Что-то пошло не так, и ваш аккаунт не был активирован.',
	'message_the_confirmation_link_is_invalid_or_expired' => 'Ссылка для активации аккаунта неправильна или она устарела. Вы можете запросить новую.',
	'message_your_confirmation_token_is_invalid_or_expired' => 'Ваша ссылка устарела или является ошибочной',
	'message_an_error_occurred_processing_your_request' => 'Во время выполнения запроса произошла ошибка',
	'message_awesome_almost_there_now_you_need_to_click_the_confirmation_link_sent_to_your_old_email_address' => 'Почти готово! Осталось перейти по ссылке, отправленной на ваш старый E-mail',
	'message_awesome_almost_there_now_you_need_to_click_the_confirmation_link_sent_to_your_new_email_address' => 'Почти готово! Осталось перейти по ссылке, отправленной на ваш новый E-mail',
	'message_your_email_address_has_been_changed' => 'Ваш email был успешно изменен',
	'message_something_went_wrong' => 'Что-то пошло не так',
	'message_your_account_has_been_connected' => 'Аккаунт был успешно подключен',
	'message_this_account_has_already_been_connected_to_another_user' => 'Этот аккаунт уже был привязан к другой учетной записи',
	'message_there_is_no_user_with_this_email_address' => 'Нет пользователя с таким E-mail',
	'message_you_need_to_confirm_your_email_address' => 'Вы должны активировать аккаунт',
	'message_an_email_has_been_sent_with_instructions_for_resetting_your_password' => 'Вам отправлено письмо с инструкциями по смене пароля.',
	'message_your_password_has_been_changed_successfully' => 'Ваш пароль был успешно изменен.',
	'message_an_error_occurred_and_your_password_has_not_been_changed_please_try_again_later' => 'Что то пошло не так при смене пароля, попробуйте пожалуйста повторить позже.',
	'message_this_account_has_already_been_confirmed' => 'Этот аккаунт уже был активирован',
	'message_a_message_has_been_sent_to_your_email_address_it_contains_a_confirmation_link_that_you_must_click_to_complete_registration' => 'Вам было отправлено письмо. Оно содержит ссылку, по которой вы должны перейти, чтобы завершить регистрацию.',
	'message_a_confirmation_message_has_been_sent_to_your_new_email_address' => 'Ссылка для подтверждения была отправлена вам на почту',
	'message_me_we_have_sent_confirmation_links_to_both_old_and_new_email_addresses_you_must_click_both_links_to_complete_your_request' => 'Мы отправили письма на ваш старый и новый почтовые ящики. Вы должны перейти по обеим, чтобы завершить процесс смены адреса.',
	'message_your_account_has_been_created_and_a_message_with_further_instructions_has_been_sent_to_your_email' => 'Ваш аккаунт был создан и сообщение с дальнейшими инструкциями отправлено на ваш E-mail',
	'message_recovery_message_sent' => 'Письмо для сброса пароля было отправлено',
	'message_invalid_or_expired_link' => 'Ссылка неправильна или устарела',
	'message_password_has_been_changed' => 'Пароль был изменен',
	'message_recovery_link_is_invalid_or_expired' => 'Ссылка для смены пароля неправильна или устарела. Пожалуйста, попробуйте запросить новую ссылку.',
	'message_your_account_has_been_created' => 'Ваш аккаунт был создан',
	'message_a_new_confirmation_link_has_been_sent' => 'Ссылка для активации аккаунта была отправлена вам на почту',
	'message_your_profile_has_been_updated' => 'Настройки профиля были успешно сохранены',
	'message_your_account_have_been_updated' => 'Настройки аккаунта были успешно сохранены',
	'message_you_can_connect_multiple_accounts_to_be_able_to_log_in_using_them' => 'Вы можете использовать его вместе с вашим email или логином для входа',
	'message_in_order_to_finish_your_registration_we_need_you_to_enter_your_email_address' => 'Чтобы закончить регистрацию, вы должны ввести свой E-mail',
	'message_in_order_to_finish_your_registration_we_need_you_to_enter_following_fields' => 'Чтобы закончить регистрацию, вы должны ввести свой E-mail и логин',
	'message_if_you_already_registered_sign_in_and_connect_this_account_on_settings_page' => 'Если вы уже зарегистрированы, войдите и подключите аккаунт в настройках',
	'message_need_account' => 'Вы новенький?',
	
	// Errors
	'error_not_exists' => 'Пользователь не существует или был удален',
	'error_this_username_has_already_been_taken' => 'Это имя пользователя уже используется',
	'error_this_email_address_has_already_been_taken' => 'Этот E-mail уже используется',
	'error_current_password_is_not_valid' => 'Текущий пароль введен неправильно',
	'error_invalid_login_or_password' => 'Неправильный логин или пароль',
	'error_you_need_to_confirm_your_email_address' => 'Вы должны активировать аккаунт',
	'error_your_account_has_been_blocked' => 'Ваш аккаунт был блокирован',
	'error_your_account_has_been_deleted' => 'Ваш аккаунт был удален',
	'error_access' => 'Пользователь не существует или у вас нет прав доступа к нему',
];