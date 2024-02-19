<?php

return [
	
	// Titles
	'title' => 'Версии',
	'title_index' => 'Версии',
	'title_view' => 'Просмотр версии',
	'title_create' => 'Создание версии',
	'title_update' => 'Редактирование версии',
	'title_delete' => 'Удаление версии',
	'title_log' => 'Лог изменений версии',
	'title_select_provider' => 'Выбор провайдера',
	'title_select_repository' => 'Выбор репозитория',
	'title_select_release' => 'Выбор версии',
	
	// Headers
	'header_general' => 'Основные данные',
	'header_other' => 'Дополнительные данные',
	'header_select_provider' => 'Выберите провайдер',
	'header_select_provider_empty' => 'Внимание!',
	'header_select_repository' => 'Выберите репозиторий',
	'header_select_repository_empty' => 'Внимание!',
	'header_select_release' => 'Выберите версию',
	'header_select_release_empty' => 'Внимание!',
	
	// Fields
	'field_id' => 'ID',
	'field_plugin_id' => 'Версия',
	'field_version' => 'Версия',
	'field_url' => 'Ссылка',
	'field_text' => 'Описание',
    'field_date' => 'Дата',
    'field_datetime' => 'Дата и время',
	'field_latest' => 'Текущая',
	'field_file' => 'ZIP архив',
	'field_status' => 'Статус',
	'field_created_by' => 'Создан пользователем',
	'field_updated_by' => 'Изменен пользователем',
    'field_date_at' => 'Дата версии',
	'field_created_at' => 'Дата создания',
	'field_updated_at' => 'Дата изменения',
	
	// Field hint
	'field_hint_version' => 'Пример: 1.0.0',
	'field_hint_url' => 'Ссылка на репозиторий Github',
	'field_hint_url_file' => 'Ссылка на архив',
	'field_hint_text' => 'Описание версии',
	'field_hint_file' => 'До 50 мегабайт',
	
	// Confirms
	'confirm_delete' => 'Вы действительно хотите удалить эту версию?',
	'confirm_delete_name' => 'Вы действительно хотите удалить версию - <strong>«{title}»</strong>?',
	
	// Tooltips
	'tooltip_user' => 'Перейти к пользователю',
	
	// Buttons
	'button_provider_change' => 'Сменить провайдер',
	'button_repository_change' => 'Сменить репозиторий',
	'button_release_change' => 'Сменить версию',
	
	// Hints
	'hint_create_new_repository' => 'Или создайте новый репозиторий перейдя по ссылке <a href="{url}" target="_blank">{url}</a>',
	'hint_create_new_release' => 'Или добавьте новую версию перейдя по ссылке <a href="{url}" target="_blank">{url}</a>',
	
	// Editable
	
	// Messages
	'message_create_success' => 'Версия была успешно создана',
	'message_update_success' => 'Версия была успешно обновлена',
	'message_delete_success' => 'Версия была успешно удалена',
	
	// Errors
	'error_not_exists' => 'Версия не найдена или была удалена',
	'error_moderated' => 'Версия находится на модерации и ее редактировать нельзя',
	'error_url' => 'Проверьте пожалуйста ссылку',
	'error_version_exists' => 'Вы уже добавляли эту версию',
	'error_empty_repositories' => 'У вас нет репозиториев. Создайте пожалуйста репозиторий, залейте плагин и обновите эту страницу.<br>Создать репозиторий можете перейдя по ссылке <a href="{url}" target="_blank">{url}</a>',
	'error_empty_releases' => 'У вас нет версий. Создайте пожалуйста версию и обновите эту страницу.<br>Добавить версию вы можете перейдя по ссылке <a href="{url}" target="_blank">{url}</a>',
	'error_file_size' => 'Максимальный размер архива {size}',
];